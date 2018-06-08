<?php

use App\Factory\Produits;
use App\Utils;

require '../app/autoload.php';

require '../define.php';
//require BASE . '/api/persistence/objets/utils.php';
//require BASE . '/api/persistence/factories/factProduits.class.php';

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

if ($GET['persId'] === null) {
    $GET['persId'] = uniqid();
}

if (!$GET['produit']) {
    Utils::display_error_page('Paramètre requis manquant.');
}

$produit = Produits::getProduitByPk($GET['produit']);

if (!is_object($produit) || !$produit->isOpen()) {
    Utils::display_error_page(
        'Produit indisponible',
        'ID produit: ' . $GET['produit']
    );
}

$_SESSION['produit'] = $produit->getKey();

include VIEW . '/index.phtml';
