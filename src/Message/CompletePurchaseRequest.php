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
        return [
            'sessionId' => $this->getParameter('sessionId') ?? $this->httpRequest->query->get('sessionId') ?? $this->httpRequest->request->get('sessionId') ?? '',
            'username' => $this->getParameter('username') ?? $this->httpRequest->query->get('username') ?? $this->httpRequest->request->get('username') ?? '',
        ];
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
