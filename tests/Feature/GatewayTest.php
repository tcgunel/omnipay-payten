<?php

namespace Omnipay\Payten\Tests\Feature;

use Omnipay\Payten\Constants\Provider;
use Omnipay\Payten\Gateway;
use Omnipay\Payten\Message\CompletePurchaseRequest;
use Omnipay\Payten\Message\InstallmentQueryRequest;
use Omnipay\Payten\Message\PurchaseRequest;
use Omnipay\Payten\Message\RefundRequest;
use Omnipay\Payten\Message\TransactionQueryRequest;
use Omnipay\Payten\Message\VoidRequest;
use Omnipay\Payten\Tests\TestCase;

class GatewayTest extends TestCase
{
	public function test_gateway_name()
	{
		$this->assertSame('Payten', $this->gateway->getName());
	}

	public function test_gateway_default_parameters()
	{
		$defaults = $this->gateway->getDefaultParameters();

		$this->assertArrayHasKey('merchantId', $defaults);
		$this->assertArrayHasKey('merchantUser', $defaults);
		$this->assertArrayHasKey('merchantPassword', $defaults);
		$this->assertArrayHasKey('provider', $defaults);
		$this->assertSame(Provider::PAYTEN, $defaults['provider']);
	}

	public function test_gateway_provider_parameter()
	{
		$this->gateway->setProvider(Provider::PARATIKA);

		$this->assertSame(Provider::PARATIKA, $this->gateway->getProvider());
	}

	public function test_gateway_purchase_returns_purchase_request()
	{
		$request = $this->gateway->purchase();

		$this->assertInstanceOf(PurchaseRequest::class, $request);
	}

	public function test_gateway_complete_purchase_returns_request()
	{
		$request = $this->gateway->completePurchase();

		$this->assertInstanceOf(CompletePurchaseRequest::class, $request);
	}

	public function test_gateway_refund_returns_refund_request()
	{
		$request = $this->gateway->refund();

		$this->assertInstanceOf(RefundRequest::class, $request);
	}

	public function test_gateway_void_returns_void_request()
	{
		$request = $this->gateway->void();

		$this->assertInstanceOf(VoidRequest::class, $request);
	}

	public function test_gateway_installment_query_returns_request()
	{
		$request = $this->gateway->installmentQuery();

		$this->assertInstanceOf(InstallmentQueryRequest::class, $request);
	}

	public function test_gateway_transaction_query_returns_request()
	{
		$request = $this->gateway->transactionQuery();

		$this->assertInstanceOf(TransactionQueryRequest::class, $request);
	}

	public function test_gateway_all_providers_can_be_set()
	{
		foreach (Provider::VALID_PROVIDERS as $provider) {
			$this->gateway->setProvider($provider);
			$this->assertSame($provider, $this->gateway->getProvider());
		}
	}

	public function test_gateway_merchant_credentials()
	{
		$this->gateway->setMerchantId('myMerchant');
		$this->gateway->setMerchantUser('myUser');
		$this->gateway->setMerchantPassword('myPassword');
		$this->gateway->setMerchantStorekey('myStorekey');

		$this->assertSame('myMerchant', $this->gateway->getMerchantId());
		$this->assertSame('myUser', $this->gateway->getMerchantUser());
		$this->assertSame('myPassword', $this->gateway->getMerchantPassword());
		$this->assertSame('myStorekey', $this->gateway->getMerchantStorekey());
	}
}
