<?php
require_once('api/persistence/factories/factClient.class.php');
require_once('api/persistence/factories/factPayement.class.php');
require_once('api/persistence/factories/factAchat.class.php');
require_once('api/action/actionPBX.class.php');


$y = factPayement::getPayementByReference($_POST["refPayement"]);
$c = factClient::getClientByReference($_POST["refClient"]);
$a = factAchat::getAchatByPayement($y->getKey());

echo actionPBX::send($c, $y,$a);
