<?php
namespace wallets;

require_once __DIR__ . '/../lib/Ness.php';

use lib\IWallet;
use lib\Ness as Privateness;

class Ness implements IWallet {

    private $min_sum = 0.1;
    private $ness;

    public function __construct()
    {
        $config = require __DIR__ . '/../config/config.php';
        $ness = $config['ness'];
        $this->ness = new Privateness($ness['host'], (int) $ness['port'], $ness['wallet_id'], $ness['password'], $ness['prefix']);
    }

    public function getMinSum(int $daysx100): float
    {
        return $this->min_sum * $daysx100;
    }

    public function setMinSum(float $sum)
    {
        $this->min_sum = $sum;
    }

    public function getWalletName(): string
    {
        return 'NESS';
    }

    public function getWalletDescription(): string
    {
        return 'Privateness';
    }

    public function generateAddress(string $wallet = '')
    {
        return $this->ness->createAddr();
    }

    public function getRecievedByAddress(string $addr)
    {
        return $this->ness->getAddress($addr)['confirmed']['coins'];
    }

    public function checkRecievedByAddress(string $addr)
    {
        return $this->min_sum <= $this->getRecievedByAddress($addr);
    }
}
