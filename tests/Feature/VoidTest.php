<?php

namespace Omnipay\Payten\Tests\Feature;

use Omnipay\Payten\Message\VoidRequest;
use Omnipay\Payten\Message\VoidResponse;
use Omnipay\Payten\Models\VoidRequestModel;
use Omnipay\Payten\Models\VoidResponseModel;
use Omnipay\Payten\Tests\TestCase;

class VoidTest extends TestCase
{
    /**
     * @throws \JsonException
     */
    public function test_void_request()
    {
        $options = file_get_contents(__DIR__ . '/../Mock/VoidRequest.json');

        $options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

        $request = new VoidRequest($this->getHttpClient(), $this->getHttpRequest());

        $request->initialize($options);

        $data = $request->getData();

        $this->assertInstanceOf(VoidRequestModel::class, $data);
        $this->assertSame('VOID', $data->ACTION);
        $this->assertSame('testmerchant', $data->MERCHANT);
        $this->assertSame('testuser', $data->MERCHANTUSER);
        $this->assertSame('testpassword', $data->MERCHANTPASSWORD);
        $this->assertSame('ORDER-12345', $data->MERCHANTPAYMENTID);
    }

    public function test_void_response_success()
    {
        $httpResponse = $this->getMockHttpResponse('VoidResponseSuccess.txt');

        $response = new VoidResponse($this->getMockRequest(), $httpResponse);

        $data = $response->getData();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('00', $response->getCode());

        $this->assertInstanceOf(VoidResponseModel::class, $data);
        $this->assertSame('00', $data->responseCode);
    }

    public function test_void_response_api_error()
    {
        $httpResponse = $this->getMockHttpResponse('VoidResponseApiError.txt');

        $response = new VoidResponse($this->getMockRequest(), $httpResponse);

        $data = $response->getData();

        $this->assertFalse($response->isSuccessful());
        $this->assertSame('99', $response->getCode());
        $this->assertSame('Transaction already voided', $response->getMessage());

        $this->assertInstanceOf(VoidResponseModel::class, $data);
    }
}
