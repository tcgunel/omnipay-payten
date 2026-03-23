<?php

namespace Omnipay\Payten\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Payten\Models\CompletePurchaseResponseModel;

class CompletePurchaseResponse extends AbstractResponse
{
	protected $response;

	protected $request;

	public function __construct(RequestInterface $request, $data)
	{
		parent::__construct($request, $data);

		$this->request = $request;

		$this->response = $data;
	}

	public function isSuccessful(): bool
	{
		return $this->response->responseCode === '00';
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
		return $this->response->pgTranId;
	}

	public function getTransactionId(): ?string
	{
		return $this->response->merchantPaymentId;
	}

	public function getData(): CompletePurchaseResponseModel
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
