<?php

namespace Omnipay\WindcaveHpp\Message;

use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * Windcave HPP Redirect Response
 */
class PurchaseResponse extends AbstractResponse implements RedirectResponseInterface {

    public function isSuccessful()
    {
        return false;
    }

    public function isRedirect()
    {
        return true;
    }

    public function getRedirectUrl()
    {
        foreach ( $this->data->links ?? [] as $link ) {
            if ( $link->rel === 'hpp' ) {
                return $link->href;
            }
        }

        throw new InvalidResponseException('Invalid response from windcave server');
    }

    public function getRedirectData()
    {
        return [];
    }
}