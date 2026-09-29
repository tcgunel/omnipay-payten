<?php

namespace Omnipay\Payten\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Payten\Constants\Provider;
use Omnipay\Payten\Exceptions\OmnipayPaytenInvalidProviderException;
use Omnipay\Payten\Traits\GettersSettersTrait;

abstract class RemoteAbstractRequest extends AbstractRequest
{
    use GettersSettersTrait;

    protected $endpoint = '';

    /**
     * @throws InvalidRequestException
     */
    protected function validateSettings(): void
    {
        $this->validate('merchantId', 'merchantUser', 'merchantPassword');
    }

    /**
     * Resolve the API URL based on provider and test mode.
     */
    protected function getApiUrl(): string
    {
        $provider = $this->getProvider() ?? Provider::PAYTEN;

        if (!isset(Provider::PROVIDERS[$provider])) {
            throw new OmnipayPaytenInvalidProviderException(
                "Invalid provider: {$provider}. Valid providers: " . implode(', ', Provider::VALID_PROVIDERS)
            );
        }

        $key = $this->getTestMode() ? 'test_api' : 'live_api';

        return Provider::PROVIDERS[$provider][$key];
    }

    /**
     * Resolve the 3D URL based on provider and test mode.
     *
     * The MSU Direct Post 3D form must be posted to the sale3d endpoint of the
     * secure session token, not the merchant id (the old merchant-in-URL flow
     * answers "ERR10097 Sonlandırılmış oturum bilgisi").
     */
    protected function get3dUrl(string $sessionToken): string
    {
        $provider = $this->getProvider() ?? Provider::PAYTEN;

        if (!isset(Provider::PROVIDERS[$provider])) {
            throw new OmnipayPaytenInvalidProviderException(
                "Invalid provider: {$provider}. Valid providers: " . implode(', ', Provider::VALID_PROVIDERS)
            );
        }

        $key = $this->getTestMode() ? 'test_3d' : 'live_3d';

        $url = Provider::PROVIDERS[$provider][$key];

        return str_replace('{sessionToken}', $sessionToken, $url);
    }

    protected function get_card($key)
    {
        return $this->getCard() ? $this->getCard()->$key() : null;
    }

    abstract protected function createResponse($data);
}
