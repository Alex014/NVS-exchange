<?php
namespace modules;
require_once __DIR__ . '/../wallets/Emercoin.php';
require_once __DIR__ . '/../lib/Slots.php';
require_once __DIR__ . '/../lib/IWallet.php';
require_once __DIR__ . '/../modules/BaseModule.php';

use Exception;
use lib\iDecoder;
use lib\Slots;
use modules\BaseModule;
use wallets\Emercoin;

class ExchangeForm extends BaseModule {

    private $url;
    private $exchange_form_decoder;

    public function __construct(Slots $slot, Emercoin $emc, iDecoder $exchange_form_decoder, iDecoder $exchange_form_token_decoder) 
    {
        $this->exchange_form_decoder = $exchange_form_decoder;

        parent::__construct($slot,  $exchange_form_token_decoder);
        
        $nvs = $emc->getNVS($exchange_form_decoder->encodeName([]));

        if (empty($nvs)) {
            throw new \Exception('Exchange form record not found in NVS !');
        }

        $exchange_fields =  $this->exchange_form_decoder->decodeValue($nvs['value']);
        $this->url = $exchange_fields['url'];
        $this->title = $exchange_fields['title'];
    }

    public function pingExchangeForm(): bool
    {
        $data = file_get_contents($this->url);
        $data = json_decode($data, true);
        return isset($data['status']);
    }

    private function decodeToken(string $value): array
    {
        return $this->getDecoder()->decodeValue($value);
    }

    private function loadToken(string $addr, string $payAddr): array
    {
        $token = file_get_contents($this->url . "?address=$addr&pay_address=$payAddr");
        return json_decode($token, true);
    }

    public function showSlot(string $slot_id)
    {
        $slot = $this->getSlot()->showSlot($slot_id);

        if (false === $slot) {
            return false;
        }

        $fields = $this->decodeToken($slot['value']);

        if ('payed' === strtolower($slot['status'])) {
            $token = $this->loadToken($fields['address'], $fields['pay_address']);
            // var_dump($token);
            switch (strtolower($token['status'])) {
                case 'activated':
                    return $fields + [
                        'id' => $slot['id'],
                        'addr' => $slot['addr'],
                        'name' => $slot['name'],
                        'address' => $token['address'],
                        'pay_address' => $token['pay_address'],
                        'gen_address' => $token['gen_address'],
                        'hours' => $token['hours'],
                        'recieve' => $token['recieve'],
                        'status' => 'activated'
                    ];
                break;
                case 'payed':
                    return $fields + [
                        'id' => $slot['id'],
                        'addr' => $slot['addr'],
                        'name' => $slot['name'],
                        'address' => $token['address'],
                        'pay_address' => $token['pay_address'],
                        'gen_address' => $token['gen_address'],
                        'hours' => $token['hours'],
                        'recieve' => $token['recieve'],
                        'status' => 'done'
                    ];
                break;
                case 'nofunds':
                    return $fields + [
                        'id' => $slot['id'],
                        'addr' => $slot['addr'],
                        'name' => $slot['name'],
                        'address' => $token['address'],
                        'pay_address' => $token['pay_address'],
                        'gen_address' => $token['gen_address'],
                        'hours' => $token['hours'],
                        'recieve' => $token['recieve'],
                        'error' => "The exchange is out of funds =( =( =( ",
                        'status' => 'error'
                    ];
                break;
                case 'error':
                    return $fields + [
                        'id' => $slot['id'],
                        'addr' => $slot['addr'],
                        'name' => $slot['name'],
                        'address' => $token['address'],
                        'pay_address' => $token['pay_address'],
                        'gen_address' => $token['gen_address'],
                        'hours' => $token['hours'],
                        'recieve' => $token['recieve'],
                        'error' => "Your token is failed: \n" . $token['error'],
                        'status' => 'error'
                    ];
                break;
                default:
                    return $fields + [
                        'id' => $slot['id'],
                        'addr' => $slot['addr'],
                        'name' => $slot['name'],
                        'status' => $slot['status']
                    ];
            }
        } else {
            return $fields + [
                'id' => $slot['id'],
                'addr' => $slot['addr'],
                'name' => $slot['name'],
                'status' => $slot['status']
            ];
        }
    }
}