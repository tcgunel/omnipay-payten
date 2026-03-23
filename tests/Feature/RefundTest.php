<?php

namespace Omnipay\Payten\Tests\Feature;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Payten\Message\RefundRequest;
use Omnipay\Payten\Message\RefundResponse;
use Omnipay\Payten\Models\RefundRequestModel;
use Omnipay\Payten\Models\RefundResponseModel;
use Omnipay\Payten\Tests\TestCase;

class RefundTest extends TestCase
{
	/**
	 * @throws \JsonException
	 */
	public function test_refund_request()
	{
		$options = file_get_contents(__DIR__ . "/../Mock/RefundRequest.json");

		$options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

		$request = new RefundRequest($this->getHttpClient(), $this->getHttpRequest());

		$request->initialize($options);

		$data = $request->getData();

		$this->assertInstanceOf(RefundRequestModel::class, $data);
		$this->assertSame('REFUND', $data->ACTION);
		$this->assertSame('testmerchant', $data->MERCHANT);
		$this->assertSame('testuser', $data->MERCHANTUSER);
		$this->assertSame('testpassword', $data->MERCHANTPASSWORD);
		$this->assertSame('ORDER-12345', $data->MERCHANTPAYMENTID);
		$this->assertSame('50.00', $data->AMOUNT);
		$this->assertSame('TRY', $data->CURRENCY);
	}

	public function test_refund_request_validation_error()
	{
		$options = file_get_contents(__DIR__ . "/../Mock/RefundRequest-ValidationError.json");

		$options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

		$request = new RefundRequest($this->getHttpClient(), $this->getHttpRequest());

		$request->initialize($options);

		$this->expectException(InvalidRequestException::class);

		$request->getData();
	}

	public function test_refund_response_success()
	{
		$httpResponse = $this->getMockHttpResponse('RefundResponseSuccess.txt');

		$response = new RefundResponse($this->getMockRequest(), $httpResponse);

		$data = $response->getData();

		$this->assertTrue($response->isSuccessful());
		$this->assertSame('00', $response->getCode());

		$this->assertInstanceOf(RefundResponseModel::class, $data);
		$this->assertSame('00', $data->responseCode);
	}

	public function test_refund_response_api_error()
	{
		$httpResponse = $this->getMockHttpResponse('RefundResponseApiError.txt');

		$response = new RefundResponse($this->getMockRequest(), $httpResponse);

		$data = $response->getData();

		$this->assertFalse($response->isSuccessful());
		$this->assertSame('99', $response->getCode());
		$this->assertSame('Original transaction not found', $response->getMessage());

		$this->assertInstanceOf(RefundResponseModel::class, $data);
		$this->assertSame('99', $data->responseCode);
	}
}
