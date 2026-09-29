<?php

namespace Omnipay\Payten\Models;

class TransactionQueryResponseModel extends BaseModel
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
     * Number of transactions returned for the query.
     *
     * @var string|int
     */
    public $transactionCount;

    /**
     * Transactions returned for the query.
     *
     * @var array<int, array<string, mixed>>
     */
    public $transactionList = [];

    /**
     * Transaction amount.
     *
     * @var string
     */
    public $amount;

    /**
     * Currency code.
     *
     * @var string
     */
    public $currency;

    /**
     * Installment count.
     *
     * @var string
     */
    public $installmentCount;
}
