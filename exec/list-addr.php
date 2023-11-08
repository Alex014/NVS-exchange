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
}

$Ness = new Ness($ness['host'], (int) $ness['port'], $ness['wallets'], $wallet, $ness['prefix']);

if (false !== $Ness->health()) {
    foreach ($Ness->listAddresses($wallet) as $addr => $sum) {
        echo "$addr Coins:" . $sum['confirmed']['coins'] . "  Hours:" . $sum['confirmed']['hours'] . "\n";
    }
} else {
    echo "No connection.\n";
}

echo "First empty address: " . $Ness->findEmptyAddress($wallet) . "\n";

if ($argc == 1) {
    echo "\nUSAGE: \n";
    echo "php list-addr.php [wallet-filename]\n";
}