<?php

namespace Omnipay\Payten\Models;

class Purchase3dRequestModel extends PurchaseRequestModel
{
	/**
	 * Return URL for both success and failure in 3D flow.
	 *
	 * @var string
	 */
	public $RETURNURL;

	/**
	 * The ACTION is not sent in 3D form post (URL contains it).
	 * Override toArray to exclude ACTION.
	 */
	public function toArray(): array
	{
		$data = parent::toArray();

		unset($data['ACTION']);

		return $data;
	}
}
