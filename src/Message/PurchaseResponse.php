<?php

namespace Omnipay\WindcaveHpp\Message;

use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * Windcave HPP Purchase Response.
 *
 * Always represents an HPP session creation result from POST /sessions.
 * MIT (stored card) charges go through MitPurchaseResponse instead.
 */
class PurchaseResponse extends AbstractResponse implements RedirectResponseInterface
{
    public function isSuccessful(): bool
    {
        return false;
    }

    public function isRedirect(): bool
    {
        foreach ($this->data->links ?? [] as $link) {
            if ($link->rel === 'hpp') {
                return true;
            }
        }
        return false;
    }

    public function getRedirectUrl()
    {
        foreach ($this->data->links ?? [] as $link) {
            if ($link->rel === 'hpp') {
                return $link->href;
            }
        }

        throw new InvalidResponseException('Invalid response from windcave server');
    }

    public function getRedirectData()
    {
        return [];
    }

    public function getTransactionReference(): ?string
    {
        return null;
    }

    public function getMessage(): ?string
    {
        return null;
    }

    public function getCard(): array
    {
        return [];
    }

    public function getCardReference(): ?string
    {
        return null;
    }
}
