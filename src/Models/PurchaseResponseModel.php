<?php

namespace Omnipay\Payten\Models;

class PurchaseResponseModel extends BaseModel
{
	/**
	 * Response code. "00" means success.
	 *
	 * @var string
	 */
	public $responseCode;

	/**
	 * Response message.
	 *
	 * @var string
	 */
	public $responseMsg;

	/**
	 * Error message (if any).
	 *
	 * @var string
	 */
	public $errorMsg;

	/**
	 * Error code (if any).
	 *
	 * @var string
	 */
	public $errorCode;

	/**
	 * Transaction ID from the provider.
	 *
	 * @var string
	 */
	public $pgTranId;

	/**
	 * Merchant payment ID.
	 *
	 * @var string
	 */
	public $merchantPaymentId;
}
