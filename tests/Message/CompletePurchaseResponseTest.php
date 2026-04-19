<?php

namespace Omnipay\WindcaveHpp\Message;

use Omnipay\Tests\TestCase;

class CompletePurchaseResponseTest extends TestCase
{
    private function makeResponse(array $transactionOverrides = []): CompletePurchaseResponse
    {
        $transaction = array_merge([
            'authorised'      => true,
            'responseText'    => 'APPROVED',
            'merchantReference' => 'ref-123',
            'id'              => 'txn-456',
            'type'            => 'purchase',
            'method'          => 'card',
            'card'            => [],
        ], $transactionOverrides);

        return new CompletePurchaseResponse($this->getMockRequest(), [
            'transactions' => [$transaction],
        ]);
    }

    public function testIsSuccessfulWithApproved(): void
    {
        $this->assertTrue($this->makeResponse(['responseText' => 'APPROVED'])->isSuccessful());
    }

    public function testIsSuccessfulWithApprovedAmex(): void
    {
        // Amex returns "APPROVED (00)" rather than plain "APPROVED"
        $this->assertTrue($this->makeResponse(['responseText' => 'APPROVED (00)'])->isSuccessful());
    }

    public function testIsSuccessfulWithLowercaseApproved(): void
    {
        $this->assertTrue($this->makeResponse(['responseText' => 'approved'])->isSuccessful());
    }

    public function testIsNotSuccessfulWhenDeclined(): void
    {
        $this->assertFalse($this->makeResponse(['responseText' => 'DECLINED', 'authorised' => false])->isSuccessful());
    }

    public function testIsNotSuccessfulWhenNotAuthorised(): void
    {
        $this->assertFalse($this->makeResponse(['authorised' => false])->isSuccessful());
    }

    public function testIsNotSuccessfulWithEmptyTransactions(): void
    {
        $response = new CompletePurchaseResponse($this->getMockRequest(), ['transactions' => []]);
        $this->assertFalse($response->isSuccessful());
    }

    public function testGetMessage(): void
    {
        $response = $this->makeResponse(['responseText' => 'APPROVED (00)']);
        $this->assertSame('APPROVED (00)', $response->getMessage());
    }
}