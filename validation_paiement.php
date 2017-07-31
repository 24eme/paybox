<?php

include 'define.php';

require_once __DIR__ . '/api/persistence/objets/utils.php';
require_once __DIR__ . '/api/persistence/factories/factPayement.class.php';
require_once __DIR__ . '/api/persistence/factories/factParametre.class.php';
require_once __DIR__ . '/api/persistence/factories/factProduits.class.php';
require_once __DIR__ . '/api/persistence/factories/factAchat.class.php';
require_once __DIR__ . '/api/persistence/factories/factClient.class.php';

$GET = filter_input_array(INPUT_GET, FILTER_DEFAULT);

utils::log(LOG_FILE, '--------Nouveau entrée--------');

if (DEBUG) {
	utils::debug($GET);
}

if ($GET['Auto'] === NULL) {
	utils::log(LOG_FILE, 'REF: ' . $GET['Ref'] . ' | N° Autorisation nul. Paiement refuse.');
}

/**
* On extrait la signature de la requete
* https://davidwalsh.name/php-remove-variable
*/
$query = http_build_query($GET);

$query = preg_replace('/(.*)(?|&)' . 'Sign' . '=[^&]+?(&)(.*)/i', '$1$2$4', $query . '&');
$query = substr($query, 0, -1);

if (DEBUG) {
	utils::debug($query);
}
//--------------

/**
* On vérifie la signature
*/
if ($GET['Sign'] === NULL || !utils::verify_sign($query, base64_decode($GET['Sign']))) {
	utils::log(LOG_FILE, 'REF: ' . $GET['Ref'] . ' | Signature non valide : ' . $GET['Sign']);
	exit;
}

// On récupère les informations du paiement engagé
$refPaiement = factPayement::getPayementByReference($GET['Ref']);
if (!is_object($refPaiement)) {
	utils::log(LOG_FILE, 'REF: ' . $GET['Ref'] . ' n\'est pas une référence valide');
	exit;
}

$message_retour = utils::code_retour($GET['Reponse']);

// On récupère les informations liés au paiement engagé
$achat = factAchat::getAchatByPayement($refPaiement->getKey());
$client = $achat->getClient();
$produit = $achat->getProduit();

/**
 * Désactivation en attendant de savoir si l'IPN est appelée plusieurs fois ou non
 *
if ($produit->getMontantEnCentime() != $GET['Mt']) {
	utils::log(LOG_FILE, 'REF: ' . $GET['Ref'] . ' | Le montant n\'est pas bon');
	exit;
}
 */

$data = array(
	'user' => $client->getPrenom() . ' ' . $client->getNom(),
	'reference' => $refPaiement->getReference(),
	'produit' => $produit->getLibelle(),
	'montant' => $GET['Mt'] / 100,
	'autorisation' => $GET['Auto'],
	'message_retour' => $message_retour,
);

switch ($GET['Reponse']) {
	case '00000':
		$status = 1;
		$template = VIEW . '/mail/succes.phtml';
		$data['title'] = 'Paiement effectué';
		break;
	case '99999':
		$status = 2;
		$template = VIEW . '/mail/attente.phtml';
		$data['title'] = 'Paiement en attente';
		break;
	case (preg_match('/^001[0-9]{2}$/', $GET['Reponse']) ? true : false):
		$status = 3;
		$template = VIEW . '/mail/refuse.phtml';
		$data['title'] = 'Paiement refusé';
		break;
	case (preg_match('/^000[0-9]{2}$/', $GET['Reponse']) ? true : false):
		$status = 4;
		$template = VIEW . '/mail/refuse.phtml';
		$data['title'] = 'Paiement refusé';
		break;
	default :
		$status = 5;
		$message_retour = 'Message inconnu de la part de paybox';
		$template = VIEW . '/mail/refuse.phtml';
		$data['title'] = 'Réponse incorrect de paybox';
		break;
}

try {
    // Nouveau paiement
	$y = factPayement::getNewPayement();
	$y->setDate();
	$y->setPStatus($status);
	$y->setReference($GET['Ref']);
	$y->setMontant($data['montant']);
	$y->setTypePaiement($refPaiement->getTypePaiement());
	factPayement::writePayement($y);

	// Nouvel achat lié au nouveau paiement
	$a = factAchat::getNewAchat();
	$a->setClientPk($client->getKey());
	$a->setPayementPk($y->getKey());
	$a->setProduitPk($produit->getKey());
	factAchat::writeAchat($a);

	utils::log(LOG_FILE, 'REF: ' . $GET['Ref'] . ' ' . $message_retour);


	utils::mail(
		$client->getEmail(),
		$template,
		$data
	);
} catch (Exception $e) {
	utils::log(LOG_FILE, 'REF: ' . $GET['Ref'] . ' REPONSE: ' . $GET['Reponse'] . ' ' . $message_retour . ' | ' . $e->getMessage());
}
