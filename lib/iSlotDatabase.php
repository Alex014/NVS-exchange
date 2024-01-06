<?php
namespace lib;

interface iSlotDatabase {
    function execSql(string $sql);
    function createSlot(string $key, string $value, string $addr, string $address, string $slot_id);
    function updateSlot(string $key, string $value, string $addr, string $address, string $slot_id);
    function setSlotPayed(string $slot_id);
    function getSlot(string $slot_id);
    function deleteSlot(string $slot_id);
    function findSlot(string $name);
    function listSlots();
    function listUnpayedSlots();
    function listUpdatedSlots();
    function listPayedSlots();
    function getLastSlot();
}