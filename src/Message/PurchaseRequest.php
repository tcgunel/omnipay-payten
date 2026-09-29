<?php

namespace Omnipay\Payten\Message;

use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Payten\Helpers\Helper;
use Omnipay\Payten\Models\Purchase3dRequestModel;
use Omnipay\Payten\Models\PurchaseRequestModel;
use Omnipay\Payten\Models\PurchaseResponseModel;
use Omnipay\Payten\Models\SessionTokenRequestModel;
use Omnipay\Payten\Traits\GettersSettersTrait;

class PurchaseRequest extends RemoteAbstractRequest
{
    use GettersSettersTrait;

    /**
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     * @throws \Omnipay\Common\Exception\InvalidCreditCardException
     */
    public function getData()
    {
        $this->validateAll();

        if ($this->getSecure()) {
            return $this->getSessionTokenData();
        }

        return $this->getNon3DData();
    }

    /**
     * MSU Direct Post flows start with a session token request; the card is
     * collected in the form posted to post/sale3d/{sessionToken} afterwards.
     *
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    protected function getSessionTokenData(): SessionTokenRequestModel
    {
        return new SessionTokenRequestModel([
            'MERCHANT' => $this->getMerchantId(),
            'MERCHANTUSER' => $this->getMerchantUser(),
            'MERCHANTPASSWORD' => $this->getMerchantPassword(),
            'MERCHANTPAYMENTID' => $this->getOrderNumber() ?? $this->getTransactionId(),
            'AMOUNT' => Helper::formatAmount($this->getAmount()),
            'CURRENCY' => $this->getCurrency() ?? 'TRY',
            'INSTALLMENTS' => (string) ($this->getInstallment() ?: '1'),
            'RETURNURL' => $this->getReturnUrl(),
            'CUSTOMER' => $this->getCustomer() ?? $this->get_card('getEmail') ?? '',
            'CUSTOMERNAME' => $this->getCustomerName() ?? $this->get_card('getName') ?? '',
            'CUSTOMEREMAIL' => $this->getCustomerEmail() ?? $this->get_card('getEmail') ?? '',
            'CUSTOMERIP' => $this->getCustomerIp() ?? $this->getClientIp() ?? '127.0.0.1',
            'CUSTOMERPHONE' => $this->get_card('getPhone'),
            'DEALERTYPENAME' => $this->getMerchantStorekey() ?: null,
            'ORDERITEMS' => $this->getOrderItems(),
        ]);
    }

    /**
     * Order items as a JSON array. MSU validates the item total against the
     * amount being charged, and some merchants refuse sessions without it.
     */
    protected function getOrderItems(): ?string
    {
        $items = $this->getItems();

        if ($items === null || count($items) === 0) {
            return null;
        }

        $orderItems = [];

        foreach ($items as $item) {
            $orderItems[] = [
                'productCode' => $item->getDescription() ?: $item->getName(),
                'name' => $item->getName(),
                'quantity' => (string) max(1, (int) $item->getQuantity()),
                'description' => $item->getName(),
                'amount' => Helper::formatAmount($item->getPrice()),
            ];
        }

        return json_encode($orderItems) ?: null;
    }

