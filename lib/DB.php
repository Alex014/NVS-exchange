<?php
namespace lib;

require_once __DIR__ . '/iSlotDatabase.php';

use \lib\iSlotDatabase;

class DB implements iSlotDatabase {
    private $connection;

    public function __construct($host, $database, $user, $password)
    {
        $this->connection = new \PDO('mysql:host=' . $host . ';dbname=' . $database, $user, $password);
    }

    public function execSql(string $sql)
    {
        $st = $this->connection->prepare($sql);
        return $st->execute();
    }

    public function createSlot(string $key, string $value, string $addr, string $address, string $slot_id)
    {
        $st = $this->connection->prepare(
            "INSERT INTO slots (slot_id, addr, name, value, address, created) VALUES(?, ?, ?, ?, ?, ?)");
        return $st->execute([$slot_id, $addr, $key, $value, $address, time()]);
    }

    public function updateSlot(string $key, string $value, string $addr, string $address, string $slot_id)
    {
        $st = $this->connection->prepare(
            "UPDATE slots SET "
            . "addr = ?, name = ?, value = ?, address = ?, created = ?, `status` = 'UPDATED' "
            . "WHERE `slot_id` = ?");
        return $st->execute([$addr, $key, $value, $address, time(), $slot_id]);
    }

    public function setSlotPayed(string $slot_id)
    {
        $st = $this->connection->prepare(
            "UPDATE slots SET `status` = 'PAYED' WHERE `slot_id` = ?");

        return $st->execute([$slot_id]);
    }

    public function getSlot(string $slot_id)
    {
        $st = $this->connection->prepare("SELECT * FROM slots WHERE `slot_id` = ?");
        $st->execute([$slot_id]);
        return $st->fetch();
    }

    public function deleteSlot(string $slot_id)
    {
        $st = $this->connection->prepare("DELETE FROM slots WHERE `slot_id` = ?");

        return $st->execute([$slot_id]);
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
        $st = $this->connection->query("SELECT * FROM slots WHERE `status` = 'GENERATED'", \PDO::FETCH_ASSOC);
        return $st->fetchAll();
    }

    public function listUpdatedSlots()
    {
        $st = $this->connection->query("SELECT * FROM slots WHERE `status` = 'UPDATED'", \PDO::FETCH_ASSOC);
        return $st->fetchAll();
    }

    public function listPayedSlots()
    {
        $st = $this->connection->query("SELECT * FROM slots WHERE `status` = 'PAYED'", \PDO::FETCH_ASSOC);
        return $st->fetchAll();
    }

    public function getLastSlot()
    {
        $st = $this->connection->query("SELECT * FROM slots ORDER BY ID DESC LIMIT 1", \PDO::FETCH_ASSOC);
        return $st->fetch();
    }
}