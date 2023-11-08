<?php
namespace lib;

require_once __DIR__ . '/iSlotDatabase.php';

use \lib\iSlotDatabase;

class Sqlite implements iSlotDatabase {
    private $connection;

    public function __construct($db_filename)
    {
        $this->connection = new \PDO("sqlite:" . __DIR__ . '/' . $db_filename);

        $sql = <<<SQL
            CREATE TABLE IF NOT EXISTS `slots` (
            `id` INTEGER PRIMARY KEY,
            `slot_id` TEXT KEY NOT NULL,
            `addr` TEXT KEY,
            `name` TEXT UNIQUE,
            `value` TEXT  NOT NULL,
            `status` TEXT KEY CHECK( `status` IN ('GENERATED','UPDATED','PAYED') )   NOT NULL DEFAULT 'GENERATED',
            `created` INTEGER UNSIGNED KEY NOT NULL 
            )
        SQL;

        $st = $this->connection->prepare($sql);
        return $st->execute();
    }


    public function createSlot(string $key, string $value, string $addr, string $slot_id)
    {
        $st = $this->connection->prepare(
            "INSERT INTO slots (slot_id, addr, name, value, created) VALUES(?, ?, ?, ?, ?)");
        return $st->execute([$slot_id, $addr, $key, $value, time()]);
    }

    public function updateSlot(string $key, string $value, string $addr, string $slot_id)
    {
        $st = $this->connection->prepare(
            "UPDATE slots SET "
            . "addr = ?, name = ?, value = ?, created = ?, `status` = 'UPDATED' "
            . "WHERE `slot_id` = ?");
        return $st->execute([$addr, $key, $value, time(), $slot_id]);
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