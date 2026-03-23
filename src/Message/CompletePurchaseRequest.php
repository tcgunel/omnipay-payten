<?php

namespace Omnipay\Payten\Message;

use Omnipay\Payten\Models\CompletePurchaseResponseModel;
use Omnipay\Payten\Traits\GettersSettersTrait;

class CompletePurchaseRequest extends RemoteAbstractRequest
{
	use GettersSettersTrait;

	/**
	 * @throws \Omnipay\Common\Exception\InvalidRequestException
	 */
	public function getData()
	{
		// completePurchase parses the callback data from 3D redirect.
		// The callback POST data should be passed as request parameters.
		$rawData = $this->httpRequest->request->all();

		if (empty($rawData)) {
			$rawData = $this->httpRequest->query->all();
		}

		$data = new CompletePurchaseResponseModel([
			'responseCode'    => $rawData['responseCode'] ?? null,
			'responseMsg'     => $rawData['responseMsg'] ?? null,
			'errorMsg'        => $rawData['errorMsg'] ?? null,
			'errorCode'       => $rawData['errorCode'] ?? null,
			'merchantPaymentId' => $rawData['merchantPaymentId'] ?? $rawData['MERCHANTPAYMENTID'] ?? null,
			'pgTranId'        => $rawData['pgTranId'] ?? null,
			'mdStatus'        => $rawData['mdStatus'] ?? null,
			'rawData'         => $rawData,
		]);

		return $data;
	}

	/**
	 * @param CompletePurchaseResponseModel $data
	 * @return CompletePurchaseResponse
	 */
	public function sendData($data)
	{
		return $this->response = new CompletePurchaseResponse($this, $data);
	}

	protected function createResponse($data): void
	{
	}
}
