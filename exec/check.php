<?php
require __DIR__ . '/../lib/Container.php';

ini_set('display_errors', true);

use lib\Container;

$slots = Container::createSlots();

$slots->processSlots();

echo "OK\n";