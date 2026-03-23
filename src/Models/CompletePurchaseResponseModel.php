<?php

namespace Omnipay\Payten\Models;

class CompletePurchaseResponseModel extends BaseModel
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
	 * Merchant payment ID.
	 *
	 * @var string
	 */
	public $merchantPaymentId;

	/**
	 * PG transaction ID.
	 *
	 * @var string
	 */
	public $pgTranId;

	/**
	 * 3D secure status indicator.
	 *
	 * @var string
	 */
	public $mdStatus;

	/**
	 * Raw response data (all callback fields).
	 *
	 * @var array
	 */
	public $rawData;
}
