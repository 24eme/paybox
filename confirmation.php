<?php
require_once('api/persistence/factories/factProduits.class.php');
require_once('api/persistence/factories/factClient.class.php');
require_once('api/persistence/factories/factPayement.class.php');
require_once('api/persistence/factories/factAchat.class.php');
require_once('api/persistence/factories/factParametre.class.php');
require_once('api/persistence/objets/utils.php');

require_once('api/action/paybox.class.php');

if (!session_start()) {
    utils::display_error_page('La session n\'a pas démarré !');
}

$args = array(
    'nom' => FILTER_SANITIZE_STRING,
    'prenom' => FILTER_SANITIZE_STRING,
    'email' => FILTER_SANITIZE_EMAIL,
    'persId' => FILTER_SANITIZE_STRING,
    'paiement' => FILTER_VALIDATE_INT
);

$POST = filter_input_array(INPUT_POST, $args);

$produit_id = (isset($_SESSION['produit'])) ? $_SESSION['produit'] : false;
unset($_SESSION['produit']);

if ($produit_id === false) {
    $lsComplement = 'Mauvais id produit : ' . $produit_id;
    utils::display_error_page('Erreur Interne <br> Veuillez contacter la DSI', $lsComplement);
}

if (!isset($POST['nom'], $POST['prenom'], $POST['email'], $POST['persId'], $POST['paiement'])) {
    $lsComplement = 'Un parametre est manquant !!!' . PHP_EOL
        . 'Array dump: ' . print_r($POST, true) . PHP_EOL;
    utils::display_error_page('Erreur Interne <br> Veuillez contacter la DSI', $lsComplement);
}

if (empty($POST['nom']) || empty($POST['prenom']) || empty($POST['email']) || empty($POST['persId']) || empty($POST['paiement'])) {
    $lsComplement = 'Un parametre est vide !!!' . PHP_EOL
        . 'Array dump: ' . print_r($POST, true) . PHP_EOL;
    utils::display_error_page('Erreur Interne <br> Veuillez contacter la DSI', $lsComplement);
}

$p = factProduits::getProduitByPk($produit_id);

if (!is_object($p)) {
    $lsComplement = 'Produit inconnu => Pk Produit = ' . $produit_id;
    utils::display_error_page('Erreur Interne <br> veuillez Contacter la DSI', $lsComplement);
}

if (!$p->isOpen()) {
    utils::display_error_page('Le produit que vous voulez est indisponible.');
}

// Si la variable paiement incorrecte -> paiement en 1x par défaut
if ($POST['paiement'] === false || $POST['paiement'] < 1 || $POST['paiement'] > $p->getTypePaiement()) {
    $POST['paiement'] = 1;
}

// Avant toute opération, on vérifie que les serveurs de Paybox sont
// disponible !
$paybox = new Paybox();
$paybox->setUrl(factParametre::getParametreByCode("PBX_PAYBOX")->getValue());