    /**
     * Non-3D sale, posted straight to the API.
     *
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    protected function getNon3DData(): PurchaseRequestModel
    {
        return new PurchaseRequestModel([
            'MERCHANTPAYMENTID' => $this->getOrderNumber() ?? $this->getTransactionId(),
            'MERCHANT' => $this->getMerchantId(),
            'MERCHANTUSER' => $this->getMerchantUser(),
            'MERCHANTPASSWORD' => $this->getMerchantPassword(),
            'DEALERTYPENAME' => $this->getMerchantStorekey(),
            'CUSTOMER' => $this->getCustomer() ?? $this->get_card('getEmail') ?? '',
            'CUSTOMERNAME' => $this->getCustomerName() ?? $this->get_card('getName') ?? '',
            'CUSTOMEREMAIL' => $this->getCustomerEmail() ?? $this->get_card('getEmail') ?? '',
            'CUSTOMERIP' => $this->getCustomerIp() ?? $this->getClientIp() ?? '127.0.0.1',
            'NAMEONCARD' => $this->get_card('getName'),
            'CARDPAN' => $this->get_card('getNumber'),
            'CARDEXPIRY' => Helper::formatExpiry(
                $this->get_card('getExpiryMonth'),
                $this->get_card('getExpiryYear')
            ),
            'CARDCVV' => $this->get_card('getCvv'),
            'AMOUNT' => Helper::formatAmount($this->getAmount()),
            'CURRENCY' => $this->getCurrency() ?? 'TRY',
            'INSTALLMENTS' => $this->getInstallment() ?? '1',
            'CAMPAIGNCODE' => $this->getCampaignCode(),
        ]);
    }

    /**
     * The card form for post/sale3d/{sessionToken}.
     */
    protected function get3DFormData(): Purchase3dRequestModel
    {
        $expiryYear = $this->get_card('getExpiryYear');

        if ($expiryYear !== null && strlen((string) $expiryYear) === 2) {
            $expiryYear = '20' . $expiryYear;
        }

        $expiryMonth = $this->get_card('getExpiryMonth');

        return new Purchase3dRequestModel([
            'cardOwner' => $this->get_card('getName'),
            'pan' => $this->get_card('getNumber'),
            'expiryMonth' => $expiryMonth !== null ? str_pad((string) $expiryMonth, 2, '0', STR_PAD_LEFT) : null,
            'expiryYear' => $expiryYear !== null ? (string) $expiryYear : null,
            'cvv' => $this->get_card('getCvv'),
            'cardName' => $this->get_card('getName'),
            'installmentCount' => (string) ($this->getInstallment() ?: '1'),
        ]);
    }

    /**
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     * @throws \Omnipay\Common\Exception\InvalidCreditCardException
     */
    protected function validateAll(): void
    {
        $this->validateSettings();

        $this->validate('card', 'amount');

        $this->getCard()->validate();

        if ($this->getSecure()) {
            $this->validate('returnUrl');
        }
    }

    /**
     * For 3D payments the session token is fetched first, then the response
     * carries the sale3d redirect form. For non-3D the sale is posted directly.
     *
     * @param  SessionTokenRequestModel|PurchaseRequestModel  $data
     * @return ResponseInterface|PurchaseResponse
     */
    public function sendData($data)
    {
        if ($data instanceof SessionTokenRequestModel) {
            return $this->send3DData($data);
        }

        $httpResponse = $this->httpClient->request(
            'POST',
            $this->getApiUrl(),
            [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Accept' => 'application/json',
            ],
            http_build_query($data->toArray(), '', '&')
        );

        return $this->createResponse($httpResponse);
    }

    /**
     * Fetch a secure session token and build the 3D form for it.
     */
    protected function send3DData(SessionTokenRequestModel $data): PurchaseResponse
    {
        $httpResponse = $this->httpClient->request(
            'POST',
            $this->getApiUrl(),
            [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Accept' => 'application/json',
            ],
            http_build_query($data->toArray(), '', '&')
        );

        $body = (string) $httpResponse->getBody();

        $decoded = json_decode($body, true);

        $sessionToken = is_array($decoded) ? ($decoded['sessionToken'] ?? null) : null;

        if (! is_array($decoded) || ($decoded['responseCode'] ?? null) !== '00' || empty($sessionToken)) {
            return $this->createResponse(new PurchaseResponseModel([
                'responseCode' => is_array($decoded) ? ($decoded['responseCode'] ?? '99') : '99',
                'responseMsg' => is_array($decoded) ? ($decoded['responseMsg'] ?? null) : null,
                'errorCode' => is_array($decoded) ? ($decoded['errorCode'] ?? null) : null,
                'errorMsg' => is_array($decoded) ? ($decoded['errorMsg'] ?? $body) : $body,
            ]));
        }

        $this->endpoint = $this->get3dUrl((string) $sessionToken);

        return $this->createResponse($this->get3DFormData());
    }

    protected function createResponse($data): PurchaseResponse
    {
        return $this->response = new PurchaseResponse($this, $data);
    }
}
