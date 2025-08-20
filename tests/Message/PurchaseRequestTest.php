<?php

namespace Omnipay\WindcaveHpp\Message;

use Omnipay\Tests\TestCase;

class PurchaseRequestTest extends TestCase
{
    /** @var PurchaseRequest */
    private $request;

    public function setUp(): void
    {
        $this->options = [
            'apiUsername'   => 'merchant',
            'apiKey'        => 'apikey',
            'amount'        => '1.45',
            'currency'      => 'NZD',
            'returnUrl'     => 'https://example.com/return',
            'cancelUrl'     => 'https://example.com/cancel',
            'notifyUrl'     => 'https://example.com/notify',
            'transactionId' => 'transaction123',
            'testMode'      => true,
            'card'          => $this->getValidCard(),
        ];

        $this->request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize($this->options);
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
        $data = $this->request->getData();

        $this->assertArrayHasKey('amount', $data);
        $this->assertArrayHasKey('currency', $data);
    }

    public function testRedirect(): void
    {
        $this->setMockHttpResponse('PurchaseSuccess.txt');

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertSame('GET', $response->getRedirectMethod());
        $this->assertSame('https://uat.windcave.com/pxmi3/EF4054F622D6C4C1B5D0F260ED2B2C143C1EDCADD7A86374ED601212E11C409319CD439CC8082194B', $response->getRedirectUrl());
        $this->assertSame([], $response->getRedirectData());
    }

    public function testReturnUrls(): void
    {
        $this->request->setReturnUrl('https://example.com/return');
        $this->request->setDeclineUrl('https://example.com/decline');
        $this->request->setCancelUrl('https://example.com/cancel');
        $this->request->setNotifyUrl('https://example.com/notify');

        $data = $this->request->getData();

        $this->assertSame('https://example.com/return', $data['callbackUrls']['approved']);
        $this->assertSame('https://example.com/decline', $data['callbackUrls']['declined']);
        $this->assertSame('https://example.com/cancel', $data['callbackUrls']['cancelled']);
        $this->assertSame('https://example.com/notify', $data['notificationUrl']);
    }

    public function testCreateTokenSetsStoreCardTrue(): void
    {
        $this->request->setCreateToken(true);
        $data = $this->request->getData();
        $this->assertArrayHasKey('storeCard', $data);
        $this->assertTrue($data['storeCard']);
    }

    public function testStoredCardIndicator(): void
    {
        $this->request->setStoredCardIndicator('credentialonfileinitial');
        $data = $this->request->getData();
        $this->assertSame('credentialonfileinitial', $data['storedCardIndicator']);
    }

    public function testRecurringFields(): void
    {
        $this->request->setRecurringFrequency('monthly');
        $this->request->setRecurringExpiry('9999-12-31');
        $data = $this->request->getData();
        $this->assertSame('monthly', $data['recurringFrequency']);
        $this->assertSame('9999-12-31', $data['recurringExpiry']);
    }

    public function testCardIdFromToken(): void
    {
        $this->request->setToken('tok_abc');
        $data = $this->request->getData();
        $this->assertSame('tok_abc', $data['cardId']);
    }

    public function testCardIdFromCardReference(): void
    {
        $this->request->setToken(null);
        $this->request->setCardReference('card_ref_123');
        $data = $this->request->getData();
        $this->assertSame('card_ref_123', $data['cardId']);
    }

    public function testPaymentMethodsAndCardTypesAndMetadata(): void
    {
        $this->request->setPaymentMethods(['card', 'alipay']);
        $this->request->setCardTypes(['visa', 'mastercard']);
        $this->request->setMetadata(['key' => 'value']);

        $data = $this->request->getData();

        $this->assertSame(['card', 'alipay'], $data['methods']);
        $this->assertSame(['visa', 'mastercard'], $data['cardTypes']);
        $this->assertSame(['key' => 'value'], $data['metaData']);
    }
}