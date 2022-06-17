<?php
require __DIR__ . '/../lib/DB.php';
require __DIR__ . '/../lib/Emercoin.php';
require __DIR__ . '/../lib/Ness.php';

use lib\DB;
use lib\Ness;
use lib\Emercoin;


ini_set('display_errors', true);
error_reporting(E_ERROR);

if (!file_exists(__DIR__ . '/../config/config.php')) {
    die ('config.php file does not exist');
}

$config = require __DIR__ . '/../config/config.php';

$db = new DB($config['db']['host'], $config['db']['database'], $config['db']['user'], $config['db']['password']);

Emercoin::$address = $config['emercoin']['host'];
Emercoin::$port = $config['emercoin']['port'];
Emercoin::$username = $config['emercoin']['user'];
Emercoin::$password = $config['emercoin']['password'];

$ness = $config['ness'];
$Ness = new Ness($ness['host'], (int) $ness['port'], $ness['wallets'], $ness['main_wallet_id'], $ness['prefix']);


try {
    $slots = $db->listSlots();
    if ($argc > 1 && '-debug' === $argv[1]) {
        print_r($slots);
    }

} catch (\Exception $err) {
    echo "\nDB - FAILED (" . $err->getMessage() . ")\n";
}

if (!isset($err)) {
    echo "\nDB - OK\n";
}

try {
    Emercoin::getinfo();
    if ($argc > 1 && '-debug' === $argv[1]) {
        foreach ($slots as $slot) {
            if ('PAYED' === $slot['status']) {
                $name = Emercoin::name_show($slot['name']);
                print_r($name);
            }
        }
    }
} catch (\Exception $err) {
    echo "\nEmercoin NVS - FAILED (" . $err->getMessage() . ")\n";
}

if (!isset($err)) {
    echo "\nEmercoin NVS - OK\n";
}

if ($Ness->health()) {
    echo "\nNESS - OK\n";

    foreach ($Ness->listWallets() as $wallet => $password) {
        $addresses = $Ness->listAddresses($wallet);

        echo $wallet . ": " . count($addresses) . " addresses\n";

        if ($argc > 1 && '-debug' === $argv[1]) {
            print_r($addresses);
        }
    }

    echo "First empty address: " . $Ness->findEmptyAddress() . "\n";

} else {
    echo "\nNESS - FAILED\n";
}

if ($argc == 1) {
    echo "\nUse 'php check.php -debug' for more info\n";
}