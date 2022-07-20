<?php
namespace lib;

interface iModule {
    public function createSlot(array $fields);
    public function findSlot(array $fields): array;
    public function locateSlot(array $fields): array;
}