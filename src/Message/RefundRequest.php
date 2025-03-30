<?php

namespace Omnipay\WindcaveHpp\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Http\Exception;

class RefundRequest extends BaseRequest
{
    public function getData()
    {
        // Validate required parameters
        $this->validate('apiUsername', 'apiKey', 'amount', 'currency', 'transactionReference');

        $data = [];
        $data['type'] = 'refund';
        $data['amount'] = $this->getAmount();
        $data['currency'] = $this->getCurrency();
        $data['transactionId'] = $this->getTransactionReference();

        if ($this->getMerchantReference()) {
            $data['merchantReference'] = $this->getMerchantReference();
        }

        return $data;
    }

    public function sendData($data)
    {
        $headers = [
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
            'Authorization' => 'Basic ' . $this->getAuthorization(),
        ];

        $httpResponse = $this->httpClient->request('POST', $this->getEndpoint('transactions'), $headers, json_encode($data));

        try {
            $responseData = json_decode($httpResponse->getBody()->getContents());
        } catch (\Exception $exception) {
            $responseData = [];
        }

        return $this->response = new RefundResponse($this, $responseData ?? []);
    }
}