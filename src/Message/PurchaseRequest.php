<?php

namespace Omnipay\WindcaveHpp\Message;

use Illuminate\Http\Request;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\AbstractRequest;
use Symfony\Component\HttpFoundation\ParameterBag;

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

    /**
     * @param $list
     * Possible methods: ['card', 'account2account', 'alipay', 'applepay', 'googlepay', 'paypal', 'interac', 'unionpay', 'oxipay', 'visacheckout', 'wechat']
     *
     * @return PurchaseRequest
     */
    public function setPaymentMethods($list)
    {
        $options = [
            'card', 'account2account', 'alipay', 'applepay',
            'googlepay', 'paypal', 'interac', 'unionpay',
            'oxipay', 'visacheckout', 'wechat'
        ];

        foreach ( $list as $method ) {
            if ( !in_array($method, $options) ) {
                throw new InvalidRequestException("Unknown payment method: {$method}");
            }
        }

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

    public function setStoredCardIndicator($value)
    {
        $options = [
            'single', 'recurringfixed', 'recurringvariable', 'installment',
            'recurringnoexpiry', 'recurringinitial', 'installmentinitial', 'credentialonfileinitial',
            'unscheduledcredentialonfileinitial', 'credentialonfile', 'unscheduledcredentialonfile', 'incremental',
            'resubmission', 'reauthorisation', 'delayedcharges', 'noshow'
        ];

        if ( ! in_array($value, $options) ) {
            throw new InvalidRequestException("Invalid option '{$value}' set for StoredCardIndicator.");
        }

        return $this->setParameter('storeCardIndicator', $value);
    }

    public function getStoredCardIndicator()
    {
        return $this->getParameter('storeCardIndicator');
    }

    public function setMetadata($data)
    {
        return $this->setParameter('metaData', $data);
    }

    public function getMetadata()
    {
        return $this->getParameter('metaData');
    }

    public function setRecurringFrequency($value)
    {
        $options = [
            'daily', 'weekly', 'every2weeks', 'every4weeks',
            'monthly', 'monthly28th', 'monthlylastcalendarday',
            'monthlysecondlastcalendarday', 'monthlythirdlastcalendarday',
            'twomonthly', 'threemonthly', 'fourmonthly', 'sixmonthly', 'annually'
        ];

        if ( ! in_array($value, $options) ) {
            throw new InvalidRequestException("Invalid option '{$value}' set for RecurringFrequency.");
        }

        return $this->setParameter('recurringFrequency', $value);
    }

    public function getRecurringFrequency()
    {
        return $this->getParameter('recurringFrequency');
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