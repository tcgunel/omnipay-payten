<?php

namespace Omnipay\Payten\Models;

use Omnipay\Payten\Constants\Action;

/**
 * MSU v2 session token request.
 *
 * Direct Post flows start with a SESSIONTOKEN request; the returned token is
 * the only thing the 3D form URL (post/sale3d/{sessionToken}) needs.
 */
class SessionTokenRequestModel extends BaseModel
{
    /**
     * @var string
     */
    public $ACTION = Action::SESSIONTOKEN;

    /**
     * @var string
     */
    public $SESSIONTYPE = 'PAYMENTSESSION';

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
     * Unique payment id from merchant side.
     *
     * @var string
     */
    public $MERCHANTPAYMENTID;

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
     * Return URL the result is posted to.
     *
     * @var string
     */
    public $RETURNURL;

    /**
     * Customer identifier.
     *
     * @var string
     */
    public $CUSTOMER;

    /**
     * @var string
     */
    public $CUSTOMERNAME;

    /**
     * @var string
     */
    public $CUSTOMEREMAIL;

    /**
     * @var string
     */
    public $CUSTOMERIP;

    /**
     * @var string
     */
    public $CUSTOMERPHONE;

    /**
     * Dealer type name / store key (optional).
     *
     * @var string|null
     */
    public $DEALERTYPENAME;

    /**
     * Order items as a JSON array; MSU validates their total against AMOUNT
     * and some merchants (Paratika test included) refuse sessions without it.
     *
     * @var string|null
     */
    public $ORDERITEMS;

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
