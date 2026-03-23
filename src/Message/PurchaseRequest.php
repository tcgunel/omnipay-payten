<?php

namespace Omnipay\Payten\Message;

use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Payten\Helpers\Helper;
use Omnipay\Payten\Models\Purchase3dRequestModel;
use Omnipay\Payten\Models\PurchaseRequestModel;
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

		$is3d = $this->getSecure();

		$modelClass = $is3d ? Purchase3dRequestModel::class : PurchaseRequestModel::class;

		$fields = [
			'MERCHANTPAYMENTID' => $this->getOrderNumber() ?? $this->getTransactionId(),
			'MERCHANT'          => $this->getMerchantId(),
			'MERCHANTUSER'      => $this->getMerchantUser(),
			'MERCHANTPASSWORD'  => $this->getMerchantPassword(),
			'DEALERTYPENAME'    => $this->getMerchantStorekey(),
			'CUSTOMER'          => $this->getCustomer() ?? $this->get_card('getEmail') ?? '',
			'CUSTOMERNAME'      => $this->getCustomerName() ?? $this->get_card('getName') ?? '',
			'CUSTOMEREMAIL'     => $this->getCustomerEmail() ?? $this->get_card('getEmail') ?? '',
			'CUSTOMERIP'        => $this->getCustomerIp() ?? $this->getClientIp() ?? '127.0.0.1',
			'NAMEONCARD'        => $this->get_card('getName'),
			'CARDPAN'           => $this->get_card('getNumber'),
			'CARDEXPIRY'        => Helper::formatExpiry(
				$this->get_card('getExpiryMonth'),
				$this->get_card('getExpiryYear')
			),
			'CARDCVV'           => $this->get_card('getCvv'),
			'AMOUNT'            => Helper::formatAmount($this->getAmount()),
			'CURRENCY'          => $this->getCurrency() ?? 'TRY',
			'INSTALLMENTS'      => $this->getInstallment() ?? '1',
			'CAMPAIGNCODE'      => $this->getCampaignCode(),
		];

		if ($is3d) {
			$fields['RETURNURL'] = $this->getReturnUrl();
		}

		$data = new $modelClass($fields);

		return $data;
	}

	/**
	 * @throws \Omnipay\Common\Exception\InvalidRequestException
	 * @throws \Omnipay\Common\Exception\InvalidCreditCardException
	 */
	protected function validateAll(): void
	{
		$this->validateSettings();

		$this->validate("card", "amount");

		$this->getCard()->validate();

		if ($this->getSecure()) {
			$this->validate("returnUrl");
		}
	}

	/**
	 * For 3D payments, return a redirect response.
	 * For non-3D, send to the API directly.
	 *
	 * @param PurchaseRequestModel|Purchase3dRequestModel $data
	 * @return ResponseInterface|PurchaseResponse
	 */
	public function sendData($data)
	{
		if ($data instanceof Purchase3dRequestModel) {
			// 3D flow: return redirect response
			$this->endpoint = $this->get3dUrl();

			return $this->response = new PurchaseResponse($this, $data);
		}

		// Non-3D flow: POST to API
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

	protected function createResponse($data): PurchaseResponse
	{
		return $this->response = new PurchaseResponse($this, $data);
	}
}
