<?php

namespace Omnipay\Payten\Models;

use Omnipay\Payten\Constants\Action;

class RefundRequestModel extends BaseModel
{
    /**
     * @var string
     */
    public $ACTION = Action::REFUND;

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
     * @var string
     */
    public $MERCHANTPAYMENTID;

    /**
     * Amount to refund in decimal format (e.g. "10.00").
     *
     * @var string
     */
    public $AMOUNT;

    /**
     * Currency code.
     *
     * @var string
     */
    public $CURRENCY;

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
