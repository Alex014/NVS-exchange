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

if ($argc == 1) {
    $addr_count = 1;
    $wallet = $ness['main_wallet_id'];
} elseif ($argc == 2) {
    $addr_count = 1;
    $wallet = $argv[1];
} elseif ($argc == 3) {
    $addr_count = (int)$argv[2];
    $wallet = $argv[1];
}

$Ness = new Ness($ness['host'], (int) $ness['port'], $ness['wallets'], $wallet, $ness['prefix']);

if (false !== $Ness->health()) {
    for ($i = 0; $i < $addr_count; $i++) {
        // $res = $Ness->createAddrDebug();
        echo $Ness->createAddr();
        echo "\n";
    }
} else {
    echo "No connection.\n";
}

if ($argc == 1) {
    echo "\n\nUSAGE: \n";
    echo "php new-addr.php [wallet-filename] [new addresses count]\n";
}