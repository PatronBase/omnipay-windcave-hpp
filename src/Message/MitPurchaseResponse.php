<?php

namespace Omnipay\WindcaveHpp\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * Windcave MIT Purchase Response.
 *
 * Parses a direct /transactions response (flat transaction object, no session wrapper).
 * isRedirect() is always false — MIT charges never require cardholder interaction.
 */
class MitPurchaseResponse extends AbstractResponse
{
    public function isSuccessful(): bool
    {
        return ($this->data->authorised ?? false) === true;
    }

    public function isRedirect(): bool
    {
        return false;
    }

    public function getCard(): array
    {
        return (array) ($this->data->card ?? []);
    }

    public function getCardReference(): ?string
    {
        return $this->data->card->id ?? null;
    }

    public function getTransactionReference(): ?string
    {
        return $this->data->id ?? null;
    }

    public function getMessage(): ?string
    {
        return $this->data->responseText ?? null;
    }
}