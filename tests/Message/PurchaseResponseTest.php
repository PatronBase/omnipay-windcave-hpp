<?php

namespace Omnipay\WindcaveHpp\Message;

use Omnipay\Tests\TestCase;
use Omnipay\Common\Message\RequestInterface;

class PurchaseResponseTest extends TestCase
{
    public function testGetRedirectUrlFindsHppLink(): void
    {
        $response = new PurchaseResponse($this->getMockRequest(), (object) [
            'links' => [
                (object) ['rel' => 'self', 'href' => 'https://uat.windcave.com/api/v1/sessions/session1234', 'method' => 'GET'],
                (object) ['rel' => 'hpp',  'href' => 'https://uat.windcave.com/pxmi3/session1234', 'method' => 'REDIRECT'],
                (object) ['rel' => 'hpp',  'href' => 'https://uat.windcave.com/api/v1/sessions/session1234', 'method' => 'PATCH'],
            ],
        ]);

        $this->assertTrue($response->isRedirect());
        $this->assertSame('GET', $response->getRedirectMethod());
        $this->assertSame(
            'https://uat.windcave.com/pxmi3/session1234',
            $response->getRedirectUrl()
        );
    }

    public function testGetRedirectUrlThrowsWhenMissing(): void
    {
        $this->expectException(\Omnipay\Common\Exception\InvalidResponseException::class);

        $resp = new PurchaseResponse($this->getMockRequest(), (object) ['links' => [(object) ['rel' => 'self', 'href' => 'x']]]);

        $resp->getRedirectUrl();
    }
}