<?php

include 'define.php';

require_once __DIR__ . '/api/persistence/objets/utils.php';
require_once __DIR__ . '/api/persistence/factories/factPayement.class.php';
require_once __DIR__ . '/api/persistence/factories/factParametre.class.php';
require_once __DIR__ . '/api/persistence/factories/factProduits.class.php';
require_once __DIR__ . '/api/persistence/factories/factAchat.class.php';
require_once __DIR__ . '/api/persistence/factories/factClient.class.php';

$args = array(
		'Mt' => FILTER_DEFAULT,
		'Ref' => FILTER_DEFAULT,
		'Auto' => FILTER_DEFAULT,
		'Reponse' => FILTER_DEFAULT,
		'Sign' => FILTER_DEFAULT
	     );

$GET = filter_input_array(INPUT_GET, $args);

if($GET['Auto'] === NULL) {
	utils::log(LOG_FILE, 'REF: ' . $GET['Ref'] . ' | N° Autorisation nl. Paiement refuse.');
	$valeurs = "Mt=".$GET['Mt']."&Ref=".$GET['Ref']."&Reponse=".$GET['Reponse'];
}
else {
	$valeurs = "Mt=".$GET['Mt']."&Ref=".$GET['Ref']."&Auto=".$GET['Auto']."&Reponse=".$GET['Reponse'];
}

if($GET['Sign'] === NULL || !utils::verify_sign($valeurs, base64_decode($GET['Sign']))) {
	utils::log(LOG_FILE, 'REF: ' . $GET['Ref'] . ' | Signature non valide : ' . $GET['Sign']);
	exit;
}

$refPaiement = factPayement::getPayementByReference($GET['Ref']);
if (!is_object($refPaiement)) {
	utils::log(LOG_FILE, 'REF: ' . $GET['Ref'] . ' n\'est pas une référence valide');
	exit;
}

$message_retour = utils::code_retour($GET['Reponse']);

$achat = factAchat::getAchatByPayement($refPaiement->getKey());
$client = $achat->getClient();
$produit = $achat->getProduit();

if($produit->getMontantEnCentime() != $GET['Mt']) {
	utils::log(LOG_FILE, 'REF: ' . $GET['Ref'] . ' | Le montant n\'est pas bon');
	exit;
}


$data = array(
                'user' => $client->getPrenom() . ' ' . $client->getNom(),
                'reference' => $refPaiement->getReference(),
                'produit' => $produit->getLibelle(),
                'montant' => $GET['Mt']/100,
                'autorisation' => $GET['Auto'],
		'message_retour' => $message_retour,
);

switch ($GET['Reponse']) {
	case '00000':
		$status = 1;
		$template = VIEW.'/mail/succes.phtml';
		$data['title'] = 'Paiement effectué';
		break;
	case '99999':
		$status = 2;
		$template = VIEW.'/mail/attente.phtml';
		$data['title'] = 'Paiement en attente';
		break;
	case (preg_match('/^001[0-9]{2}$/', $GET['Reponse']) ? true : false):
		$status = 3;
		$template = VIEW.'/mail/refuse.phtml';
		$data['title'] = 'Paiement refusé';
		break;
	case (preg_match('/^000[0-9]{2}$/', $GET['Reponse']) ? true : false):
		$status = 4;
		$template = VIEW.'/mail/refuse.phtml';
		$data['title'] = 'Paiement refusé';
		break;
	default :
		$status = 5;
		$message_retour = 'Message inconnu de la part de paybox';
		$template = VIEW.'/mail/refuse.phtml';
		$data['title'] = 'Réponse incorrect de paybox';
		break;
}

try{
	$refPaiement->setPStatus($status);
	factPayement::writePayement($refPaiement);
	utils::log(LOG_FILE, 'REF: ' . $GET['Ref'] . ' ' . $message_retour);


	utils::mail(
		$client->getEmail(),
		$template,
		$data
	);
}
catch (Exception $e) {
	utils::log(LOG_FILE, 'REF: ' . $GET['Ref'] . ' REPONSE: ' . $GET['Reponse'] . ' ' . $message_retour.'|' . $e->getMessage());
}
