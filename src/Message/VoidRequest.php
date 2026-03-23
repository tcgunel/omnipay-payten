<?php

namespace Omnipay\Payten\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Payten\Models\VoidRequestModel;
use Omnipay\Payten\Traits\GettersSettersTrait;

class VoidRequest extends RemoteAbstractRequest
{
	use GettersSettersTrait;

	/**
	 * @throws InvalidRequestException
	 */
	public function getData(): VoidRequestModel
	{
		$this->validateAll();

		$data = new VoidRequestModel([
			'MERCHANT'          => $this->getMerchantId(),
			'MERCHANTUSER'      => $this->getMerchantUser(),
			'MERCHANTPASSWORD'  => $this->getMerchantPassword(),
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

	protected function createResponse($data): VoidResponse
	{
		return $this->response = new VoidResponse($this, $data);
	}

	/**
	 * @param VoidRequestModel $data
	 * @return ResponseInterface|VoidResponse
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
