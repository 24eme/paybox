<?php

include 'define.php';
include __DIR__.'/api/persistence/objets/utils.php';

$view = 'student';

$args = array(
    'produit' => FILTER_VALIDATE_INT,
    'etudiant' => FILTER_VALIDATE_BOOLEAN,
    'nom' => FILTER_SANITIZE_STRING,
    'prenom' => FILTER_SANITIZE_STRING,
    'email' => FILTER_VALIDATE_EMAIL,
    'persId' => FILTER_VALIDATE_INT
);

$GET = filter_input_array(INPUT_GET, $args);

if(!$GET['produit']){
    utils::display_error_page('Paramètre requis manquant.');
}

if(!$GET['etudiant']){
    $view = 'external';
} else {
    if(!$GET['persId'] || !$GET['nom'] || !$GET['prenom'] || !$GET['email']) {
        utils::display_error_page('Paramètre requis manquant.');
    }
}

include VIEW.'/index.'.$view.'.phtml';
