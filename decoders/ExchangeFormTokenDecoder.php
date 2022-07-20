<?php

namespace decoders;

require_once __DIR__ . '/../lib/iDecoder.php';

use lib\iDecoder;

class ExchangeFormTokenDecoder implements iDecoder {

    public function encodeName(array $fields): string
    {
        return "worm:token:$fields[address]:$fields[pay_address]";
    }

    public function encodeValue(array $fields): string
    {
        return <<<WORM
<worm>
    <token type="ness-exchange-v1-v2" title="$fields[title]" url="$fields[url]"/>
</worm> 
WORM;
    }

    public function decodeValue(string $value): array
    {
        $xmlString = preg_replace("/<!--.+?-->/i", '', $value);
        $xmlString = preg_replace('/”/i', '"', $xmlString);
        $xmlObject = simplexml_load_string($xmlString);

        $address = (string) $xmlObject->slot['address'];
        $pay_address = (string) $xmlObject->slot['pay_address'];

        return [
            "address" => $address,
            "pay_address" => $pay_address,
        ];
    }
}