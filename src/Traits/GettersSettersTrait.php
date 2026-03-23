<?php

namespace Omnipay\Payten\Traits;

trait GettersSettersTrait
{
    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
    }

    public function getMerchantUser()
    {
        return $this->getParameter('merchantUser');
    }

    public function setMerchantUser($value)
    {
        return $this->setParameter('merchantUser', $value);
    }

    public function getMerchantPassword()
    {
        return $this->getParameter('merchantPassword');
    }

    public function setMerchantPassword($value)
    {
        return $this->setParameter('merchantPassword', $value);
    }

    public function getMerchantStorekey()
    {
        return $this->getParameter('merchantStorekey');
    }

    public function setMerchantStorekey($value)
    {
        return $this->setParameter('merchantStorekey', $value);
    }

    public function getProvider()
    {
        return $this->getParameter('provider');
    }

    public function setProvider($value)
    {
        return $this->setParameter('provider', $value);
    }

    public function getInstallment()
    {
        return $this->getParameter('installment');
    }

    public function setInstallment($value)
    {
        return $this->setParameter('installment', $value);
    }

    public function getSecure()
    {
        return $this->getParameter('secure');
    }

    public function setSecure($value)
    {
        return $this->setParameter('secure', $value);
    }

    public function getOrderNumber()
    {
        return $this->getParameter('orderNumber');
    }

    public function setOrderNumber($value)
    {
        return $this->setParameter('orderNumber', $value);
    }

    public function getCustomer()
    {
        return $this->getParameter('customer');
    }

    public function setCustomer($value)
    {
        return $this->setParameter('customer', $value);
    }

    public function getCustomerName()
    {
        return $this->getParameter('customerName');
    }

    public function setCustomerName($value)
    {
        return $this->setParameter('customerName', $value);
    }

    public function getCustomerEmail()
    {
        return $this->getParameter('customerEmail');
    }

    public function setCustomerEmail($value)
    {
        return $this->setParameter('customerEmail', $value);
    }

    public function getCustomerIp()
    {
        return $this->getParameter('customerIp');
    }

    public function setCustomerIp($value)
    {
        return $this->setParameter('customerIp', $value);
    }

    public function getCampaignCode()
    {
        return $this->getParameter('campaignCode');
    }

    public function setCampaignCode($value)
    {
        return $this->setParameter('campaignCode', $value);
    }

    public function getEndpoint()
    {
        return $this->endpoint;
    }
}
