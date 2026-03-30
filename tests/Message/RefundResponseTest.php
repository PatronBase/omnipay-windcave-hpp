<?php

namespace Omnipay\WindcaveHpp\Message;

use Omnipay\Tests\TestCase;

class RefundResponseTest extends TestCase
{
    public function testRefundSuccess()
    {
        $response = new RefundResponse($this->getMockRequest(), (object) [
            'id' => 'refundtransaction123',
            'type' => 'refund',
            'responseText' => 'APPROVED',
            'authorised' => true
        ]);

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('refundtransaction123', $response->getTransactionReference());
        $this->assertSame('APPROVED', $response->getMessage());
    }

    public function testRefundFailure()
    {
        $response = new RefundResponse($this->getMockRequest(), (object) [
            'id' => 'refundtransaction123',
            'type' => 'refund',
            'responseText' => 'DECLINED',
            'authorised' => true
        ]);

        $this->assertFalse($response->isSuccessful());
        $this->assertSame('refundtransaction123', $response->getTransactionReference());
        $this->assertSame('DECLINED', $response->getMessage());
    }
}