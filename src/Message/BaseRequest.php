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

    // Merchant Reference
    public function setMerchantReference($value)
    {
        return $this->setParameter('merchantReference', $value);
    }

    public function getMerchantReference()
    {
        return $this->getParameter('merchantReference');
    }

    // Store Card Options
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
            'unscheduledcredentialonfileinitial', 'credentialonfile', 'unscheduledcredentialonfile',
            'incremental', 'resubmission', 'reauthorisation', 'delayedcharges', 'noshow'
        ];

        if (!in_array($value, $options)) {
            throw new InvalidRequestException("Invalid option '{$value}' set for StoredCardIndicator.");
        }

        return $this->setParameter('storeCardIndicator', $value);
    }

    public function getStoredCardIndicator()
    {
        return $this->getParameter('storeCardIndicator');
    }

    // Metadata
    public function setMetadata($data)
    {
        return $this->setParameter('metaData', $data);
    }

    public function getMetadata()
    {
        return $this->getParameter('metaData');
    }

    // Recurring Frequency
    public function setRecurringFrequency($value)
    {
        $options = [
            'daily', 'weekly', 'every2weeks', 'every4weeks',
            'monthly', 'monthly28th', 'monthlylastcalendarday',
            'monthlysecondlastcalendarday', 'monthlythirdlastcalendarday',
            'twomonthly', 'threemonthly', 'fourmonthly', 'sixmonthly', 'annually'
        ];

        if (!in_array($value, $options)) {
            throw new InvalidRequestException("Invalid option '{$value}' set for RecurringFrequency.");
        }

        return $this->setParameter('recurringFrequency', $value);
    }

    public function getRecurringFrequency()
    {
        return $this->getParameter('recurringFrequency');
    }

    // Endpoint selection based on test mode
    protected function getEndpoint($path = '')
    {
        $base = $this->getTestMode() ? self::ENDPOINT_TEST : self::ENDPOINT_LIVE;
        return $base . '/' . $path;
    }
}