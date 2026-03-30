<?php

namespace Omnipay\WindcaveHpp;

use Omnipay\Tests\GatewayTestCase;
use Omnipay\Common\Message\NotificationInterface;

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
            'card'          => [
                'email'            => "test@example.net",
                'name'             => "ABCDE FGHIJK",
                'phone'            => "123 456 7890",
                'shippingAddress1' => "Ship 1 Test",
                'shippingAddress2' => "Ship 2 Test",
                'shippingCity'     => "Ship 4 City",
                'shippingPostcode' => "Ship 5 Postcode",
                'shippingState'    => "Ship 6 State",
                'shippingCountry'  => "Ship 7 Country",
                'billingAddress1'  => "Bill 1 Test",
                'billingAddress2'  => "Bill 2 Test",
                'billingCity'      => "Bill 4 City",
                'billingPostcode'  => "Bill 5 Postcode",
                'billingState'     => "Bill 6 State",
                'billingCountry'   => "Bill 7 Country",
            ],
        ];
    }

    public function testPurchaseSuccess()
    {
        $this->setMockHttpResponse('PurchaseSuccess.txt');

        $options = $this->options + ['type' => 'purchase'];
        $request = $this->gateway->purchase($options);

        $data = $request->getData();
        $this->assertSame('test@example.net', $data['customer']['email']);
        $this->assertSame('ABCDE FGHIJK', $data['customer']['billing']['name']);
        $this->assertSame('Bill 1 Test', $data['customer']['billing']['address1']);
        $this->assertSame('Bill 2 Test', $data['customer']['billing']['address2']);
        $this->assertSame('Bill 4 City', $data['customer']['billing']['city']);
        $this->assertSame('Bill 7 Country', $data['customer']['billing']['countryCode']);
        $this->assertSame('Bill 5 Postcode', $data['customer']['billing']['postalCode']);
        $this->assertSame('123 456 7890', $data['customer']['billing']['phoneNumber']);
        $this->assertSame('Bill 6 State', $data['customer']['billing']['state']);
        $this->assertSame('ABCDE FGHIJK', $data['customer']['shipping']['name']);
        $this->assertSame('Ship 1 Test', $data['customer']['shipping']['address1']);
        $this->assertSame('Ship 2 Test', $data['customer']['shipping']['address2']);
        $this->assertSame('Ship 4 City', $data['customer']['shipping']['city']);
        $this->assertSame('Ship 7 Country', $data['customer']['shipping']['countryCode']);
        $this->assertSame('Ship 5 Postcode', $data['customer']['shipping']['postalCode']);
        $this->assertSame('123 456 7890', $data['customer']['shipping']['phoneNumber']);
        $this->assertSame('Ship 6 State', $data['customer']['shipping']['state']);

        $response = $request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertNull($response->getTransactionReference());
        $this->assertNull($response->getMessage());
        $this->assertSame('GET', $response->getRedirectMethod());
        $this->assertSame(
            'https://uat.windcave.com/pxmi3/EF4054F622D6C4C1B5D0F260ED2B2C143C1EDCADD7A86374ED601212E11C409319CD439CC8082194B',
            $response->getRedirectUrl()
        );
        $this->assertSame([], $response->getRedirectData());
    }

    public function testCompletePurchaseSuccess()
    {
        $this->setMockHttpResponse('CompletePurchaseSuccess.txt');

        $request = $this->gateway->completePurchase([
            'apiUsername' => $this->options['apiUsername'],
            'apiKey'      => $this->options['apiKey'],
            'sessionId'   => 'session123',
            'testMode'    => true,
        ]);

        $this->getHttpRequest()->request->set('sessionId','session123');

        $response = $request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('APPROVED', $response->getResponseText());
        $this->assertSame('session123', $response->getSessionId());
        $this->assertSame('merchantRef123', $response->getTransactionId());
        $this->assertSame('transaction123', $response->getTransactionReference());
        $this->assertSame('411111......1111', $response->getCard()['cardNumber']);
    }

    public function testAcceptNotificationPaid()
    {
        $this->setMockHttpResponse('CompletePurchaseSuccess.txt');

        $this->getHttpRequest()->request->set('sessionId','session123');

        $notification = $this->gateway->acceptNotification([
            'apiUsername' => $this->options['apiUsername'],
            'apiKey'      => $this->options['apiKey'],
            'sessionId'   => 'session123',
            'testMode'    => true,
        ]);

        $this->assertSame(NotificationInterface::STATUS_COMPLETED, $notification->getTransactionStatus());
        $this->assertSame('transaction123', $notification->getTransactionReference());
    }


    public function testAcceptNotificationFailed()
    {
        $this->setMockHttpResponse('CompletePurchaseFailed.txt');

        $this->getHttpRequest()->request->set('sessionId','session123');

        $notification = $this->gateway->acceptNotification([
            'apiUsername' => $this->options['apiUsername'],
            'apiKey'      => $this->options['apiKey'],
            'sessionId'   => 'SESSION123',
            'testMode'    => true,
        ]);

        $this->assertSame(NotificationInterface::STATUS_FAILED, $notification->getTransactionStatus());
    }

    public function testRefundSuccess()
    {
        $this->setMockHttpResponse('RefundSuccess.txt');

        $request = $this->gateway->refund([
            'apiUsername'          => $this->options['apiUsername'],
            'apiKey'               => $this->options['apiKey'],
            'amount'               => '1.00',
            'currency'             => 'GBP',
            'transactionReference' => 'refundtransaction123',
            'testMode'             => true,
        ]);

        $response = $request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('refundtransaction123', $response->getTransactionReference());
        $this->assertSame('APPROVED', $response->getMessage());
    }

    public function testRefundFailed()
    {
        $this->setMockHttpResponse('RefundFailed.txt');

        $request = $this->gateway->refund([
            'apiUsername'          => $this->options['apiUsername'],
            'apiKey'               => $this->options['apiKey'],
            'amount'               => '1.00',
            'currency'             => 'GBP',
            'transactionReference' => 'refundtransaction123',
            'testMode'             => true,
        ]);

        $response = $request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertSame('refundtransaction123', $response->getTransactionReference());
        $this->assertSame('DECLINED', $response->getMessage());
    }
}
