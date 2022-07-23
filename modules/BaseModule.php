<?php

namespace modules;

require_once __DIR__ . '/../lib/iModule.php';
require_once __DIR__ . '/../lib/iDecoder.php';
require_once __DIR__ . '/../lib/Slots.php';

use lib\iModule;
use lib\iDecoder;
use lib\Slots;

class BaseModule implements iModule
{

    private $decoder;
    private $slot;

    public function __construct(Slots $slot, iDecoder $decoder)
    {
        $this->decoder = $decoder;
        $this->slot = $slot;
    }

    public function getSlot(): Slots
    {
        return $this->slot;
    }

    public function getDecoder(): iDecoder
    {
        return $this->decoder;
    }

    public function createSlot(array $fields)
    {
        return $this->slot->createSlot(
            $this->decoder->encodeName($fields), 
            $this->decoder->encodeValue($fields)
        );
    }

    public function findSlot(array $fields): array
    {
        return $this->slot->findSlot($this->decoder->encodeName($fields));
    }

    public function locateSlot(array $fields): array
    {
        return $this->slot->locateSlot($this->decoder->encodeName($fields));
    }
}
