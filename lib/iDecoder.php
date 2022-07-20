<?php
namespace lib;

interface iDecoder {
    public function encodeName(array $fields): string;
    public function encodeValue(array $fields): string;
    public function decodeValue(string $value): array;
}