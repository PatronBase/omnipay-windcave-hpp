<?php

namespace Omnipay\WindcaveHpp\Message;

use Omnipay\Tests\TestCase;

class MitPurchaseResponseTest extends TestCase
{
    private function makeResponse(array $transactionFields): MitPurchaseResponse
    {
        return new MitPurchaseResponse($this->getMockRequest(), (object) $transactionFields);
    }

    public function testSuccessfulMitCharge(): void
    {
        $response = $this->makeResponse([
            'id'           => 'tx_mit_456',
            'authorised'   => true,
            'responseText' => 'APPROVED',
            'card'         => (object) [
                'id'              => 'card_stored_789',
                'cardNumber'      => '411111......1111',
                'cardHolderName'  => 'Test Patron',
                'dateExpiryMonth' => '05',
                'dateExpiryYear'  => '28',
                'type'            => 'visa',
            ],
        ]);

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('tx_mit_456', $response->getTransactionReference());
        $this->assertSame('card_stored_789', $response->getCardReference());
        $this->assertSame('APPROVED', $response->getMessage());
        $this->assertSame('411111......1111', $response->getCard()['cardNumber']);
        $this->assertSame('05', $response->getCard()['dateExpiryMonth']);
        $this->assertSame('visa', $response->getCard()['type']);
    }

    public function testDeclinedMitCharge(): void
    {
        $response = $this->makeResponse([
            'id'           => 'tx_mit_declined',
            'authorised'   => false,
            'responseText' => 'DECLINED',
        ]);

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('DECLINED', $response->getMessage());
        $this->assertSame('tx_mit_declined', $response->getTransactionReference());
        $this->assertNull($response->getCardReference());
        $this->assertSame([], $response->getCard());
    }

    public function testNullDataIsNotSuccessful(): void
    {
        $response = new MitPurchaseResponse($this->getMockRequest(), null);

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNull($response->getTransactionReference());
        $this->assertNull($response->getMessage());
        $this->assertNull($response->getCardReference());
        $this->assertSame([], $response->getCard());
    }
}
