<?php

define('BASE', __DIR__);
define('VIEW', BASE.'/view');
define('LOG_FILE', BASE.'/log/paybox.log');
define('PUBKEY', BASE.'/cert/pbox_pubkey.pem');

define('DEBUG', true);

date_default_timezone_set('Europe/Paris');

define('FROM_EMAIL', 'From Email <from@email.com>');
