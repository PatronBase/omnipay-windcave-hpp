<?php

namespace Omnipay\WindcaveHpp;

use Omnipay\Tests\GatewayTestCase;

class GatewayTest extends GatewayTestCase
{
    /** @var array */
    protected $options;

    public function setUp(): void
    {
        parent::setUp();

        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());

        $this->options = [
            'amount'        => '1.45',
            'apiUsername'   => 'Test_Merchant',
            'apiKey'        => 'ABCDEF1234567890ABCDEF1234567890ABCDEF1234567890ABCDEF1234567890',
            'currency'      => 'NZD',
            'cancelUrl'     => 'https://www.example.com/cancel',
            'notifyUrl'     => 'https://www.example.com/notify',
            'returnUrl'     => 'https://www.example.com/return',
            'transactionId' => '123abc',
            'testMode'      => true,
        ];
    }
}
