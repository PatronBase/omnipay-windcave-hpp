<?php

namespace Omnipay\WindcaveHpp\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Windcave HPP Complete Purchase Request
 */
class CompletePurchaseRequest extends PurchaseRequest
{
    public function getData()
    {
        $this->validate('sessionId');

        return [
            'sessionId' => $this->getSessionId(),
        ];
    }

    public function setSessionId($sessionId)
    {
        return $this->setParameter('sessionId', $sessionId);
    }

    public function getSessionId()
    {
        return $this->getParameter('sessionId');
    }

    public function sendData($data)
    {
        if ( !$data['sessionId'] ) {
            throw new InvalidRequestException('Session id is required');
        }

        $sessionId = $data['sessionId'];

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

        return $this->response = new CompletePurchaseResponse($this, $transactionData);
    }
}
