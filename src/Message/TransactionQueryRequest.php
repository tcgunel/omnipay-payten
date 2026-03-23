<?php

namespace Omnipay\Payten\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Payten\Models\TransactionQueryRequestModel;
use Omnipay\Payten\Traits\GettersSettersTrait;

class TransactionQueryRequest extends RemoteAbstractRequest
{
    use GettersSettersTrait;

    /**
     * @throws InvalidRequestException
     */
    public function getData(): TransactionQueryRequestModel
    {
        $this->validateAll();

        $data = new TransactionQueryRequestModel([
            'MERCHANT' => $this->getMerchantId(),
            'MERCHANTUSER' => $this->getMerchantUser(),
            'MERCHANTPASSWORD' => $this->getMerchantPassword(),
            'MERCHANTPAYMENTID' => $this->getOrderNumber() ?? $this->getTransactionId(),
        ]);

        return $data;
    }

    /**
     * @throws InvalidRequestException
     */
    protected function validateAll(): void
    {
        $this->validateSettings();
    }

    protected function createResponse($data): TransactionQueryResponse
    {
        return $this->response = new TransactionQueryResponse($this, $data);
    }

    /**
     * @param TransactionQueryRequestModel $data
     * @return ResponseInterface|TransactionQueryResponse
     */
    public function sendData($data)
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

        return $this->createResponse($httpResponse);
    }
}
