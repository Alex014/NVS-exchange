<?php
require __DIR__ . '/../lib/DB.php';
require __DIR__ . '/../lib/Emercoin.php';

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

try {
    $db->listSlots();
} catch (\Exception $err) {
    echo "\nDB - FAILED (" . $err->getMessage() . ")\n";
}

if (!isset($err)) {
    echo "\nDB - OK\n";
}

try {
    Emercoin::getinfo();
    Emercoin::name_filter("worm:token:ness_exchange_v1_v2:.+");
} catch (\Exception $err) {
    echo "\nEmercoin NVS - FAILED (" . $err->getMessage() . ")\n";
}

if (!isset($err)) {
    echo "\nEmercoin NVS - OK\n";
}
