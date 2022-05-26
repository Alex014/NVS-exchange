<?php
namespace lib;

require __DIR__ . '/iSlotDatabase.php';

use \lib\iSlotDatabase;

class DB implements iSlotDatabase {
    private $connection;

    public function __construct($host, $database, $user, $password)
    {
        $this->connection = new \PDO('mysql:host=' . $host . ';dbname=' . $database, $user, $password);
    }


    public function createSlot(string $key, string $value, string $addr, string $slot_id)
    {
        $st = $this->connection->prepare(
            "INSERT INTO slots (slot_id, addr, name, value, created) VALUES(?, ?, ?, ?, ?)");
        return $st->execute([$slot_id, $addr, $key, $value, time()]);
    }

    public function setSlotPayed(string $slot_id)
    {
        $st = $this->connection->prepare(
            "UPDATE slots SET status = 'PAYED' WHERE `slot_id` = ?");

        return $st->execute([$slot_id]);
    }

    public function getSlot(string $slot_id)
    {
        $st = $this->connection->prepare("SELECT * FROM slots WHERE `slot_id` = ?");
        $st->execute([$slot_id]);
        return $st->fetch();
    }

    public function findSlot(string $name)
    {
        $st = $this->connection->prepare("SELECT * FROM slots WHERE `name` = ?");
        $st->execute([$name]);
        return $st->fetch();
    }

    public function listSlots()
    {
        $st = $this->connection->query("SELECT * FROM slots", \PDO::FETCH_ASSOC);
        return $st->fetchAll();
    }

    public function listUnpayedSlots()
    {
        $st = $this->connection->query("SELECT * FROM slots WHERE `status` != 'PAYED'", \PDO::FETCH_ASSOC);
        return $st->fetchAll();
    }

    public function listPayedSlots()
    {
        $st = $this->connection->query("SELECT * FROM slots WHERE `status` == 'PAYED'", \PDO::FETCH_ASSOC);
        return $st->fetchAll();
    }

    public function getLastSlot()
    {
        $st = $this->connection->query("SELECT * FROM slots ORDER BY ID DESC LIMIT 1", \PDO::FETCH_ASSOC);
        return $st->fetch();
    }
}