<?php

require __DIR__ . '/../lib/Ness.php';

use lib\Ness;

ini_set('display_errors', true);
error_reporting(E_ERROR);

if (!file_exists(__DIR__ . '/../config/config.php')) {
    die ('config.php file does not exist');
}

$config = require __DIR__ . '/../config/config.php';

$ness = $config['ness'];
$Ness = new Ness($ness['host'], (int) $ness['port'], $ness['wallet_id'], $ness['password'], $ness['prefix']);

if (false !== $Ness->health()) {
    $res = $Ness->createAddrDebug();
    print_r($res);
} else {
    echo "No connection.\n";
}