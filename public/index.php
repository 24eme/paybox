<?php

use App\Factory\Produit;
use App\Utils;

require '../app/autoload.php';

require '../define.php';

if (!session_start()) {
    Utils::display_error_page('La session n\'a pas démarré !');
}

$args = array(
    'produit' => FILTER_VALIDATE_INT,
    'persId' => FILTER_SANITIZE_NUMBER_INT,
    'nom' => FILTER_SANITIZE_STRING,
    'prenom' => FILTER_SANITIZE_STRING,
    'email' => FILTER_SANITIZE_EMAIL
);

$GET = filter_input_array(INPUT_GET, $args, true);

if ($GET === null || $GET['persId'] === null) {
    $GET['persId'] = uniqid();
}

if ($GET === null || isset($GET['produit']) === false || !$GET['produit']) {
    Utils::display_error_page('Paramètre requis manquant.');
}

$produit = Produit::getProduitByPk($GET['produit']);

if (!is_object($produit) || !$produit->isOpen()) {
    Utils::display_error_page(
        'Produit indisponible',
        'ID produit: ' . $GET['produit']
    );
}

$_SESSION['produit'] = $produit->getKey();

include VIEW . '/index.phtml';
