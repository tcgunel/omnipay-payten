<?php

namespace Omnipay\Payten\Helpers;

class Helper
{
    /**
     * Format expiry to MM.YYYY as required by MSU API.
     */
    public static function formatExpiry(?string $month, ?string $year): string
    {
        if ($month === null || $year === null) {
            return '';
        }

        $month = str_pad((string) (int) $month, 2, '0', STR_PAD_LEFT);

        // If year is 2 digits, prepend 20
        if (strlen($year) === 2) {
            $year = '20' . $year;
        }

        return $month . '.' . $year;
    }

    /**
     * Format amount: MSU expects decimal string like "10.00"
     */
    public static function formatAmount($amount): string
    {
        return number_format((float) $amount, 2, '.', '');
    }

    /**
     * Remove null values from array recursively.
     *
     * @param array|object $input
     */
    public static function arrayUnsetRecursive(&$input): void
    {
        foreach ($input as $key => $value) {

            if (is_array($value)) {

                self::arrayUnsetRecursive($value);

            } elseif ($value === null) {

                if (is_object($input)) {

                    unset($input->$key);

                } else {

                    unset($input[$key]);

                }

            }

        }
    }

    public static function prettyPrint($data): void
    {
        echo '<pre>' . print_r($data, true) . '</pre>';
    }
}
