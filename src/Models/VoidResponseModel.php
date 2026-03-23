<?php

namespace Omnipay\Payten\Models;

class VoidResponseModel extends BaseModel
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
}
