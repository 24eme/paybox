<?php

include 'define.php';
include __DIR__.'/api/persistence/objets/utils.php';
include __DIR__.'/api/persistence/factories/factProduits.class.php';

if (!session_start()) {
	utils::display_error_page('La session n\'a pas démarré !');
}


$args = array(
	'produit' => FILTER_VALIDATE_INT
);

$GET = filter_input_array(INPUT_GET, $args);

if(!$GET['produit']){
    utils::display_error_page('Paramètre requis manquant.');
}

$produit = factProduits::getProduitByPk($GET['produit']);

if(!is_object($produit) || !$produit->isOpen()) {
    utils::display_error_page('Produit indisponible',
                                'ID produit: '.$GET['produit']);
}

$_SESSION['produit'] = $produit->getKey();

include VIEW . '/index.external.phtml';
