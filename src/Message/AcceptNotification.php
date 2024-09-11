<?php

namespace Omnipay\WindcaveHpp\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Http\ClientInterface;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Common\Message\NotificationInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;

class AcceptNotification extends PurchaseRequest implements NotificationInterface{
    protected $data;

    protected $transaction;

    public function getData()
    {
        return $this->data;
    }
    public function sendData($data)
    {
        $sessionId = $this->httpRequest->query->get('sessionId') ?? $this->httpRequest->request->get('sessionId') ?? '';

        if ( empty($sessionId) ) {
            throw new InvalidRequestException('Session id is required');
        }

        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . $this->getAuthorization()
        ];

        try {
            $httpResponse = $this->httpClient->request('GET', $this->getEndpoint('sessions/' . $sessionId), $headers);
        } catch (\Exception $exception) {
            throw new InvalidRequestException($exception->getMessage());
        }

        $transactionData = json_decode($httpResponse->getBody()->getContents(), true);

        $this->data = $transactionData;
        $this->transaction = $transactionData['transactions'][0] ?? [];

        return $this;
    }

    public function getTransaction()
    {
        return $this->transaction;
    }

    public function getTransactionReference()
    {
        return $this->getTransaction()['id'] ?? '';
    }

    public function getTransactionStatus()
    {
        if ( $this->getTransaction() && $this->getAuthorised() && $this->getResponseText() === 'APPROVED' )  {
            return static::STATUS_COMPLETED;
        }

        return static::STATUS_FAILED;
    }

    public function getAuthorised()
    {
        return $this->getTransaction()['authorised'] ?? false;
    }

    public function getResponseText()
    {
        return strtoupper($this->getTransaction()['responseText']) ?? '';
    }

    public function getMessage()
    {
        return $this->getResponseText() ?? '';
    }
}