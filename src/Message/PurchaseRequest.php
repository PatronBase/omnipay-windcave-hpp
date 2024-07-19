<?php

namespace Omnipay\WindcaveHpp\Message;

use Illuminate\Http\Request;
use Omnipay\Common\Message\AbstractRequest;

/**
 * Windcave HPP Purchase Request
 */

class PurchaseRequest extends AbstractRequest {

    const endpointTest = '';
    const endpointLive = '';

    public function initialize(array $parameters = []) 
    {
        return parent::initialize($parameters);
    }

    /* @todo add other purchase field functions

    /**
     * Get endpoint
     *
     * Returns endpoint depending on test mode
     *
     * @access protected
     * @return string
     */
    protected function getEndpoint() 
    {
        return ($this->getTestMode() ? self::endpointTest : self::endpointLive);
    }
}