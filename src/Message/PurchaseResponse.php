<?php

namespace Omnipay\Payten\Message;

use JsonException;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Payten\Models\Purchase3dRequestModel;
use Omnipay\Payten\Models\PurchaseResponseModel;
use Psr\Http\Message\ResponseInterface;

class PurchaseResponse extends AbstractResponse implements RedirectResponseInterface
{
	protected $response;

	protected $request;

	public function __construct(RequestInterface $request, $data)
	{
		parent::__construct($request, $data);

		$this->request = $request;

		$this->response = $data;

		if ($data instanceof ResponseInterface) {

			$body = (string)$data->getBody();

			try {

				$decoded = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

				$this->response = new PurchaseResponseModel($decoded);

			} catch (JsonException $e) {

				$this->response = new PurchaseResponseModel([
					'responseCode' => '99',
					'errorMsg'     => $body,
				]);

			}

		}
	}

	public function isSuccessful(): bool
	{
		if ($this->response instanceof Purchase3dRequestModel) {
			// 3D redirect is not yet "successful" - needs completePurchase
			return false;
		}

		if ($this->response instanceof PurchaseResponseModel) {
			return $this->response->responseCode === '00';
		}

		return false;
	}

	public function isRedirect(): bool
	{
		return $this->response instanceof Purchase3dRequestModel;
	}

	public function getRedirectUrl()
	{
		if (!$this->isRedirect()) {
			return null;
		}

		/** @var PurchaseRequest $request */
		$request = $this->getRequest();

		return $request->getEndpoint();
	}

	public function getRedirectMethod(): string
	{
		return 'POST';
	}

	public function getRedirectData(): array
	{
		if ($this->response instanceof Purchase3dRequestModel) {
			return $this->response->toArray();
		}

		return [];
	}

	public function getMessage(): ?string
	{
		if ($this->response instanceof PurchaseResponseModel) {
			return $this->response->errorMsg ?: $this->response->responseMsg;
		}

		return null;
	}

	public function getCode(): ?string
	{
		if ($this->response instanceof PurchaseResponseModel) {
			return $this->response->responseCode;
		}

		return null;
	}

	public function getTransactionReference(): ?string
	{
		if ($this->response instanceof PurchaseResponseModel) {
			return $this->response->pgTranId;
		}

		return null;
	}

	public function getData()
	{
		return $this->response;
	}
}
