<?php

namespace Omnipay\Payten\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Payten\Helpers\Helper;
use Omnipay\Payten\Models\InstallmentQueryRequestModel;
use Omnipay\Payten\Traits\GettersSettersTrait;

class InstallmentQueryRequest extends RemoteAbstractRequest
{
	use GettersSettersTrait;

	/**
	 * @throws InvalidRequestException
	 */
	public function getData(): InstallmentQueryRequestModel
	{
		$this->validateAll();

		$cardPan = $this->get_card('getNumber');

		// Only first 6 digits (BIN)
		if ($cardPan && strlen($cardPan) > 6) {
			$cardPan = substr($cardPan, 0, 6);
		}

		$data = new InstallmentQueryRequestModel([
			'MERCHANT'         => $this->getMerchantId(),
			'MERCHANTUSER'     => $this->getMerchantUser(),
			'MERCHANTPASSWORD' => $this->getMerchantPassword(),
			'CARDPAN'          => $cardPan,
			'CURRENCY'         => $this->getCurrency() ?? 'TRY',
			'AMOUNT'           => $this->getAmount() ? Helper::formatAmount($this->getAmount()) : null,
		]);

		return $data;
	}

	/**
	 * @throws InvalidRequestException
	 */
	protected function validateAll(): void
	{
		$this->validateSettings();

		$this->validate("card");
	}

	protected function createResponse($data): InstallmentQueryResponse
	{
		return $this->response = new InstallmentQueryResponse($this, $data);
	}

	/**
	 * @param InstallmentQueryRequestModel $data
	 * @return ResponseInterface|InstallmentQueryResponse
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