if (! $paybox->check()) {
    utils::display_error_page('Les serveurs de Paybox ne sont pas disponible
        pour le moment. Merci de réessayer ultérieurement.');
}

//on instancie le client
$paramRefSepar = factParametre::getParametreByCode("REF_SEPA");
$refClient = implode($paramRefSepar->getValue(), [
    $p->getSalt(),
    $POST['persId']
]);
$c = factClient::getClientByReference($refClient);

if (is_null($c)) {
    $c = factClient::getNewClient();
    $c->setNom($POST['nom']);
    $c->setPrenom($POST['prenom']);
    $c->setIdentifiant($refClient);
    $c->setEmail($POST['email']);
    factClient::writeClient($c);
    $c = factClient::getClientByReference($refClient);
}

// On instancie la référence du paiement
$random = uniqid(date('Ymd'));
$refPayement = implode($paramRefSepar->getValue(), [
    $c->getIdentifiant(),
    $random,
    $produit_id
]);

// A chaque nouvelle commande, on instancie un nouveau paiement
$y = factPayement::getNewPayement();
$y->setDate();
$y->setPStatus(0);
$y->setReference($refPayement);
$y->setMontant(0);
// Paiement en 1x ou 3x
$y->setTypePaiement((int)$POST['paiement']);
$idNewPayement = factPayement::writePayement($y);

$a = factAchat::getNewAchat();
$a->setClientPk($c->getKey());
$a->setPayementPk($idNewPayement);
$a->setProduitPk($produit_id);
factAchat::writeAchat($a);

// On renseigne les informations qui seront envoyés à Paybox
$paybox->add('PBX_SITE', factParametre::getParametreByCode("PBX_SITE")->getValue());
$paybox->add('PBX_RANG', factParametre::getParametreByCode("PBX_RANG")->getValue());
$paybox->add('PBX_IDENTIFIANT', factParametre::getParametreByCode("PBX_IDENTIFIANT")->getValue());
$paybox->add('PBX_DEVISE', factParametre::getParametreByCode("PBX_DEVISE")->getValue());
$paybox->add('PBX_REPONDRE_A', factParametre::getParametreByCode("PBX_REPONDRE_A")->getValue());
$paybox->add('PBX_MODE', factParametre::getParametreByCode("PBX_MODE")->getValue());
$paybox->add('PBX_CMD', $y->getReference());
$paybox->add('PBX_PORTEUR', $c->getEmail());
$paybox->add('PBX_RETOUR', factParametre::getParametreByCode("PBX_RETOUR")->getValue());
$paybox->add('PBX_EFFECTUE', factParametre::getParametreByCode("PBX_EFFECTUE")->getValue());
$paybox->add('PBX_REFUSE', factParametre::getParametreByCode("PBX_REFUSE")->getValue());
$paybox->add('PBX_ANNULE', factParametre::getParametreByCode("PBX_ANNULE")->getValue());
$paybox->add('PBX_HASH', factParametre::getParametreByCode("PBX_HASH")->getValue());
$paybox->add('PBX_TIME', date('c'));

// Différentes informations si le paiement est en plusieurs fois
if ($y->getTypePaiement() === $paybox::UNEFOIS) {
    $paybox->add('PBX_TOTAL', sprintf("%03u", $p->getMontantEnCentime()));
} else {
    $today = new DateTime();

    $unMois = clone $today;
    $unMois->add(new DateInterval('P1M')); // +1 mois

    $deuxMois = clone $today;
    $deuxMois->add(new DateInterval('P2M')); // +2 mois

    // Retourne la valeur entière de la division
    $tier = (int)($p->getMontantEnCentime() / 3);
    $modulo = $p->getMontantEnCentime() % 3;

    // Montant initial
    $paybox->add('PBX_TOTAL', sprintf("%03u", $tier));

    // 1er prélèvement
    $paybox->add('PBX_DATE1', $unMois->format('d/m/Y'));
    $paybox->add('PBX_2MONT1', sprintf("%03u", $tier));

    // 2eme prélèvement
    $paybox->add('PBX_DATE2', $deuxMois->format('d/m/Y'));
    $paybox->add('PBX_2MONT2', sprintf("%03u", ($tier + $modulo)));
}

// On génère le formulaire
$formhidden = $paybox->formulaire();

// On récupère l'url à appeler dans le formulaire
$formurl = $paybox->getUrl();

// On crée la chaîne à hacher sans URLencodage
$msg = $paybox->message();

// On récupère la clé secrète HMAC (stockée dans une base de données par exemple) et que l'on renseigne dans la variable $keyTest;
// Si la clé est en ASCII, On la transforme en binaire
$paramPrivKey = factParametre::getParametreByCode("PBX_PRIV_KEY");
$binKey = pack("H*", $paramPrivKey->getValue());

// On calcule l'empreinte (à renseigner dans le paramètre PBX_HMAC) grâce à la fonction hash_hmac et
// la clé binaire
// On envoie via la variable PBX_HASH l'algorithme de hachage qui a été utilisé (SHA512 dans ce cas)
$paramHash = factParametre::getParametreByCode("PBX_HASH");
$hmac = strtoupper(hash_hmac($paramHash->getValue(), $msg, $binKey));
// La chaîne sera envoyée en majuscules, d'où l'utilisation de strtoupper()
// On crée le formulaire à envoyer à PayboxSystem
// ATTENTION : l'ordre des champs est extrêmement important, il doit
// correspondre exactement à l'ordre des champs dans la chaîne hachée

include VIEW . '/confirm.phtml';
