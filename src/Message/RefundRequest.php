<?php

namespace Omnipay\Payten\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Payten\Helpers\Helper;
use Omnipay\Payten\Models\RefundRequestModel;
use Omnipay\Payten\Traits\GettersSettersTrait;

class RefundRequest extends RemoteAbstractRequest
{
	use GettersSettersTrait;

	/**
	 * @throws InvalidRequestException
	 */
	public function getData(): RefundRequestModel
	{
		$this->validateAll();

		$data = new RefundRequestModel([
			'MERCHANT'          => $this->getMerchantId(),
			'MERCHANTUSER'      => $this->getMerchantUser(),
			'MERCHANTPASSWORD'  => $this->getMerchantPassword(),
			'MERCHANTPAYMENTID' => $this->getOrderNumber() ?? $this->getTransactionId(),
			'AMOUNT'            => Helper::formatAmount($this->getAmount()),
			'CURRENCY'          => $this->getCurrency() ?? 'TRY',
		]);

		return $data;
	}

	/**
	 * @throws InvalidRequestException
	 */
	protected function validateAll(): void
	{
		$this->validateSettings();

		$this->validate("amount");
	}

	protected function createResponse($data): RefundResponse
	{
		return $this->response = new RefundResponse($this, $data);
	}

	/**
	 * @param RefundRequestModel $data
	 * @return ResponseInterface|RefundResponse
	 */
	public function sendData($data)
	{
		$httpResponse = $this->httpClient->request(
			'POST',
			$this->getApiUrl(),
			[
				'Content-Type' => 'application/x-www-form-urlencoded',
				'Accept'       => 'application/json',
			],
			http_build_query($data->toArray(), '', '&')
		);

		return $this->createResponse($httpResponse);
	}
}
