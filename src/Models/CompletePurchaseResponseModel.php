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
     * Bank transaction error code (if any).
     *
     * @var string
     */
    public $pgTranErrorCode;

    /**
     * Bank transaction error message (if any).
     *
     * @var string
     */
    public $pgTranErrorText;

    /**
     * 3D secure status indicator.
     *
     * @var string
     */
    public $mdStatus;

    /**
     * Session token the payment was started with.
     *
     * @var string
     */
    public $sessionToken;

    /**
     * Customer id sent with the session token request.
     *
     * @var string
     */
    public $customerId;

    /**
     * Random value used in the sdSha512 signature.
     *
     * @var string
     */
    public $random;

    /**
     * Callback signature (SHA-512 hex).
     *
     * @var string
     */
    public $sdSha512;

    /**
     * Raw response data (all callback fields).
     *
     * @var array
     */
    public $rawData;
}
