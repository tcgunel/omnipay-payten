<?php

namespace Omnipay\Payten\Tests\Feature;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Payten\Message\PurchaseRequest;
use Omnipay\Payten\Message\PurchaseResponse;
use Omnipay\Payten\Models\Purchase3dRequestModel;
use Omnipay\Payten\Models\PurchaseRequestModel;
use Omnipay\Payten\Models\PurchaseResponseModel;
use Omnipay\Payten\Tests\TestCase;

class PurchaseTest extends TestCase
{
	/**
	 * @throws \JsonException
	 */
	public function test_purchase_request_non_3d()
	{
		$options = file_get_contents(__DIR__ . "/../Mock/PurchaseRequest.json");

		$options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

		$request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());

		$request->initialize($options);

		$data = $request->getData();

		$this->assertInstanceOf(PurchaseRequestModel::class, $data);
		$this->assertNotInstanceOf(Purchase3dRequestModel::class, $data);

		$this->assertSame('SALE', $data->ACTION);
		$this->assertSame('ORDER-12345', $data->MERCHANTPAYMENTID);
		$this->assertSame('testmerchant', $data->MERCHANT);
		$this->assertSame('testuser', $data->MERCHANTUSER);
		$this->assertSame('testpassword', $data->MERCHANTPASSWORD);
		$this->assertSame('4355084355084358', $data->CARDPAN);
		$this->assertSame('12.2030', $data->CARDEXPIRY);
		$this->assertSame('000', $data->CARDCVV);
		$this->assertSame('100.00', $data->AMOUNT);
		$this->assertSame('TRY', $data->CURRENCY);
		$this->assertSame('1', $data->INSTALLMENTS);
		$this->assertSame('Example User', $data->NAMEONCARD);
	}

	/**
	 * @throws \JsonException
	 */
	public function test_purchase_request_3d()
	{
		$options = file_get_contents(__DIR__ . "/../Mock/PurchaseRequest-3d.json");

		$options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

		$request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());

		$request->initialize($options);

		$data = $request->getData();

		$this->assertInstanceOf(Purchase3dRequestModel::class, $data);

		$this->assertSame('ORDER-12345', $data->MERCHANTPAYMENTID);
		$this->assertSame('testmerchant', $data->MERCHANT);
		$this->assertSame('https://example.com/payment/callback', $data->RETURNURL);
		$this->assertSame('100.00', $data->AMOUNT);
	}

	/**
	 * @throws \JsonException
	 */
	public function test_purchase_3d_to_array_excludes_action()
	{
		$options = file_get_contents(__DIR__ . "/../Mock/PurchaseRequest-3d.json");

		$options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

		$request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());

		$request->initialize($options);

		$data = $request->getData();

		$array = $data->toArray();

		$this->assertArrayNotHasKey('ACTION', $array);
		$this->assertArrayHasKey('MERCHANT', $array);
		$this->assertArrayHasKey('RETURNURL', $array);
	}

	/**
	 * @throws \JsonException
	 */
	public function test_purchase_3d_response_is_redirect()
	{
		$options = file_get_contents(__DIR__ . "/../Mock/PurchaseRequest-3d.json");

		$options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

		$request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());

		/** @var PurchaseResponse $response */
		$response = $request->initialize($options)->send();

		$this->assertFalse($response->isSuccessful());
		$this->assertTrue($response->isRedirect());
		$this->assertSame('POST', $response->getRedirectMethod());

		// 3D URL should have merchant ID substituted
		$redirectUrl = $response->getRedirectUrl();
		$this->assertStringContainsString('testmerchant', $redirectUrl);
		$this->assertStringNotContainsString('{merchant}', $redirectUrl);
		$this->assertStringContainsString('entegrasyon', $redirectUrl); // test mode

		$redirectData = $response->getRedirectData();
		$this->assertArrayHasKey('MERCHANT', $redirectData);
		$this->assertArrayHasKey('RETURNURL', $redirectData);
		$this->assertArrayNotHasKey('ACTION', $redirectData);
	}

	public function test_purchase_request_validation_error_no_card()
	{
		$options = file_get_contents(__DIR__ . "/../Mock/PurchaseRequest-ValidationError.json");

		$options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

		$request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());

		$request->initialize($options);

		$this->expectException(InvalidRequestException::class);

		$request->getData();
	}

	public function test_purchase_response_success()
	{
		$httpResponse = $this->getMockHttpResponse('PurchaseResponseSuccess.txt');

		$response = new PurchaseResponse($this->getMockRequest(), $httpResponse);

		$this->assertTrue($response->isSuccessful());
		$this->assertFalse($response->isRedirect());
		$this->assertSame('00', $response->getCode());
		$this->assertSame('PG-TXN-001', $response->getTransactionReference());

		$data = $response->getData();
		$this->assertInstanceOf(PurchaseResponseModel::class, $data);
		$this->assertSame('00', $data->responseCode);
		$this->assertSame('ORDER-12345', $data->merchantPaymentId);
	}

	public function test_purchase_response_api_error()
	{
		$httpResponse = $this->getMockHttpResponse('PurchaseResponseApiError.txt');

		$response = new PurchaseResponse($this->getMockRequest(), $httpResponse);

		$this->assertFalse($response->isSuccessful());
		$this->assertFalse($response->isRedirect());
		$this->assertSame('99', $response->getCode());
		$this->assertSame('Insufficient funds', $response->getMessage());

		$data = $response->getData();
		$this->assertInstanceOf(PurchaseResponseModel::class, $data);
		$this->assertSame('99', $data->responseCode);
	}

	/**
	 * @throws \JsonException
	 */
	public function test_purchase_non_3d_to_array_includes_action()
	{
		$options = file_get_contents(__DIR__ . "/../Mock/PurchaseRequest.json");

		$options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

		$request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());

		$request->initialize($options);

		$data = $request->getData();

		$array = $data->toArray();

		$this->assertArrayHasKey('ACTION', $array);
		$this->assertSame('SALE', $array['ACTION']);
	}
}
