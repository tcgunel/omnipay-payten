<?php

namespace Omnipay\Payten\Models;

use Omnipay\Payten\Constants\Action;

class InstallmentQueryRequestModel extends BaseModel
{
	/**
	 * @var string
	 */
	public $ACTION = Action::QUERYINSTALLMENTS;

	/**
	 * @var string
	 */
	public $MERCHANT;

	/**
	 * @var string
	 */
	public $MERCHANTUSER;

	/**
	 * @var string
	 */
	public $MERCHANTPASSWORD;

	/**
	 * First 6 digits of card number (BIN).
	 *
	 * @var string
	 */
	public $CARDPAN;

	/**
	 * Currency code.
	 *
	 * @var string
	 */
	public $CURRENCY;

	/**
	 * Amount for installment calculation.
	 *
	 * @var string
	 */
	public $AMOUNT;

	/**
	 * Convert model to form data array.
	 */
	public function toArray(): array
	{
		$data = get_object_vars($this);

		return array_filter($data, function ($value) {
			return $value !== null;
		});
	}
}
