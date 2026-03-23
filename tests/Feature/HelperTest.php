<?php

namespace Omnipay\Payten\Tests\Feature;

use Omnipay\Payten\Helpers\Helper;
use PHPUnit\Framework\TestCase;

class HelperTest extends TestCase
{
	public function test_format_expiry_full_year()
	{
		$result = Helper::formatExpiry('12', '2030');

		$this->assertSame('12.2030', $result);
	}

	public function test_format_expiry_short_year()
	{
		$result = Helper::formatExpiry('1', '30');

		$this->assertSame('01.2030', $result);
	}

	public function test_format_expiry_pads_month()
	{
		$result = Helper::formatExpiry('3', '2025');

		$this->assertSame('03.2025', $result);
	}

	public function test_format_expiry_null_values()
	{
		$result = Helper::formatExpiry(null, null);

		$this->assertSame('', $result);
	}

	public function test_format_amount()
	{
		$this->assertSame('100.00', Helper::formatAmount(100));
		$this->assertSame('10.50', Helper::formatAmount(10.5));
		$this->assertSame('0.99', Helper::formatAmount(0.99));
		$this->assertSame('1234.56', Helper::formatAmount('1234.56'));
	}

	public function test_format_amount_string_input()
	{
		$this->assertSame('50.00', Helper::formatAmount('50'));
		$this->assertSame('9.99', Helper::formatAmount('9.99'));
	}

	public function test_array_unset_recursive()
	{
		$data = [
			'key1' => 'value1',
			'key2' => null,
			'key3' => 'value3',
		];

		Helper::arrayUnsetRecursive($data);

		$this->assertArrayHasKey('key1', $data);
		$this->assertArrayNotHasKey('key2', $data);
		$this->assertArrayHasKey('key3', $data);
	}
}
