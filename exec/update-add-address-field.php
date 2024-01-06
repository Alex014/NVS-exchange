<?php
require_once __DIR__ . '/../lib/Sqlite.php';

use lib\Sqlite;

$config = require __DIR__ . '/../config/config.php';

$fdb = $config['db'];

// $db = new DB($fdb['host'], $fdb['database'], $fdb['user'], $fdb['password']);
$db = new Sqlite($fdb['filename']);

$sql = <<<SQL
ALTER TABLE `slots` ADD column `address` TEXT AFTER `value`
SQL;

$db->execSql($sql);