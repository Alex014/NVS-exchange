<?php
namespace wallets;

require_once __DIR__ . '/../lib/Ness.php';

use lib\IWallet;
use lib\Ness as Privateness;

class NCH implements IWallet {

    private float $min_sum = 10;
    private $ness;

    public function __construct()
    {
        $config = require_once __DIR__ . '/../config/config.php';
        $ness = $config['ness'];
        $this->ness = new Privateness($ness['host'], (int) $ness['port'], $ness['wallet_id'], $ness['password'], $ness['prefix']);
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
        return 'NCH';
    }

    public function getWalletDescription(): string
    {
        return 'Privateness Coin-Hours';
    }

    public function generateAddress()
    {
        return $this->ness->createAddr();
    }

    public function getRecievedByAddress(string $addr)
    {
        return $this->ness->getAddress($addr)['confirmed']['hours'];
    }

    public function checkRecievedByAddress(string $addr)
    {
        return $this->min_sum <= $this->getRecievedByAddress($addr);
    }
}
