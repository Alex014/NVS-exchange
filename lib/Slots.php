<?php
namespace lib;
require __DIR__ . '/Emercoin.php';

use \lib\iSlotDatabase;
use \lib\Emercoin;

class Stots {

    private iSlotDatabase $db;
    public float $min_sum = 0.01;
    public float $days = 100;

    public function __construct(iSlotDatabase $db, float $min_sum = 0.01, int $days = 100)
    {
        $this->db = $db;
        $this->min_sum = $min_sum;
        $this->days = $days;

        $config = require __DIR__ . '/../config/config.php';
        $femc = $config['emercoin'];

        Emercoin::$address = $femc['host'];
        Emercoin::$port = $femc['port'];
        Emercoin::$username = $femc['user'];
        Emercoin::$password = $femc['password'];
    }

    public function createSlot(string $key, string $value): string
    {
        $slot_id = md5(rand(10000, 99999) . time());
        $addr = (string) Emercoin::createNewAddress($slot_id);

        $this->db->createSlot($key, $value, $addr, $slot_id);

        return $slot_id;
    }

    public function showSlot(string $slot_id)
    {
        return $this->db->getSlot($slot_id);
    }

    public function findSlot(string $name)
    {
        return $this->db->findSlot($name);
    }

    public function locateSlot(string $key)
    {
        return Emercoin::name_list($key);
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

    public function processSlot(string $slot_id)
    {
        $slot = $this->db->getSlot($slot_id);

        if (empty($slot)) {
            return false;
        }

        if (Emercoin::getRecievedByAddress($slot['addr']) >= (float)$this->min_sum) {
            Emercoin::name_new($slot['name'], $slot['value'], $this->days);
            $this->db->setSlotPayed($slot_id);
            return true;
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