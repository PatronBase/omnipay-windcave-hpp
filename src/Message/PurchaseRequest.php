<?php

namespace Omnipay\WindcaveHpp\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Windcave HPP Purchase Request
 */
class PurchaseRequest extends BaseRequest
{
    public function getData()
    {
        $this->validate('apiUsername', 'apiKey', 'amount', 'currency', 'card');
        $card = $this->getCard();

        $data = [
            'type' => $this->getType(),
            'amount' => $this->getAmount(),
            'currency' => $this->getCurrency(),
            'callbackUrls' => [],
            'customer' => [
                'email' => null,
                'shipping' => [],
                'billing' => []
            ],
        ];

        if ((bool) $this->getCreateToken()) {
            $data['storeCard'] = true;
        }

        if ($this->getStoredCardIndicator()) {
            $data['storedCardIndicator'] = $this->getStoredCardIndicator();
        }

        if ($this->getRecurringExpiry()) {
            $data['recurringExpiry'] = $this->getRecurringExpiry();
        }

        if ($this->getRecurringFrequency()) {
            $data['recurringFrequency'] = $this->getRecurringFrequency();
        }

        if ($this->getToken() || $this->getCardReference()) {
            $data['cardId'] = $this->getToken() ?? $this->getCardReference();
        }

        if (is_array($this->getPaymentMethods())) {
            $data['methods'] = $this->getPaymentMethods();
        }

        if (is_array($this->getCardTypes())) {
            $data['cardTypes'] = $this->getCardTypes();
        }

        if (is_array($this->getMetadata())) {
            $data['metaData'] = $this->getMetadata();
        }

        $merchantReference = $this->getMerchantReference() ?? $this->getDescription();

        if ($merchantReference) {
            $data['merchantReference'] = $merchantReference;
        }

        if ($this->getReturnUrl()) {
            $data['callbackUrls']['approved'] = $this->getReturnUrl();
        }

        if ($this->getDeclineUrl()) {
            $data['callbackUrls']['declined'] = $this->getDeclineUrl();
        }

        if ($this->getCancelUrl()) {
            $data['callbackUrls']['cancelled'] = $this->getCancelUrl();
        }

        if ($this->getNotifyUrl()) {
            $data['notificationUrl'] = $this->getNotifyUrl();
        }

        if ($card->getEmail()) {
            $data['customer']['email'] = $card->getEmail();
        }

        if ($card->getBillingName()) {
            $data['customer']['billing']['name'] = $card->getBillingName();
        }

        if ($card->getBillingAddress1()) {
            $data['customer']['billing']['address1'] = $card->getBillingAddress1();
        }

        if ($card->getBillingAddress2()) {
            $data['customer']['billing']['address2'] = $card->getBillingAddress2();
        }

        if ($card->getBillingCity()) {
            $data['customer']['billing']['city'] = $card->getBillingCity();
        }

        if ($card->getBillingCountry()) {
            $data['customer']['billing']['countryCode'] = $card->getBillingCountry();
        }

        if ($card->getBillingPostcode()) {
            $data['customer']['billing']['postalCode'] = $card->getBillingPostcode();
        }

        if ($card->getBillingPhone()) {
            $data['customer']['billing']['phoneNumber'] = $card->getBillingPhone();
        }

        if ($card->getBillingState()) {
            $data['customer']['billing']['state'] = $card->getBillingState();
        }

        if ($card->getShippingName()) {
            $data['customer']['shipping']['name'] = $card->getShippingName();
        }

        if ($card->getShippingAddress1()) {
            $data['customer']['shipping']['address1'] = $card->getShippingAddress1();
        }

        if ($card->getShippingAddress2()) {
            $data['customer']['shipping']['address2'] = $card->getShippingAddress2();
        }

        if ($card->getShippingCity()) {
            $data['customer']['shipping']['city'] = $card->getShippingCity();
        }

        if ($card->getShippingCountry()) {
            $data['customer']['shipping']['countryCode'] = $card->getShippingCountry();
        }

        if ($card->getShippingPostcode()) {
            $data['customer']['shipping']['postalCode'] = $card->getShippingPostcode();
        }

        if ($card->getShippingPhone()) {
            $data['customer']['shipping']['phoneNumber'] = $card->getShippingPhone();
        }

        if ($card->getShippingState()) {
            $data['customer']['shipping']['state'] = $card->getShippingState();
        }

        if (empty($data['customer']['shipping'])) {
            unset($data['customer']['shipping']);
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

        $httpResponse = $this->httpClient->request(
            'POST',
            $this->getEndpoint('sessions'),
            $headers,
            json_encode($data)
        );

        try {
            $responseData = json_decode($httpResponse->getBody()->getContents());
        } catch (\Exception $exception) {
            $responseData = [];
        }

        return $this->response = new PurchaseResponse($this, $responseData ?? []);
    }
}
