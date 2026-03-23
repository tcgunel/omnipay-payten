<?php

namespace Omnipay\Payten\Tests\Feature;

use Omnipay\Payten\Message\CompletePurchaseRequest;
use Omnipay\Payten\Message\CompletePurchaseResponse;
use Omnipay\Payten\Models\CompletePurchaseResponseModel;
use Omnipay\Payten\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request as HttpRequest;

class CompletePurchaseTest extends TestCase
{
	public function test_complete_purchase_success()
	{
		$postData = [
			'responseCode'      => '00',
			'responseMsg'       => 'Approved',
			'errorMsg'          => '',
			'errorCode'         => '',
			'merchantPaymentId' => 'ORDER-12345',
			'pgTranId'          => 'PG-TXN-001',
			'mdStatus'          => '1',
		];

		$httpRequest = new HttpRequest([], $postData);

		$request = new CompletePurchaseRequest($this->getHttpClient(), $httpRequest);

		$request->initialize([
			'merchantId'       => 'testmerchant',
			'merchantUser'     => 'testuser',
			'merchantPassword' => 'testpassword',
		]);

		$data = $request->getData();

		$this->assertInstanceOf(CompletePurchaseResponseModel::class, $data);
		$this->assertSame('00', $data->responseCode);
		$this->assertSame('ORDER-12345', $data->merchantPaymentId);
		$this->assertSame('PG-TXN-001', $data->pgTranId);
		$this->assertSame('1', $data->mdStatus);
	}

	public function test_complete_purchase_response_success()
	{
		$model = new CompletePurchaseResponseModel([
			'responseCode'      => '00',
			'responseMsg'       => 'Approved',
			'errorMsg'          => '',
			'merchantPaymentId' => 'ORDER-12345',
			'pgTranId'          => 'PG-TXN-001',
			'mdStatus'          => '1',
		]);

		$response = new CompletePurchaseResponse($this->getMockRequest(), $model);

		$this->assertTrue($response->isSuccessful());
		$this->assertSame('00', $response->getCode());
		$this->assertSame('Approved', $response->getMessage());
		$this->assertSame('ORDER-12345', $response->getTransactionId());
		$this->assertSame('PG-TXN-001', $response->getTransactionReference());
	}

	public function test_complete_purchase_response_failure()
	{
		$model = new CompletePurchaseResponseModel([
			'responseCode'      => '99',
			'responseMsg'       => 'Declined',
			'errorMsg'          => '3D authentication failed',
			'merchantPaymentId' => 'ORDER-12345',
			'pgTranId'          => '',
			'mdStatus'          => '0',
		]);

		$response = new CompletePurchaseResponse($this->getMockRequest(), $model);

		$this->assertFalse($response->isSuccessful());
		$this->assertSame('99', $response->getCode());
		$this->assertSame('3D authentication failed', $response->getMessage());
	}

	public function test_complete_purchase_raw_data_preserved()
	{
		$postData = [
			'responseCode'      => '00',
			'responseMsg'       => 'Approved',
			'errorMsg'          => '',
			'merchantPaymentId' => 'ORDER-12345',
			'pgTranId'          => 'PG-TXN-001',
			'mdStatus'          => '1',
			'customField'       => 'customValue',
		];

		$httpRequest = new HttpRequest([], $postData);

		$request = new CompletePurchaseRequest($this->getHttpClient(), $httpRequest);

		$request->initialize([
			'merchantId'       => 'testmerchant',
			'merchantUser'     => 'testuser',
			'merchantPassword' => 'testpassword',
		]);

		$data = $request->getData();

		$this->assertIsArray($data->rawData);
		$this->assertArrayHasKey('customField', $data->rawData);
		$this->assertSame('customValue', $data->rawData['customField']);
	}
}
