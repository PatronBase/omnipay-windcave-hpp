<?php

namespace Omnipay\WindcaveHpp;

use Omnipay\Common\AbstractGateway;
use Omnipay\WindcaveHpp\Message\AcceptNotification;
use Omnipay\WindcaveHpp\Message\CompletePurchaseRequest;
use Omnipay\WindcaveHpp\Message\PurchaseRequest;

/**
 * Windcave HPP Payment Gateway
 */
class Gateway extends AbstractGateway
{
    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return 'Windcave Hpp';
    }

    /**
     * Get default parameters
     *
     * @return array
     */
    public function getDefaultParameters()
    {
        return [];
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

    /**
     * Purchase
     *
     * @param array $parameters Parameters
     *
     * @return Omnipay\WindcaveHpp\Message\PurchaseRequest
     */
    public function purchase(array $parameters = [])
    {
        return $this->createRequest(
            PurchaseRequest::class,
            $parameters
        );
    }

    /**
     * Complete a purchase process
     *
     * @param array $parameters
     *
     * @return Omnipay\WindcaveHpp\Message\CompletePurchaseRequest
     */
    public function completePurchase(array $parameters = [])
    {
        return $this->createRequest(
            CompletePurchaseRequest::class,
            $parameters
        );
    }

    public function acceptNotification(array $parameters = [])
    {
        return $this->createRequest(
            AcceptNotification::class,
            $parameters
        )->send();
    }
}
