<?php

namespace Omnipay\Payten\Models;

use Omnipay\Payten\Constants\Action;

class VoidRequestModel extends BaseModel
{
    /**
     * @var string
     */
    public $ACTION = Action::VOID;

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
