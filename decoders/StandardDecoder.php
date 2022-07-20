<?php

namespace decoders;

require_once __DIR__ . '/../lib/iDecoder.php';

use lib\iDecoder;

class StandardDecoder implements iDecoder {

    public function encodeName(array $fields): string
    {
        return $fields['name'];
    }

    public function encodeValue(array $fields): string
    {
        return $fields['value'];
    }

    public function decodeValue(string $value): array
    {
        return [
            'value' => $value
        ];
    }
}