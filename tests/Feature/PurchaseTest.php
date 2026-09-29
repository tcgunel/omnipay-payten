<?php

namespace Omnipay\Payten\Tests\Feature;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Item;
use Omnipay\Common\ItemBag;
use Omnipay\Payten\Message\PurchaseRequest;
use Omnipay\Payten\Message\PurchaseResponse;
use Omnipay\Payten\Models\PurchaseRequestModel;
use Omnipay\Payten\Models\PurchaseResponseModel;
use Omnipay\Payten\Models\SessionTokenRequestModel;
use Omnipay\Payten\Tests\TestCase;

class PurchaseTest extends TestCase
{
    /**
     * @throws \JsonException
     */
    public function test_purchase_request_non_3d()
    {
        $options = file_get_contents(__DIR__ . '/../Mock/PurchaseRequest.json');

        $options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

        $request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());

        $request->initialize($options);

        $data = $request->getData();

        $this->assertInstanceOf(PurchaseRequestModel::class, $data);
        $this->assertNotInstanceOf(SessionTokenRequestModel::class, $data);

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
        $options = file_get_contents(__DIR__ . '/../Mock/PurchaseRequest-3d.json');

        $options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

        $request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());

        $request->initialize($options);

        $data = $request->getData();

        // The 3D flow starts with a session token request.
        $this->assertInstanceOf(SessionTokenRequestModel::class, $data);

        $this->assertSame('SESSIONTOKEN', $data->ACTION);
        $this->assertSame('PAYMENTSESSION', $data->SESSIONTYPE);
        $this->assertSame('ORDER-12345', $data->MERCHANTPAYMENTID);
        $this->assertSame('testmerchant', $data->MERCHANT);
        $this->assertSame('testuser', $data->MERCHANTUSER);
        $this->assertSame('testpassword', $data->MERCHANTPASSWORD);
        $this->assertSame('https://example.com/payment/callback', $data->RETURNURL);
        $this->assertSame('100.00', $data->AMOUNT);
        $this->assertSame('TRY', $data->CURRENCY);
        $this->assertSame('1', $data->INSTALLMENTS);
        $this->assertSame('127.0.0.1', $data->CUSTOMERIP);
        $this->assertSame('test@example.com', $data->CUSTOMEREMAIL);
        $this->assertSame('Example User', $data->CUSTOMERNAME);
    }

    /**
     * @throws \JsonException
     */
    public function test_purchase_3d_session_token_response_is_redirect()
    {
        $options = file_get_contents(__DIR__ . '/../Mock/PurchaseRequest-3d.json');

        $options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

        $this->setMockHttpResponse('SessionTokenResponseSuccess.txt');

        $request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());

        /** @var PurchaseResponse $response */
        $response = $request->initialize($options)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertSame('POST', $response->getRedirectMethod());

        // The form must post to the sale3d endpoint of the returned token.
        $redirectUrl = $response->getRedirectUrl();
        $this->assertStringContainsString('post/sale3d/SESSION-TOKEN-123', $redirectUrl);
        $this->assertStringNotContainsString('{sessionToken}', $redirectUrl);
        $this->assertStringContainsString('entegrasyon', $redirectUrl); // test mode

        $redirectData = $response->getRedirectData();
        $this->assertSame('4355084355084358', $redirectData['pan']);
        $this->assertSame('12', $redirectData['expiryMonth']);
        $this->assertSame('2030', $redirectData['expiryYear']);
        $this->assertSame('000', $redirectData['cvv']);
        $this->assertSame('1', $redirectData['installmentCount']);
        $this->assertSame('Example User', $redirectData['cardOwner']);
    }

    /**
     * @throws \JsonException
     */
    public function test_purchase_3d_session_token_error_is_not_a_redirect()
    {
        $options = file_get_contents(__DIR__ . '/../Mock/PurchaseRequest-3d.json');

        $options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

        $this->setMockHttpResponse('SessionTokenResponseApiError.txt');

        $request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());

        /** @var PurchaseResponse $response */
        $response = $request->initialize($options)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('99', $response->getCode());
        $this->assertSame('Gecersiz kullanici bilgileri', $response->getMessage());

        $data = $response->getData();
        $this->assertInstanceOf(PurchaseResponseModel::class, $data);
        $this->assertSame('ERR10020', $data->errorCode);
    }

    /**
     * @throws \JsonException
     */
    public function test_purchase_3d_session_token_includes_order_items()
    {
        $options = file_get_contents(__DIR__ . '/../Mock/PurchaseRequest-3d.json');

        $options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

        $request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());

        $request->initialize($options);

        $itemBag = new ItemBag();
        $item = new Item();
        $item->setName('Test Item');
        $item->setDescription('SKU-1');
        $item->setPrice('100.00');
        $item->setQuantity(1);
        $itemBag->add($item);

        $request->setItems($itemBag);

        $data = $request->getData();

        $this->assertNotEmpty($data->ORDERITEMS);

        $items = json_decode($data->ORDERITEMS, true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame('SKU-1', $items[0]['productCode']);
        $this->assertSame('Test Item', $items[0]['name']);
        $this->assertSame('1', $items[0]['quantity']);
        $this->assertSame('Test Item', $items[0]['description']);
        $this->assertSame('100.00', $items[0]['amount']);
    }

    public function test_purchase_request_validation_error_no_card()
    {
        $options = file_get_contents(__DIR__ . '/../Mock/PurchaseRequest-ValidationError.json');

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
        $options = file_get_contents(__DIR__ . '/../Mock/PurchaseRequest.json');

        $options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

        $request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());

        $request->initialize($options);

        $data = $request->getData();

        $array = $data->toArray();

        $this->assertArrayHasKey('ACTION', $array);
        $this->assertSame('SALE', $array['ACTION']);
    }
}
