<?php

namespace Omnipay\Payten;

use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Payten\Constants\Provider;
use Omnipay\Payten\Message\CompletePurchaseRequest;
use Omnipay\Payten\Message\InstallmentQueryRequest;
use Omnipay\Payten\Message\PurchaseRequest;
use Omnipay\Payten\Message\RefundRequest;
use Omnipay\Payten\Message\TransactionQueryRequest;
use Omnipay\Payten\Message\VoidRequest;
use Omnipay\Payten\Traits\GettersSettersTrait;

/**
 * Payten (Merchant Safe Unipay) Gateway
 *
 * Supports Payten, Paratika, VakifPays, ZiraatPay sub-providers via the `provider` parameter.
 *
 * (c) Tolga Can Gunel
 * http://www.github.com/tcgunel/omnipay-payten
 *
 * @method \Omnipay\Common\Message\NotificationInterface acceptNotification(array $options = [])
 * @method \Omnipay\Common\Message\RequestInterface authorize(array $options = [])
 * @method \Omnipay\Common\Message\RequestInterface completeAuthorize(array $options = [])
 * @method \Omnipay\Common\Message\RequestInterface fetchTransaction(array $options = [])
 * @method \Omnipay\Common\Message\RequestInterface createCard(array $options = [])
 * @method \Omnipay\Common\Message\RequestInterface updateCard(array $options = [])
 * @method \Omnipay\Common\Message\RequestInterface deleteCard(array $options = [])
 */
class Gateway extends AbstractGateway
{
	use GettersSettersTrait;

	public function getName(): string
	{
		return 'Payten';
	}

	public function getDefaultParameters(): array
	{
		return [
			'merchantId'       => '',
			'merchantUser'     => '',
			'merchantPassword' => '',
			'merchantStorekey' => '',
			'provider'         => Provider::PAYTEN,
			'installment'      => '1',
			'secure'           => true,
		];
	}

	public function purchase(array $options = []): AbstractRequest
	{
		return $this->createRequest(PurchaseRequest::class, $options);
	}

	public function completePurchase(array $options = []): AbstractRequest
	{
		return $this->createRequest(CompletePurchaseRequest::class, $options);
	}

	public function refund(array $options = []): AbstractRequest
	{
		return $this->createRequest(RefundRequest::class, $options);
	}

	public function void(array $options = []): AbstractRequest
	{
		return $this->createRequest(VoidRequest::class, $options);
	}

	public function installmentQuery(array $options = []): AbstractRequest
	{
		return $this->createRequest(InstallmentQueryRequest::class, $options);
	}

	public function transactionQuery(array $options = []): AbstractRequest
	{
		return $this->createRequest(TransactionQueryRequest::class, $options);
	}
}
