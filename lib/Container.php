<?php
namespace lib;

require_once __DIR__ . '/../lib/Slots.php';
require_once __DIR__ . '/../lib/DB.php';
require_once __DIR__ . '/../lib/Emercoin.php';
require_once __DIR__ . '/../wallets/Emercoin.php';
require_once __DIR__ . '/../wallets/Ness.php';
require_once __DIR__ . '/../wallets/NCH.php';

ini_set('display_errors', true);

use lib\Stots;
use lib\DB;
use lib\Emercoin;
use wallets\Emercoin as EMC;
use wallets\NCH;
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
        $Emercoin->setMinSum($config['exchange']['min_sum']['emc']);
        $Ness = new Ness();
        $Ness->setMinSum($config['exchange']['min_sum']['ness']);
        $NCH = new NCH();
        $Ness->setMinSum($config['exchange']['min_sum']['nch']);
        
        return new Stots($db, $Emercoin, $Ness, $NCH);
    }
}
