<?php

namespace Omnipay\WindcaveHpp\Message;

use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

/**
 * Windcave HPP Complete Purchase Response
 */
class CompletePurchaseResponse extends AbstractResponse
{
    /**
     * Constructor
     *
     * @param RequestInterface $request the initiating request.
     * @param mixed $data
     *
     * @throws InvalidResponseException If merchant data or order number is missing, or signature does not match
     */
    public function __construct(RequestInterface $request, $data)
    {
        parent::__construct($request, $data);
    }

    public function isSuccessful()
    {
        $transaction = $this->getTransactionResult();

        return (
            $transaction &&
            ($transaction['authorised'] ?? false) &&
            ( strtoupper($transaction['responseText'] ?? '') ) === 'APPROVED'
        ) ?? false;
    }

    protected function getTransactionResult()
    {
        return $this->getData()['transactions'][0] ?? [];
    }

    public function getTransactionId()
    {
        return $this->getTransactionResult()['merchantReference'] ?? '';
    }

    public function getTransactionReference()
    {
        return $this->getTransactionResult()['id'] ?? '';
    }

    public function getTransactionType()
    {
        return $this->getTransactionResult()['type'] ?? '';
    }

    public function getMessage()
    {
        return $this->getResponseText() ?? '';
    }

    public function getResponseText()
    {
        $transaction = $this->getTransactionResult();

        return $transaction['responseText'] ?? '';
    }

    public function getSessionId()
    {
        return $this->getTransactionResult()['sessionId'] ?? '';
    }

    public function getCard()
    {
        return $this->getTransactionResult()['card'] ?? '';
    }

    public function getCardNumber()
    {
        return $this->getCard()['cardNumber'] ?? '';
    }

    public function getCardHolderName()
    {
        return $this->getCard()['cardHolderName'] ?? '';
    }

    public function getCardExpiry()
    {
        return $this->getCardExpiryMonth() . '/' . $this->getCardExpiryYear();
    }

    public function getCardExpiryYear()
    {
        return $this->getCard()['dateExpiryYear'] ?? '';
    }

    public function getCardExpiryMonth()
    {
        return $this->getCard()['dateExpiryMonth'] ?? '';
    }

    public function getCardType()
    {
        return $this->getCard()['type'] ?? '';
    }

    public function getCardReference()
    {
        return $this->getCard()['id'] ?? null;
    }
}
