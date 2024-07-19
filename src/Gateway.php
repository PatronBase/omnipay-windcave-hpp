<?php

namespace Omnipay\WindcaveHpp;

use Omnipay\Common\AbstractGateway;
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
        return 'Windcave HPP';
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

    /* @todo add other functions */

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
    public function completePurchase(array $parameters = array())
    {
        return $this->createRequest(CompletePurchaseRequest::class, $parameters);
    }
}
