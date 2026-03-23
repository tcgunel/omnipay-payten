<?php

namespace Omnipay\Payten\Message;

use JsonException;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Payten\Models\InstallmentQueryResponseModel;
use Psr\Http\Message\ResponseInterface;

class InstallmentQueryResponse extends AbstractResponse
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

				$this->response = new InstallmentQueryResponseModel($decoded);

			} catch (JsonException $e) {

				$this->response = new InstallmentQueryResponseModel([
					'responseCode' => '99',
					'errorMsg'     => $body,
				]);

			}

		}
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

	public function getData(): InstallmentQueryResponseModel
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
