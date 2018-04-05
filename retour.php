<?php

include 'define.php';
include BASE.'/api/persistence/objets/utils.php';

$args = array(
    'action' => FILTER_DEFAULT,
    'Mt' => FILTER_DEFAULT,
    'Ref' => FILTER_DEFAULT,
    'Auto' => FILTER_DEFAULT,
    'Reponse' => FILTER_DEFAULT
);

$GET = filter_input_array(INPUT_GET, $args);
$data['reference'] = $GET['Ref'];
$data['message_retour'] = utils::code_retour($GET['Reponse']);

switch ($GET['action']) {
    case 'effectue':
        $page_retour = VIEW.'/urlretour/effectue.phtml';
        break;
    case 'refuse':
        $page_retour = VIEW.'/urlretour/refuse.phtml';
        break;
    case 'annule':
        $page_retour = VIEW.'/urlretour/annule.phtml';
        break;
    case 'attente':
        $page_retour = VIEW.'/urlretour/attente.phtml';
        break;
    default:
        utils::display_error_page('(╯°□°）╯︵ ┻━┻');
}

echo utils::render($page_retour, $data);
