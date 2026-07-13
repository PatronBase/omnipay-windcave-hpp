<?php

namespace Omnipay\WindcaveHpp\Message;

use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Common\Exception\InvalidRequestException;

abstract class BaseRequest extends AbstractRequest
{
    const ENDPOINT_TEST = 'https://uat.windcave.com/api/v1';
    const ENDPOINT_LIVE = 'https://sec.windcave.com/api/v1';

    // API Credentials
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

    protected function getAuthorization()
    {
        return base64_encode($this->getApiUsername() . ':' . $this->getApiKey());
    }

    public function setCreateToken($value)
    {
        return $this->setParameter('createToken', $value);
    }

    public function getCreateToken()
    {
        return $this->getParameter('createToken');
    }

    // Merchant Reference
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
     * Possible methods: ['card', 'account2account', 'alipay', 'applepay', 'googlepay', 'paypal', 'interac',
     * 'unionpay', 'oxipay', 'visacheckout', 'wechat']
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

    /**
     * @deprecated   Alias. Use standard `setCreateToken()` instead
     */
    public function setStoreCard($value)
    {
        return $this->setCreateToken($value);
    }

    /**
     * @deprecated   Alias. Use standard `getCreateToken()` instead
     */
    public function getStoreCard()
    {
        return $this->getCreateToken();
    }

    public function setStoredCardIndicator($value)
    {
        $options = [
            'single', 'recurringfixed', 'recurringvariable', 'installment',
            'recurringnoexpiry', 'recurringinitial', 'recurringfixedinitial', 'recurringvariableinitial',
            'installmentinitial', 'credentialonfileinitial',
            'unscheduledcredentialonfileinitial', 'credentialonfile', 'unscheduledcredentialonfile', 'incremental',
            'resubmission', 'reauthorisation', 'delayedcharges', 'noshow'
        ];

        if (! in_array($value, $options)) {
            throw new InvalidRequestException("Invalid option '{$value}' set for StoredCardIndicator.");
        }

        return $this->setParameter('storedCardIndicator', $value);
    }

    public function getStoredCardIndicator()
    {
        return $this->getParameter('storedCardIndicator');
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

        if (! in_array($value, $options)) {
            throw new InvalidRequestException("Invalid option '{$value}' set for RecurringFrequency.");
        }

        return $this->setParameter('recurringFrequency', $value);
    }

    public function getRecurringFrequency()
    {
        return $this->getParameter('recurringFrequency');
    }

    public function setRecurringExpiry($value)
    {
        // For scenarios where no expiry/end date is established i.e.,
        // subscription payments, the merchant web application should use "9999-12-31" as the value.

        return $this->setParameter('recurringExpiry', $value);
    }

    public function getRecurringExpiry()
    {
        return $this->getParameter('recurringExpiry');
    }

    // Endpoint selection based on test mode
    protected function getEndpoint($path = '')
    {
        $base = $this->getTestMode() ? self::ENDPOINT_TEST : self::ENDPOINT_LIVE;
        return $base . '/' . $path;
    }
}
