<?php
namespace lib;
require_once __DIR__ . '/Emercoin.php';
require_once __DIR__ . '/IWallet.php';
require_once __DIR__ . '/iSlotDatabase.php';

use \lib\iSlotDatabase;
use \lib\Emercoin;
use \lib\IWallet;

class Slots {

    private $db;
    private $wallets;
    public $min_sum = 0.01;
    public $days = 10000;

    public function __construct(iSlotDatabase $db, IWallet ...$wallets)
    {
        $this->db = $db;
        $this->wallets = $wallets;
    }

    public function createSlot(string $key, string $value, string $address = '', $days = 100): string
    {
        $slot_id = md5(rand(10000, 99999) . time());
        $daysx100 = ceil($days / 100);
        
        $addr = [];
        
        foreach ($this->wallets as $wallet) {
            $genAddress = $wallet->generateAddress();
            
            if (false !== $genAddress) {
                $addr[$wallet->getWalletName()] = [
                    'addr' => $genAddress,
                    'descr' => $wallet->getWalletDescription(),
                    'min_sum' => $wallet->getMinSum($daysx100),
                    'days' => $days
                ];
            }
        }

        $addr = json_encode($addr);

        $this->db->createSlot($key, $value, $addr, $address, $slot_id);

        return $slot_id;
    }

    public function updateSlot(string $slot_id, string $key, string $value, string $address = '', $days = 100): string
    {
        $daysx100 = ceil($days / 100);
        $addr = [];

        foreach ($this->wallets as $wallet) {
            $genAddress = $wallet->generateAddress();

            if (false !== $genAddress) {
                $addr[$wallet->getWalletName()] = [
                    'addr' => $genAddress,
                    'descr' => $wallet->getWalletDescription(),
                    'min_sum' => $wallet->getMinSum($daysx100),
                    'days' => $days
                ];
            }
        }

        $addr = json_encode($addr);

        $this->db->updateSlot($key, $value, $addr, $address, $slot_id);

        return $slot_id;
    }

    public function regenerateSlot(string $slot_id)
    {
        $slot = $this->showSlot($slot_id);

        foreach ($this->wallets as $wallet) {
            $genAddress = $wallet->generateAddress();

            if (false !== $genAddress) {
                $addr[$wallet->getWalletName()] = [
                    'addr' => $genAddress,
                    'descr' => $wallet->getWalletDescription(),
                    'min_sum' => $wallet->getMinSum($slot['addr']['EMC']['days']),
                    'days' => $slot['addr']['EMC']['days']
                ];
            }
        }

        $addr = json_encode($addr);

        $this->db->updateSlot($slot['name'], $slot['value'], $addr, $slot['address'], $slot_id);
    }

    public function showSlot(string $slot_id)
    {
        $slot = $this->db->getSlot($slot_id);

        if (empty($slot)) {
            return false;
        }

        $slot['addr'] = json_decode($slot['addr'], true);

        if (Emercoin::name_list($slot['name'])) {
            $slot['nvs'] = Emercoin::name_show($slot['name']);
        }

        return $slot;
    }

    public function findSlot(string $name): array
    {
        $result = $this->db->findSlot($name);
        if (false === $result) {
            return [];
        } else {
            return $result;
        }
    }

    public function locateSlot(string $name): array
    {
        try {
            return Emercoin::name_show($name);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function isMyAddress(string $addr): bool
    {
        try {
            Emercoin::getRecievedByAddress($addr);
            return true;
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

        if ('GENERATED' === $slot['status']) {
            return $this->processUnpayedSlot($slot_id);
        } elseif ('UPDATED' === $slot['status']) {
            return $this->processUpdatedSlot($slot_id);
        }

        return false;
    }

    public function processUnpayedSlot(string $slot_id)
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
                if (!empty($slot['address'])) {
                    Emercoin::name_new($slot['name'], $slot['value'], $addrs[$wname]['days'], $slot['address']);
                } else {
                    Emercoin::name_new($slot['name'], $slot['value'], $addrs[$wname]['days']);
                }

                $this->db->setSlotPayed($slot_id);  
                return true;
            }
        }

        return false;
    }

    public function processUpdatedSlot(string $slot_id)
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
                if (!empty($slot['address'])) {
                    Emercoin::name_update($slot['name'], $slot['value'], $addrs[$wname]['days'], $slot['address']);
                } else {
                    Emercoin::name_update($slot['name'], $slot['value'], $addrs[$wname]['days']);
                }

                $this->db->setSlotPayed($slot_id);  
                return true;
            }
        }

        return false;
    }

    public function processSlots()
    {
        foreach($this->db->listUnpayedSlots() as $slot) {
            $this->processUnpayedSlot($slot['slot_id']);
        }

        foreach($this->db->listUpdatedSlots() as $slot) {
            $this->processUpdatedSlot($slot['slot_id']);
        }
    }
}