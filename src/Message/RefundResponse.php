<?php

namespace Omnipay\WindcaveHpp\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

class RefundResponse extends AbstractResponse
{
    public function isSuccessful()
    {
        return (
            $this->data &&
            ($this->data->authorised ?? false) &&
            ( strtoupper($this->data->type ?? '') ) === 'REFUND' &&
            ( strtoupper($this->data->responseText ?? '') ) === 'APPROVED'
        ) ?? false;
    }

    public function getMessage()
    {
        return $this->data->responseText ?? null;
    }

    public function getTransactionReference()
    {
        return $this->data->id ?? null;
    }
}