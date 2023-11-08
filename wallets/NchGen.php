<?php
namespace wallets;

require_once __DIR__ . '/../lib/Ness.php';

use lib\IWallet;
use lib\Ness as Privateness;

class NchGen implements IWallet {

    private $min_sum = 1;
    private $ness;

    public function __construct()
    {
        $config = require __DIR__ . '/../config/config.php';
        $ness = $config['ness'];
        $this->ness = new Privateness($ness['host'], (int) $ness['port'], $ness['wallets'], $ness['main_wallet_id'], $ness['prefix']);
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
        return 'NCH';
    }

    public function getWalletDescription(): string
    {
        return 'Privateness Coin-Hours';
    }

    public function generateAddress()
    {
        return $this->ness->findEmptyAddress();
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
