<?php

namespace Omnipay\Payten\Tests\Feature;

use Omnipay\Payten\Constants\Provider;
use Omnipay\Payten\Exceptions\OmnipayPaytenInvalidProviderException;
use Omnipay\Payten\Message\PurchaseRequest;
use Omnipay\Payten\Tests\TestCase;

class ProviderTest extends TestCase
{
    public function test_provider_constants()
    {
        $this->assertSame('payten', Provider::PAYTEN);
        $this->assertSame('paratika', Provider::PARATIKA);
        $this->assertSame('vakifpays', Provider::VAKIFPAYS);
        $this->assertSame('ziraatpay', Provider::ZIRAATPAY);
    }

    public function test_all_providers_have_required_url_keys()
    {
        foreach (Provider::PROVIDERS as $name => $urls) {
            $this->assertArrayHasKey('test_api', $urls, "Provider '{$name}' missing test_api");
            $this->assertArrayHasKey('live_api', $urls, "Provider '{$name}' missing live_api");
            $this->assertArrayHasKey('test_3d', $urls, "Provider '{$name}' missing test_3d");
            $this->assertArrayHasKey('live_3d', $urls, "Provider '{$name}' missing live_3d");
        }
    }

    public function test_payten_urls()
    {
        $urls = Provider::PROVIDERS[Provider::PAYTEN];

        $this->assertSame('https://entegrasyon.asseco-see.com.tr/msu/api/v2', $urls['test_api']);
        $this->assertSame('https://merchantsafeunipay.com/msu/api/v2', $urls['live_api']);
        $this->assertStringContainsString('{merchant}', $urls['test_3d']);
        $this->assertStringContainsString('{merchant}', $urls['live_3d']);
    }

    public function test_paratika_urls()
    {
        $urls = Provider::PROVIDERS[Provider::PARATIKA];

        $this->assertSame('https://entegrasyon.paratika.com.tr/paratika/api/v2', $urls['test_api']);
        $this->assertSame('https://vpos.paratika.com.tr/paratika/api/v2', $urls['live_api']);
    }

    public function test_vakifpays_urls()
    {
        $urls = Provider::PROVIDERS[Provider::VAKIFPAYS];

        $this->assertSame('https://testpos.vakifpays.com.tr/vakifpays/api/v2', $urls['test_api']);
        $this->assertSame('https://pos.vakifpays.com.tr/vakifpays/api/v2', $urls['live_api']);
    }

    public function test_ziraatpay_urls()
    {
        $urls = Provider::PROVIDERS[Provider::ZIRAATPAY];

        $this->assertSame('https://test.ziraatpay.com.tr/ziraatpay/api/v2', $urls['test_api']);
        $this->assertSame('https://vpos.ziraatpay.com.tr/ziraatpay/api/v2', $urls['live_api']);
    }

    public function test_3d_urls_contain_merchant_placeholder()
    {
        foreach (Provider::PROVIDERS as $name => $urls) {
            $this->assertStringContainsString('{merchant}', $urls['test_3d'], "Provider '{$name}' test_3d missing {merchant}");
            $this->assertStringContainsString('{merchant}', $urls['live_3d'], "Provider '{$name}' live_3d missing {merchant}");
        }
    }

    public function test_invalid_provider_throws_exception()
    {
        $request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());

        $options = json_decode(file_get_contents(__DIR__ . '/../Mock/PurchaseRequest.json'), true);

        $options['provider'] = 'invalid_provider';

        $request->initialize($options);

        $this->expectException(OmnipayPaytenInvalidProviderException::class);

        // getData succeeds but sendData triggers URL resolution
        $data = $request->getData();
        $request->sendData($data);
    }
}
