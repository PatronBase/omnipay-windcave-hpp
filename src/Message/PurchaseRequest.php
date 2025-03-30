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

    // Purchase type, language, and payment method specifics
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
     * @param array $list
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

        foreach ($list as $method) {
            if (!in_array($method, $options)) {
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

    public function getData()
    {
        $this->validate('apiUsername', 'apiKey', 'amount', 'currency');

        $data = [];
        $data['type'] = $this->getType();
        $data['amount'] = $this->getAmount();
        $data['currency'] = $this->getCurrency();
        $data['storeCard'] = (bool)$this->getStoreCard() ?? false;
        $data['callbackUrls'] = [];

        if (is_array($this->getPaymentMethods())) {
            $data['methods'] = $this->getPaymentMethods();
        }

        if (is_array($this->getCardTypes())) {
            $data['cardTypes'] = $this->getCardTypes();
        }

        if (is_array($this->getMetadata())) {
            $data['metaData'] = $this->getMetadata();
        }

        if ($this->getMerchantReference()) {
            $data['merchantReference'] = $this->getMerchantReference();
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