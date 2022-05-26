<?php
require __DIR__ . '/../lib/Slots.php';
require __DIR__ . '/../lib/DB.php';

ini_set('display_errors', true);

use lib\Stots;
use lib\DB;

$config = require __DIR__ . '/../config/config.php';
$fdb = $config['db'];

$db = new DB($fdb['host'], $fdb['database'], $fdb['user'], $fdb['password']);
$slots = new Stots($db, $config['exchange']['min_sum']);

$slots->processSlots();

echo "OK\n";