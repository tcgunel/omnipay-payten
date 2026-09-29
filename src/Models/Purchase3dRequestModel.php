<?php

namespace Omnipay\Payten\Models;

/**
 * MSU v2 Direct Post 3D form (posted to post/sale3d/{sessionToken}).
 *
 * Field names are camelCase in this endpoint, unlike the API actions.
 */
class Purchase3dRequestModel extends BaseModel
{
    /**
     * Card owner name.
     *
     * @var string|null
     */
    public $cardOwner;

    /**
     * Card number (PAN).
     *
     * @var string|null
     */
    public $pan;

    /**
     * Expiry month, two digits (e.g. "05").
     *
     * @var string|null
     */
    public $expiryMonth;

    /**
     * Expiry year, four digits (e.g. "2031").
     *
     * @var string|null
     */
    public $expiryYear;

    /**
     * Card verification code.
     *
     * @var string|null
     */
    public $cvv;

    /**
     * Optional card name.
     *
     * @var string|null
     */
    public $cardName;

    /**
     * Installment count (1 = no installment).
     *
     * @var string|int|null
     */
    public $installmentCount;

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
