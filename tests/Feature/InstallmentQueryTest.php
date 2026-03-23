<?php

namespace Omnipay\Payten\Tests\Feature;

use Omnipay\Payten\Message\InstallmentQueryRequest;
use Omnipay\Payten\Message\InstallmentQueryResponse;
use Omnipay\Payten\Models\InstallmentQueryRequestModel;
use Omnipay\Payten\Models\InstallmentQueryResponseModel;
use Omnipay\Payten\Tests\TestCase;

class InstallmentQueryTest extends TestCase
{
    /**
     * @throws \JsonException
     */
    public function test_installment_query_request()
    {
        $options = file_get_contents(__DIR__ . '/../Mock/InstallmentQueryRequest.json');

        $options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

        $request = new InstallmentQueryRequest($this->getHttpClient(), $this->getHttpRequest());

        $request->initialize($options);

        $data = $request->getData();

        $this->assertInstanceOf(InstallmentQueryRequestModel::class, $data);
        $this->assertSame('QUERYINSTALLMENTS', $data->ACTION);
        $this->assertSame('testmerchant', $data->MERCHANT);
        $this->assertSame('testuser', $data->MERCHANTUSER);
        $this->assertSame('testpassword', $data->MERCHANTPASSWORD);
        // BIN should be first 6 digits
        $this->assertSame('435508', $data->CARDPAN);
        $this->assertSame('TRY', $data->CURRENCY);
        $this->assertSame('100.00', $data->AMOUNT);
    }

    public function test_installment_query_response_success()
    {
        $httpResponse = $this->getMockHttpResponse('InstallmentQueryResponseSuccess.txt');

        $response = new InstallmentQueryResponse($this->getMockRequest(), $httpResponse);

        $data = $response->getData();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('00', $response->getCode());

        $this->assertInstanceOf(InstallmentQueryResponseModel::class, $data);
        $this->assertIsArray($data->installmentPlanList);
        $this->assertCount(3, $data->installmentPlanList);
    }

    public function test_installment_query_response_api_error()
    {
        $httpResponse = $this->getMockHttpResponse('InstallmentQueryResponseApiError.txt');

        $response = new InstallmentQueryResponse($this->getMockRequest(), $httpResponse);

        $data = $response->getData();

        $this->assertFalse($response->isSuccessful());
        $this->assertSame('99', $response->getCode());
        $this->assertSame('Invalid BIN number', $response->getMessage());
    }
}
