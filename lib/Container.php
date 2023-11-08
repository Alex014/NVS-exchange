<?php
namespace lib;

require_once __DIR__ . '/../decoders/ExchangeFormDecoder.php';
require_once __DIR__ . '/../decoders/ExchangeFormTokenDecoder.php';
require_once __DIR__ . '/../decoders/StandardDecoder.php';
require_once __DIR__ . '/../lib/Slots.php';
// require_once __DIR__ . '/../lib/DB.php';
require_once __DIR__ . '/../lib/Sqlite.php';
require_once __DIR__ . '/../lib/Emercoin.php';
require_once __DIR__ . '/../wallets/Emercoin.php';
require_once __DIR__ . '/../wallets/NessGen.php';
require_once __DIR__ . '/../wallets/NchGen.php';

use decoders\ExchangeFormDecoder;
use decoders\ExchangeFormTokenDecoder;
use decoders\StandardDecoder;
use lib\Stots;
// use lib\DB;
use lib\Sqlite;
use lib\Emercoin;
use modules\BaseModule;
use modules\ExchangeForm;
use wallets\Emercoin as EMC;
use wallets\Ness;
use wallets\NCH;
use wallets\NchGen;
use wallets\NessGen;

class Container {
    public static function createSlots(): Slots 
    {
        $config = require __DIR__ . '/../config/config.php';
        
        Emercoin::$address = $config['emercoin']['host'];
        Emercoin::$port = $config['emercoin']['port'];
        Emercoin::$username = $config['emercoin']['user'];
        Emercoin::$password = $config['emercoin']['password'];

        $fdb = $config['db'];
        
        // $db = new DB($fdb['host'], $fdb['database'], $fdb['user'], $fdb['password']);
        $db = new Sqlite($fdb['filename']);
        
        $Emercoin = new EMC();
        $Emercoin->setMinSum($config['exchange']['min_sum']['emc']);

        if (true === $config['ness']['gen_address']) {
            $Ness = new Ness();
            $Ness->setMinSum($config['exchange']['min_sum']['ness']);
            $NCH = new NCH();
            $NCH->setMinSum($config['exchange']['min_sum']['nch']);
        } else {
            $Ness = new NessGen();
            $Ness->setMinSum($config['exchange']['min_sum']['ness']);
            $NCH = new NchGen();
            $NCH->setMinSum($config['exchange']['min_sum']['nch']);
        }
        
        return new Slots($db, $Emercoin, $Ness, $NCH);
    }

    public static function createEmcWallet(): EMC 
    {
        $config = require __DIR__ . '/../config/config.php';
        
        Emercoin::$address = $config['emercoin']['host'];
        Emercoin::$port = $config['emercoin']['port'];
        Emercoin::$username = $config['emercoin']['user'];
        Emercoin::$password = $config['emercoin']['password'];
        return new EMC();
    }

    public static function createStandardModule(): BaseModule 
    {
        $decoder = new StandardDecoder();
        return new BaseModule(self::createSlots(), $decoder);
    }

    public static function createExchangeForm(): ExchangeForm 
    {
        $exchange_form_decoder = new ExchangeFormDecoder();
        $exchange_form_token_decoder = new ExchangeFormTokenDecoder();
        return new ExchangeForm(self::createSlots(), self::createEmcWallet(), $exchange_form_decoder, $exchange_form_token_decoder);
    }
}
