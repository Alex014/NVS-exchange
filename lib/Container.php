<?php
namespace lib;

require_once __DIR__ . '/../lib/Slots.php';
require_once __DIR__ . '/../lib/DB.php';
require_once __DIR__ . '/../lib/Emercoin.php';
require_once __DIR__ . '/../wallets/Emercoin.php';
require_once __DIR__ . '/../wallets/Ness.php';

ini_set('display_errors', true);

use lib\Stots;
use lib\DB;
use lib\Emercoin;
use wallets\Emercoin as EMC;
use wallets\Ness;

class Container {
    public static function createSlots() {
        $config = require __DIR__ . '/../config/config.php';
        
        Emercoin::$address = $config['emercoin']['host'];
        Emercoin::$port = $config['emercoin']['port'];
        Emercoin::$username = $config['emercoin']['user'];
        Emercoin::$password = $config['emercoin']['password'];

        $fdb = $config['db'];
        
        $db = new DB($fdb['host'], $fdb['database'], $fdb['user'], $fdb['password']);
        
        $Emercoin = new EMC();
        $Emercoin->setMinSum(0.01);
        $Ness = new Ness();
        $Ness->setMinSum(0.1);
        
        return new Stots($db, $Emercoin, $Ness);
    }
}
