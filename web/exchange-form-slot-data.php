<?php
ini_set('display_errors', true);

use lib\Container;
use modules\ExchangeForm;

require '../lib/iModule.php';
require '../modules/ExchangeForm.php';
require __DIR__ . '/../lib/Container.php';


// if (empty($_GET['slot'])) {
//     header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
//     die('Slot not found');
// } 

$ex = Container::createExchangeForm();

var_dump($ex->pingExchangeForm());
die();

$slots = Container::createSlots();

$slot = $slots->showSlot($_GET['slot']);

if (empty($slot)) {
    header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
    die('Slot not found');
}

switch ($slot['status']) {
    case 'generated':
        echo json_encode($slot);
    break;
    case 'payed':
        echo json_encode($slot);
    break;
    case 'activated':
        echo json_encode($slot);
    break;
    case 'done':
        echo json_encode($slot);
    break;
    case 'error':
        header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
        echo json_encode($slot);
    break;
    default:
        header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
        die('Unknown status ' . $slot['status']);
    break;
}

