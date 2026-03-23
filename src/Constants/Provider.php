<?php

namespace Omnipay\Payten\Constants;

class Provider
{
	public const PAYTEN = 'payten';

	public const PARATIKA = 'paratika';

	public const VAKIFPAYS = 'vakifpays';

	public const ZIRAATPAY = 'ziraatpay';

	public const PROVIDERS = [
		self::PAYTEN => [
			'test_api' => 'https://entegrasyon.asseco-see.com.tr/msu/api/v2',
			'live_api' => 'https://merchantsafeunipay.com/msu/api/v2',
			'test_3d' => 'https://entegrasyon.asseco-see.com.tr/msu/api/v2/post/sale3d/{merchant}',
			'live_3d' => 'https://merchantsafeunipay.com/msu/api/v2/post/sale3d/{merchant}',
		],
		self::PARATIKA => [
			'test_api' => 'https://entegrasyon.paratika.com.tr/paratika/api/v2',
			'live_api' => 'https://vpos.paratika.com.tr/paratika/api/v2',
			'test_3d' => 'https://entegrasyon.paratika.com.tr/paratika/api/v2/post/sale3d/{merchant}',
			'live_3d' => 'https://vpos.paratika.com.tr/paratika/api/v2/post/sale3d/{merchant}',
		],
		self::VAKIFPAYS => [
			'test_api' => 'https://testpos.vakifpays.com.tr/vakifpays/api/v2',
			'live_api' => 'https://pos.vakifpays.com.tr/vakifpays/api/v2',
			'test_3d' => 'https://testpos.vakifpays.com.tr/vakifpays/api/v2/post/sale3d/{merchant}',
			'live_3d' => 'https://pos.vakifpays.com.tr/vakifpays/api/v2/post/sale3d/{merchant}',
		],
		self::ZIRAATPAY => [
			'test_api' => 'https://test.ziraatpay.com.tr/ziraatpay/api/v2',
			'live_api' => 'https://vpos.ziraatpay.com.tr/ziraatpay/api/v2',
			'test_3d' => 'https://test.ziraatpay.com.tr/ziraatpay/api/v2/post/sale3d/{merchant}',
			'live_3d' => 'https://vpos.ziraatpay.com.tr/ziraatpay/api/v2/post/sale3d/{merchant}',
		],
	];

	public const VALID_PROVIDERS = [
		self::PAYTEN,
		self::PARATIKA,
		self::VAKIFPAYS,
		self::ZIRAATPAY,
	];
}
