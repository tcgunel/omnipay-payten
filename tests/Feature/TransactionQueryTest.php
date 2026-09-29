<?php

namespace Omnipay\Payten\Tests\Feature;

use Omnipay\Payten\Message\TransactionQueryRequest;
use Omnipay\Payten\Message\TransactionQueryResponse;
use Omnipay\Payten\Models\TransactionQueryRequestModel;
use Omnipay\Payten\Models\TransactionQueryResponseModel;
use Omnipay\Payten\Tests\TestCase;

class TransactionQueryTest extends TestCase
{
    /**
     * @throws \JsonException
     */
    public function test_transaction_query_request()
    {
        $options = file_get_contents(__DIR__ . '/../Mock/TransactionQueryRequest.json');

        $options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

        $request = new TransactionQueryRequest($this->getHttpClient(), $this->getHttpRequest());

        $request->initialize($options);

        $data = $request->getData();

        $this->assertInstanceOf(TransactionQueryRequestModel::class, $data);
        $this->assertSame('QUERYTRANSACTION', $data->ACTION);
        $this->assertSame('testmerchant', $data->MERCHANT);
        $this->assertSame('testuser', $data->MERCHANTUSER);
        $this->assertSame('testpassword', $data->MERCHANTPASSWORD);
        $this->assertSame('ORDER-12345', $data->MERCHANTPAYMENTID);
    }

    public function test_transaction_query_response_success()
    {
        $httpResponse = $this->getMockHttpResponse('TransactionQueryResponseSuccess.txt');

        $response = new TransactionQueryResponse($this->getMockRequest(), $httpResponse);

        $data = $response->getData();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('00', $response->getCode());
        $this->assertSame('ORDER-12345', $response->getTransactionId());
        $this->assertSame('PG-TXN-001', $response->getTransactionReference());

        $this->assertInstanceOf(TransactionQueryResponseModel::class, $data);
        $this->assertSame('100.00', $data->amount);
        $this->assertSame('TRY', $data->currency);
        $this->assertCount(1, $data->transactionList);
    }

    /**
     * A query that finds no transaction is not a successful payment; the
     * reconciliation job must keep polling.
     */
    public function test_transaction_query_response_without_transactions_is_not_successful()
    {
        $httpResponse = $this->getMockHttpResponse('TransactionQueryResponseNotFound.txt');

        $response = new TransactionQueryResponse($this->getMockRequest(), $httpResponse);

        $this->assertSame('00', $response->getCode());
        $this->assertFalse($response->isSuccessful());
    }

    public function test_transaction_query_response_api_error()
    {
        $httpResponse = $this->getMockHttpResponse('TransactionQueryResponseApiError.txt');

        $response = new TransactionQueryResponse($this->getMockRequest(), $httpResponse);

        $data = $response->getData();

        $this->assertFalse($response->isSuccessful());
        $this->assertSame('99', $response->getCode());
        $this->assertSame('Transaction not found', $response->getMessage());
    }
}
