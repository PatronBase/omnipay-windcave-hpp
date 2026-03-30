<?php

namespace Omnipay\WindcaveHpp\Message;
use Omnipay\WindcaveHpp\Gateway;
use Omnipay\Tests\TestCase;

class RefundRequestTest extends TestCase
{
    protected $request;
    protected $gateway;

    protected function setUp(): void
    {
        $this->options = [
            'apiUsername'   => 'merchant',
            'apiKey'        => 'apikey',
            'amount'        => '1.45',
            'currency'      => 'NZD',
        ];

        $this->request = new RefundRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize($this->options);
        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testApiKeys(): void
    {
        $this->request->setApiUsername('user123');
        $this->request->setApiKey('key123');

        $this->assertSame('user123', $this->request->getApiUsername());
        $this->assertSame('key123', $this->request->getApiKey());
    }

    public function testRequiredParameters(): void
    {
        $this->expectException(\Omnipay\Common\Exception\InvalidRequestException::class);
        $data = $this->request->getData();

        $this->assertArrayHasKey('amount', $data);
        $this->assertArrayHasKey('currency', $data);
    }

    public function testRefundSuccess(): void
    {
        $this->setMockHttpResponse('RefundSuccess.txt');

        $request = $this->gateway->refund([
            'apiUsername'          => 'merchant',
            'apiKey'               => 'apikey',
            'amount'               => '1.45',
            'currency'             => 'NZD',
            'transactionReference' => 'refundtransaction123',
            'testMode'             => true,
        ]);

        $response = $request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('refundtransaction123', $response->getTransactionReference());
        $this->assertSame('APPROVED', $response->getMessage());
    }
}