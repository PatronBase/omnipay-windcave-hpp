<?php

namespace Omnipay\WindcaveHpp\Message;

/**
 * Windcave MIT Purchase Request.
 *
 * Used for merchant-initiated transactions (e.g. cron recurring charges) where
 * a stored cardId is charged directly via the /transactions endpoint.
 * Unlike PurchaseRequest (/sessions), this never requires an HPP redirect.
 */
class MitPurchaseRequest extends BaseRequest
{
    public function getData()
    {
        $this->validate('apiUsername', 'apiKey', 'amount', 'currency');

        $data = [
            'type'     => 'purchase',
            'amount'   => $this->getAmount(),
            'currency' => $this->getCurrency(),
            'cardId'   => $this->getToken() ?? $this->getCardReference(),
        ];

        if ($this->getStoredCardIndicator()) {
            $data['storedCardIndicator'] = $this->getStoredCardIndicator();
        }

        if ($this->getRecurringExpiry()) {
            $data['recurringExpiry'] = $this->getRecurringExpiry();
        }

        if ($this->getRecurringFrequency()) {
            $data['recurringFrequency'] = $this->getRecurringFrequency();
        }

        $merchantReference = $this->getMerchantReference() ?? $this->getDescription();
        if ($merchantReference) {
            $data['merchantReference'] = $merchantReference;
        }

        if ($this->getNotifyUrl()) {
            $data['notificationUrl'] = $this->getNotifyUrl();
        }

        return $data;
    }

    public function sendData($data)
    {
        $headers = [
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
            'Authorization' => 'Basic ' . $this->getAuthorization(),
        ];

        $httpResponse = $this->httpClient->request(
            'POST',
            $this->getEndpoint('transactions'),
            $headers,
            json_encode($data)
        );

        try {
            $responseData = json_decode($httpResponse->getBody()->getContents());
        } catch (\Exception $exception) {
            $responseData = null;
        }

        return $this->response = new MitPurchaseResponse($this, $responseData);
    }
}