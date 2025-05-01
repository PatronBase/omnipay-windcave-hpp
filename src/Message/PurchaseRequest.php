<?php

namespace Omnipay\WindcaveHpp\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Windcave HPP Purchase Request
 */
class PurchaseRequest extends BaseRequest
{
    public function initialize(array $parameters = [])
    {
        return parent::initialize($parameters);
    }

    public function getData()
    {
        $this->validate('apiUsername', 'apiKey', 'amount', 'currency');

        $data = [];

        $data['type'] = $this->getType();
        $data['amount'] = $this->getAmount();
        $data['currency'] = $this->getCurrency();
        $data['callbackUrls'] = [];

        if ( $this->getStoreCard() ) {
            $data['storeCard'] = true;
        }

        if ( $this->getStoredCardIndicator() ) {
            $data['storedCardIndicator'] = $this->getStoredCardIndicator();
        }

        if ( $this->getRecurringExpiry() ) {
            $data['recurringExpiry'] = $this->getRecurringExpiry();
        }

        if ( $this->getRecurringFrequency() ) {
            $data['recurringFrequency'] = $this->getRecurringFrequency();
        }

        if ( $this->getToken() ) {
            $data['cardId'] = $this->getToken();
        }

        if ( is_array($this->getPaymentMethods()) ) {
            $data['methods'] = $this->getPaymentMethods();
        }

        if ( is_array($this->getCardTypes()) ) {
            $data['cardTypes'] = $this->getCardTypes();
        }

        if ( is_array($this->getMetadata()) ) {
            $data['metaData'] = $this->getMetadata();
        }

        if ( $this->getMerchantReference() ) {
            $data['merchantReference'] = $this->getMerchantReference();
        }

        if ( $this->getReturnUrl() ) {
            $data['callbackUrls']['approved'] = $this->getReturnUrl();
        }

        if ( $this->getDeclineUrl() ) {
            $data['callbackUrls']['declined'] = $this->getDeclineUrl();
        }

        if ( $this->getCancelUrl() ) {
            $data['callbackUrls']['cancelled'] = $this->getCancelUrl();
        }

        if ( $this->getNotifyUrl() ) {
            $data['notificationUrl'] = $this->getNotifyUrl();
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

        $httpResponse = $this->httpClient->request('POST', $this->getEndpoint('sessions'), $headers, json_encode($data));

        try {
            $responseData = json_decode($httpResponse->getBody()->getContents());
        } catch (\Exception $exception) {
            $responseData = [];
        }

        return $this->response = new PurchaseResponse($this, $responseData ?? []);
    }
}