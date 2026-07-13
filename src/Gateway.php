<?php

namespace Omnipay\WindcaveHpp;

use Omnipay\Common\AbstractGateway;
use Omnipay\WindcaveHpp\Message\AcceptNotification;
use Omnipay\WindcaveHpp\Message\CompletePurchaseRequest;
use Omnipay\WindcaveHpp\Message\MitPurchaseRequest;
use Omnipay\WindcaveHpp\Message\PurchaseRequest;
use Omnipay\WindcaveHpp\Message\RefundRequest;

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
     * Routes to MitPurchaseRequest (/transactions) when a stored card token is present,
     * otherwise creates a standard HPP session via PurchaseRequest (/sessions).
     *
     * @param array $parameters Parameters
     *
     * @return \Omnipay\WindcaveHpp\Message\PurchaseRequest|\Omnipay\WindcaveHpp\Message\MitPurchaseRequest
     */
    public function purchase(array $parameters = [])
    {
        if (!empty($parameters['token']) || !empty($parameters['cardReference'])) {
            return $this->createRequest(MitPurchaseRequest::class, $parameters);
        }

        return $this->createRequest(PurchaseRequest::class, $parameters);
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

    /**
     * Create a card (add card only, delegates to purchase for HPP)
     *
     * @param array $parameters Parameters
     *
     * @return Omnipay\WindcaveHpp\Message\PurchaseRequest
     */
    public function createCard(array $parameters = [])
    {
        return $this->createRequest(
            PurchaseRequest::class,
            $parameters
        );
    }

    /**
     * Complete a create card process
     *
     * @param array $parameters Parameters
     *
     * @return Omnipay\WindcaveHpp\Message\CompletePurchaseRequest
     */
    public function completeCreateCard(array $parameters = [])
    {
        return $this->createRequest(
            CompletePurchaseRequest::class,
            $parameters
        );
    }

    /**
     * Refund
     *
     * @param array $parameters Parameters
     *
     * @return Omnipay\WindcaveHpp\Message\RefundRequest
     */
    public function refund(array $parameters = [])
    {
        return $this->createRequest(
            RefundRequest::class,
            $parameters
        );
    }
}
