<?php

namespace decoders;

require_once __DIR__ . '/../lib/iDecoder.php';

use lib\iDecoder;

class ExchangeFormDecoder implements iDecoder {

    public function encodeName(array $fields): string
    {
        return "worm:exchange:ness_exchange_v1_v2";
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

        $title = (string) $xmlObject->exchange['title'];
        $url = (string) $xmlObject->exchange['url'];

        return [
            "url" => $url,
            "title" => $title,
        ];
    }
}