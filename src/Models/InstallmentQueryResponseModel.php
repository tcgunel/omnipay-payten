<?php

namespace Omnipay\Payten\Models;

class InstallmentQueryResponseModel extends BaseModel
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
     * Installment plan list.
     *
     * @var array|null
     */
    public $installmentPlanList;
}
