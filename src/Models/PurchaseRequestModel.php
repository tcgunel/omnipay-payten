<?php

namespace Omnipay\Payten\Models;

use Omnipay\Payten\Constants\Action;

class PurchaseRequestModel extends BaseModel
{
	/**
	 * @var string
	 */
	public $ACTION = Action::SALE;

	/**
	 * Unique payment id from merchant side.
	 *
	 * @var string
	 */
	public $MERCHANTPAYMENTID;

	/**
	 * Merchant ID.
	 *
	 * @var string
	 */
	public $MERCHANT;

	/**
	 * Merchant user.
	 *
	 * @var string
	 */
	public $MERCHANTUSER;

	/**
	 * Merchant password.
	 *
	 * @var string
	 */
	public $MERCHANTPASSWORD;

	/**
	 * Dealer type name / store key (optional).
	 *
	 * @var string|null
	 */
	public $DEALERTYPENAME;

	/**
	 * Customer identifier.
	 *
	 * @var string
	 */
	public $CUSTOMER;

	/**
	 * Customer name.
	 *
	 * @var string
	 */
	public $CUSTOMERNAME;

	/**
	 * Customer email.
	 *
	 * @var string
	 */
	public $CUSTOMEREMAIL;

	/**
	 * Customer IP address.
	 *
	 * @var string
	 */
	public $CUSTOMERIP;

	/**
	 * Name on card.
	 *
	 * @var string
	 */
	public $NAMEONCARD;

	/**
	 * Card number (PAN).
	 *
	 * @var string
	 */
	public $CARDPAN;

	/**
	 * Card expiry in MM.YYYY format.
	 *
	 * @var string
	 */
	public $CARDEXPIRY;

	/**
	 * Card CVV.
	 *
	 * @var string
	 */
	public $CARDCVV;

	/**
	 * Amount in decimal format (e.g. "10.00").
	 *
	 * @var string
	 */
	public $AMOUNT;

	/**
	 * Currency code (TRY, USD, EUR).
	 *
	 * @var string
	 */
	public $CURRENCY;

	/**
	 * Installment count (1 = no installment).
	 *
	 * @var string|int
	 */
	public $INSTALLMENTS;

	/**
	 * Campaign code (optional).
	 *
	 * @var string|null
	 */
	public $CAMPAIGNCODE;

	/**
	 * Convert model to form data array, removing null values.
	 */
	public function toArray(): array
	{
		$data = get_object_vars($this);

		return array_filter($data, function ($value) {
			return $value !== null;
		});
	}
}
