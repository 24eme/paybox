<?php
require_once('api/persistence/factories/factProduits.class.php');
require_once('api/persistence/factories/factClient.class.php');
require_once('api/persistence/factories/factPayement.class.php');
require_once('api/persistence/factories/factAchat.class.php');
require_once('api/persistence/factories/factParametre.class.php');
require_once('api/persistence/objets/utils.php');

if (!session_start()) {
	utils::display_error_page('La session n\'a pas démarré !');
}

$args = array(
    'nom'=>FILTER_SANITIZE_STRING,
    'prenom'=>FILTER_SANITIZE_STRING,
	'email' => FILTER_SANITIZE_EMAIL,
	'lang' => FILTER_SANITIZE_STRING
);

$_POST = filter_input_array(INPUT_POST, $args);

$produit_id = (isset($_SESSION['produit'])) ? $_SESSION['produit'] : false;
unset($_SESSION['produit']);

$lang = $_POST['lang'] === null ? 'fr' : $_POST['lang'];

if (!in_array($lang, ['fr', 'en'])) {
	utils::display_error_page('Erreur Interne <br> veuillez Contacter la DSI', 'Unsupported language: ' . $lang);
}

if ($produit_id === false) {
	$lsComplement = 'Mauvais id produit : ' . $produit_id;
	utils::display_error_page('Erreur Interne <br> Veuillez contacter la DSI', $lsComplement);
}

if (!isset($_POST['nom'], $_POST['prenom'], $_POST['email'])) {
   $lsComplement = 'Un parametre est manquant !!!'.PHP_EOL
	   . 'Array dump: ' . print_r($_POST, true) . PHP_EOL;
   utils::display_error_page('Erreur Interne <br> Veuillez contacter la DSI', $lsComplement);
}

if (empty($_POST['nom']) || empty($_POST['prenom']) || empty($_POST['email'])) {
   $lsComplement = 'Un parametre est vide !!!'.PHP_EOL
	   . 'Array dump: ' . print_r($_POST, true) . PHP_EOL;
   utils::display_error_page('Erreur Interne <br> Veuillez contacter la DSI', $lsComplement);
}

$p = factProduits::getProduitByPk($produit_id);

if(!is_object($p)){
	$lsComplement = 'Produit inconnu => Pk Produit = ' . $produit_id;
   utils::display_error_page('Erreur Interne <br> veuillez Contacter la DSI', $lsComplement);
}

if(!$p->isOpen()) {
    utils::display_error_page('Le produit que vous voulez est indisponible.');
}

//on instancie le client
$paramRefSepar = factParametre::getParametreByCode("REF_SEPA");
$refClient = implode($paramRefSepar->getValue(), array('ALFORPRO', md5($_POST['email']))); // Dirty fix for ALFORPRO
$c = factClient::getClientByReference($refClient);

if (is_null($c)) {
	$c = factClient::getNewClient();
	$c->setNom($_POST['nom']);
	$c->setPrenom($_POST['prenom']);
	$c->setIdentifiant($refClient);
	$c->setEmail($_POST['email']);
	factClient::writeClient($c);
	$c = factClient::getClientByReference($refClient);
}
// On instancie la référence du paiement
$random =  uniqid(date('Ymd'));
$refPayement = implode($paramRefSepar->getValue(), array($c->getIdentifiant(),
	$random,
	$produit_id));

// On regarde si le paiement existe. On le crée s'il n'existe pas.
$y = factPayement::getPayementByReference($refPayement);
if (is_null($y)) {
	$y = factPayement::getNewPayement();
	$y->setDate();
	$y->setPStatus(0);
	$y->setReference($refPayement);
	$y->setMontant(0);
	factPayement::writePayement($y);
}

$a = factAchat::getNewAchat();
$a->setClientPk($c->getKey());
$a->setPayementPk($y->getKey());
$a->setProduitPk($produit_id);
factAchat::writeAchat($a);

/*
// On récupère la date au format ISO-8601
$dateTime = date('c');

// On crée la chaîne à hacher sans URLencodage
$msg = $paramSite->renderUrl() .
	$paramRang->renderUrl('&') .
	$paramIdentifiant->renderUrl('&') .
	"&PBX_TOTAL=" . $_POST['Montant'] .
	$paramDevise->renderUrl("&") .
	"&PBX_CMD=" . $refPayement .
	"&PBX_PORTEUR=" . $_POST['email'] .
	$paramRepondreA->renderUrl('&') .
	"&PBX_RETOUR=Mt:M;Ref:R;Auto:A;Erreur:E" .
	$paramHash->renderUrl('&') .
	"&PBX_TIME=" . $dateTime;
// On récupère la clé secrète HMAC (stockée dans une base de données par exemple)et que lon renseigne dans la variable $keyTest;
// Si la clé est en ASCII, On la transforme en binaire
$paramRivKey = factParametre::getParametreByCode("PBX_PRIV_KEY");
$binKey = pack("H*", $paramRivKey->getValue());
// On calcule lempreinte(à renseigner dans le paramètre PBX_HMAC)grâce à la fonction hash_hmac et
// la clé binaire
// On envoie via la variable PBX_HASH l'algorithme de hachage qui a été utilisé(SHA512 dans ce cas)
// Pour afficher la liste des algorithmes disponibles sur votre environnement, décommentez la ligne
// suivante
//print_r(hash_algos());
$hmac = strtoupper(hash_hmac($paramHash->getValue(), $msg, $binKey));
// La chaîne sera envoyée en majuscules, d'où l'utilisation de strtoupper()
// On crée le formulaire à envoyer à PayboxSystem
// ATTENTION : l'ordre des champs est extrêmement important, il doit
// correspondre exactement à l'ordre des champs dans la chaîne hachée
*/
include VIEW . '/confirm.' . $lang . '.phtml';
