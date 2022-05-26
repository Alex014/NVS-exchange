<?php
namespace lib;

interface iSlotDatabase {
    function createSlot(string $key, string $value, string $addr, string $slot_id);
    function setSlotPayed(string $slot_id);
    function getSlot(string $slot_id);
    function findSlot(string $name);
    function listSlots();
    function listUnpayedSlots();
    function listPayedSlots();
    function getLastSlot();
}