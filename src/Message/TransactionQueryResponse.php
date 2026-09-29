<?php

namespace Omnipay\Payten\Message;

use JsonException;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Payten\Models\TransactionQueryResponseModel;
use Psr\Http\Message\ResponseInterface;

class TransactionQueryResponse extends AbstractResponse
{
    protected $response;

    protected $request;

    public function __construct(RequestInterface $request, $data)
    {
        parent::__construct($request, $data);

        $this->request = $request;

        $this->response = $data;

        if ($data instanceof ResponseInterface) {

            $body = (string) $data->getBody();

            try {

                $decoded = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

                $this->response = new TransactionQueryResponseModel($decoded);

            } catch (JsonException $e) {

                $this->response = new TransactionQueryResponseModel([
                    'responseCode' => '99',
                    'errorMsg' => $body,
                ]);

            }

        }
    }

    public function isSuccessful(): bool
    {
        if ($this->response->responseCode !== '00') {
            return false;
        }

        $transactions = $this->response->transactionList;

        if (! is_array($transactions) || $transactions === []) {
            return false;
        }

        $first = $transactions[0] ?? [];

        return is_array($first) && (string) ($first['pgTranReturnCode'] ?? '') === '00';
    }

    public function getMessage(): ?string
    {
        return $this->response->errorMsg ?: $this->response->responseMsg;
    }

    public function getCode(): ?string
    {
        return $this->response->responseCode;
    }

    public function getTransactionReference(): ?string
    {
        if (! empty($this->response->pgTranId)) {
            return $this->response->pgTranId;
        }

        $first = $this->response->transactionList[0] ?? [];

        return is_array($first) ? ($first['pgTranId'] ?? null) : null;
    }

    public function getTransactionId(): ?string
    {
        return $this->response->merchantPaymentId;
    }

    public function getData(): TransactionQueryResponseModel
    {
        return $this->response;
    }

    public function getRedirectData()
    {
        return null;
    }

    public function getRedirectUrl(): string
    {
        return '';
    }
}
