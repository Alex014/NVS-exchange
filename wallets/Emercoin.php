<?php
namespace wallets;

require_once __DIR__ . '/../lib/Emercoin.php';

use lib\IWallet;
use lib\Emercoin as Emc;

class Emercoin implements IWallet {

    private float $min_sum = 0.01;

    public function __construct()
    {
        $config = require __DIR__ . '/../config/config.php';
        $femc = $config['emercoin'];

        Emc::$address = $femc['host'];
        Emc::$port = $femc['port'];
        Emc::$username = $femc['user'];
        Emc::$password = $femc['password'];
    }

    public function getMinSum(): float
    {
        return $this->min_sum;
    }

    public function setMinSum(float $sum)
    {
        $this->min_sum = $sum;
    }

    public function getWalletName(): string
    {
        return 'EMC';
    }

    public function getWalletDescription(): string
    {
        return 'Emercoin';
    }

    public function generateAddress()
    {
        return (string) Emc::createNewAddress('NVS-Exchange');
    }

    public function getRecievedByAddress(string $addr)
    {
        return Emercoin::getRecievedByAddress($addr);
    }

    public function checkRecievedByAddress(string $addr)
    {
        return $this->min_sum <= $this->getRecievedByAddress($addr);
    }
}