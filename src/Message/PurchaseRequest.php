<?php

namespace Omnipay\WindcaveHpp\Message;

use Illuminate\Http\Request;
use Omnipay\Common\Message\AbstractRequest;

/**
 * Windcave HPP Purchase Request
 */

class PurchaseRequest extends AbstractRequest {

    const endpointTest = 'https://uat.windcave.com/api/v1';
    const endpointLive = 'https://sec.windcave.com/api/v1';

    public function initialize(array $parameters = []) 
    {
        return parent::initialize($parameters);
    }

    public function setApiUsername($value)
    {
        return $this->setParameter('apiUsername', $value);
    }

    public function getApiUsername()
    {
        return $this->getParameter('apiUsername');
    }

    public function setApiKey($value)
    {
        return $this->setParameter('apiKey', $value);
    }

    public function getApiKey()
    {
        return $this->getParameter('apiKey');
    }

    public function setMerchantReference($value)
    {
        return $this->setParameter('merchantReference', $value);
    }

    public function getMerchantReference()
    {
        return $this->getParameter('merchantReference');
    }

    public function setType($value)
    {
        return $this->setParameter('type', $value);
    }

    public function getType()
    {
        return $this->getParameter('type') ?? 'purchase';
    }

    public function setLanguage($value)
    {
        return $this->setParameter('language', $value);
    }

    public function getLanguage()
    {
        return $this->getParameter('language') ?? 'en';
    }

    public function setPaymentMethods($list)
    {
        return $this->setParameter('paymentMethods', $list);
    }

    public function getPaymentMethods()
    {
        return $this->getParameter('paymentMethods');
    }

    public function setCardTypes($list)
    {
        return $this->setParameter('cardTypes', $list);
    }

    public function getCardTypes()
    {
        return $this->getParameter('cardTypes');
    }

    public function setExpiresAt($value)
    {
        return $this->setParameter('expiresAt', $value);
    }

    public function getExpiresAt()
    {
        return $this->getParameter('expiresAt');
    }

    public function setDeclineUrl($url)
    {
        return $this->setParameter('declineUrl', $url);
    }

    public function getDeclineUrl()
    {
        return $this->getParameter('declineUrl');
    }

    public function setStoreCard($value)
    {
        return $this->setParameter('storeCard', $value);
    }

    public function getStoreCard()
    {
        return $this->getParameter('storeCard');
    }

    public function getData()
    {
        $this->validate('apiUsername', 'apiKey', 'amount', 'currency');

        $data = [];

        $data['type'] = $this->getType();
        $data['amount'] = $this->getAmount();
        $data['currency'] = $this->getCurrency();
        $data['storeCard'] = (bool) $this->getStoreCard() ?? false;
        $data['callbackUrls'] = [];

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
//        echo '<pre>'; print_r($data); echo '</pre>';

        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . $this->getAuthorization()
        ];

        $httpResponse = $this->httpClient->request('POST', $this->getEndpoint('sessions'), $headers, json_encode($data));

        try {
            $responseData = json_decode($httpResponse->getBody()->getContents());
        } catch (\Exception $exception) {
            $responseData = [];
        }

        return $this->response = new PurchaseResponse($this, $responseData ?? []);
    }

    /**
     * Get endpoint
     *
     * Returns endpoint depending on test mode
     *
     * @access protected
     * @return string
     */
    protected function getEndpoint($path = '')
    {
        return ($this->getTestMode() ? self::endpointTest : self::endpointLive) . '/' . $path;
    }

    protected function getAuthorization()
    {
        return base64_encode($this->getApiUsername().':'.$this->getApiKey());
    }
}