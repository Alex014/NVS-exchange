<?php
namespace lib;
require_once __DIR__ . '/Emercoin.php';
require_once __DIR__ . '/IWallet.php';
require_once __DIR__ . '/iSlotDatabase.php';

use \lib\iSlotDatabase;
use \lib\Emercoin;
use \lib\IWallet;

class Stots {

    private $db;
    private $wallets;
    public $min_sum = 0.01;
    public $days = 100;

    public function __construct(iSlotDatabase $db, IWallet ...$wallets)
    {
        $this->db = $db;
        $this->wallets = $wallets;
    }

    public function createSlot(string $key, string $value): string
    {
        $slot_id = md5(rand(10000, 99999) . time());
        
        $addr = [];

        foreach ($this->wallets as $wallet) {
            $addr[$wallet->getWalletName()] = [
                'addr' => $wallet->generateAddress(),
                'descr' => $wallet->getWalletDescription(),
                'min_sum' => $wallet->getMinSum()
            ];
        }

        $addr = json_encode($addr);

        $this->db->createSlot($key, $value, $addr, $slot_id);

        return $slot_id;
    }

    public function showSlot(string $slot_id)
    {
        $slot = $this->db->getSlot($slot_id);

        if (empty($slot)) {
            return false;
        }

        $slot['addr'] = json_decode($slot['addr'], true);

        return $slot;
    }

    public function findSlot(string $name)
    {
        return $this->db->findSlot($name);
    }

    public function locateSlot(string $name)
    {
        try {
            return Emercoin::name_show($name);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function lastSlotTime()
    {
        $last = $this->db->getLastSlot();
        if (!empty($last)) {
            return $last['created'];
        } else {
            return 0;
        }
    }

    public function deleteSlot(string $slot_id)
    {
        $this->db->deleteSlot($slot_id);
    }

    public function processSlot(string $slot_id)
    {
        $slot = $this->db->getSlot($slot_id);

        if (empty($slot)) {
            return false;
        }

        $addrs = json_decode($slot['addr'], true);

        if (empty($addrs)) {
            return false;
        }

        foreach ($this->wallets as $wallet) {
            $wname = $wallet->getWalletName();
            if (isset($addrs[$wname]) && $wallet->checkRecievedByAddress($addrs[$wname]['addr'])) {
                Emercoin::name_new($slot['name'], $slot['value'], $this->days);
                $this->db->setSlotPayed($slot_id);
                return true;
            }
        }

        return false;
    }

    public function processSlots()
    {
        foreach($this->db->listUnpayedSlots() as $slot) {
            $this->processSlot($slot['slot_id']);
        }
    }
}