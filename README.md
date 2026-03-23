# Omnipay: Payten (Merchant Safe Unipay)

**Payten (MSU API v2) gateway for the Omnipay PHP payment processing library**

[![Latest Stable Version](https://poser.pugx.org/tcgunel/omnipay-payten/v/stable)](https://packagist.org/packages/tcgunel/omnipay-payten)
[![Total Downloads](https://poser.pugx.org/tcgunel/omnipay-payten/downloads)](https://packagist.org/packages/tcgunel/omnipay-payten)
[![License](https://poser.pugx.org/tcgunel/omnipay-payten/license)](https://packagist.org/packages/tcgunel/omnipay-payten)

[Omnipay](https://github.com/thephpleague/omnipay) is a framework agnostic, multi-gateway payment processing library for PHP.
This package implements Payten (Merchant Safe Unipay) support for Omnipay.

## Sub-Providers

This package supports 4 sub-providers that share the same MSU API v2 protocol. Select the provider via the `provider` parameter:

| Provider | Constant | Test API | Live API |
|----------|----------|----------|----------|
| **Payten** (default) | `Provider::PAYTEN` | entegrasyon.asseco-see.com.tr | merchantsafeunipay.com |
| **Paratika** | `Provider::PARATIKA` | entegrasyon.paratika.com.tr | vpos.paratika.com.tr |
| **VakifPays** | `Provider::VAKIFPAYS` | testpos.vakifpays.com.tr | pos.vakifpays.com.tr |
| **ZiraatPay** | `Provider::ZIRAATPAY` | test.ziraatpay.com.tr | vpos.ziraatpay.com.tr |

## Installation

```bash
composer require tcgunel/omnipay-payten
```

## Usage

### Gateway Initialization

```php
use Omnipay\Omnipay;
use Omnipay\Payten\Constants\Provider;

$gateway = Omnipay::create('Payten');

$gateway->setMerchantId('YOUR_MERCHANT_ID');
$gateway->setMerchantUser('YOUR_MERCHANT_USER');
$gateway->setMerchantPassword('YOUR_MERCHANT_PASSWORD');
$gateway->setMerchantStorekey('YOUR_STOREKEY'); // optional (DEALERTYPENAME)
$gateway->setProvider(Provider::PAYTEN); // payten, paratika, vakifpays, ziraatpay
$gateway->setTestMode(true); // false for production
```

### Provider Selection

```php
// Use Paratika
$gateway->setProvider(Provider::PARATIKA);

// Use VakifPays
$gateway->setProvider(Provider::VAKIFPAYS);

// Use ZiraatPay
$gateway->setProvider(Provider::ZIRAATPAY);
```

### Non-3D Sale (Direct Payment)

```php
$response = $gateway->purchase([
    'amount'        => '100.00',
    'currency'      => 'TRY',
    'transactionId' => 'ORDER-12345',
    'installment'   => '1',
    'secure'        => false,
    'card'          => [
        'firstName'  => 'John',
        'lastName'   => 'Doe',
        'number'     => '4355084355084358',
        'expiryMonth'=> '12',
        'expiryYear' => '2030',
        'cvv'        => '000',
        'email'      => 'john@example.com',
    ],
    'clientIp'      => '127.0.0.1',
])->send();

if ($response->isSuccessful()) {
    echo "Payment successful! Transaction ID: " . $response->getTransactionReference();
} else {
    echo "Payment failed: " . $response->getMessage();
}
```

### 3D Secure Payment

```php
$response = $gateway->purchase([
    'amount'        => '100.00',
    'currency'      => 'TRY',
    'transactionId' => 'ORDER-12345',
    'installment'   => '1',
    'secure'        => true,
    'returnUrl'     => 'https://yoursite.com/payment/callback',
    'card'          => [
        'firstName'  => 'John',
        'lastName'   => 'Doe',
        'number'     => '4355084355084358',
        'expiryMonth'=> '12',
        'expiryYear' => '2030',
        'cvv'        => '000',
        'email'      => 'john@example.com',
    ],
    'clientIp'      => '127.0.0.1',
])->send();

if ($response->isRedirect()) {
    // Redirect the customer to 3D Secure page
    $response->redirect();
}
```

### Complete Purchase (3D Callback)

Handle the callback from the 3D Secure redirect:

```php
$response = $gateway->completePurchase()->send();

if ($response->isSuccessful()) {
    echo "3D Payment successful!";
    echo "Transaction ID: " . $response->getTransactionId();
    echo "PG Reference: " . $response->getTransactionReference();
} else {
    echo "3D Payment failed: " . $response->getMessage();
    echo "Error code: " . $response->getCode();
}
```

### Refund

```php
$response = $gateway->refund([
    'transactionId' => 'ORDER-12345',
    'amount'        => '50.00',
    'currency'      => 'TRY',
])->send();

if ($response->isSuccessful()) {
    echo "Refund successful!";
} else {
    echo "Refund failed: " . $response->getMessage();
}
```

### Void (Cancel)

```php
$response = $gateway->void([
    'transactionId' => 'ORDER-12345',
])->send();

if ($response->isSuccessful()) {
    echo "Void successful!";
} else {
    echo "Void failed: " . $response->getMessage();
}
```

### Installment Query

```php
$response = $gateway->installmentQuery([
    'card'     => ['number' => '435508'], // First 6 digits (BIN)
    'amount'   => '100.00',
    'currency' => 'TRY',
])->send();

if ($response->isSuccessful()) {
    $data = $response->getData();
    // $data->installmentPlanList contains available installment options
}
```

### Transaction Query

```php
$response = $gateway->transactionQuery([
    'transactionId' => 'ORDER-12345',
])->send();

if ($response->isSuccessful()) {
    $data = $response->getData();
    echo "Amount: " . $data->amount;
    echo "Currency: " . $data->currency;
}
```

## Authentication Parameters

| Parameter | MSU Field | Description |
|-----------|-----------|-------------|
| `merchantId` | `MERCHANT` | Merchant ID |
| `merchantUser` | `MERCHANTUSER` | Merchant user |
| `merchantPassword` | `MERCHANTPASSWORD` | Merchant password |
| `merchantStorekey` | `DEALERTYPENAME` | Dealer type name / store key (optional) |

## API Response Codes

- `00` - Success (Approved)
- Any other code - Error (check `errorMsg` for details)

## Supported Methods

| Method | MSU ACTION | Description |
|--------|-----------|-------------|
| `purchase()` | `SALE` | Direct or 3D Secure payment |
| `completePurchase()` | - | Handle 3D callback |
| `refund()` | `REFUND` | Partial or full refund |
| `void()` | `VOID` | Cancel a transaction |
| `installmentQuery()` | `QUERYINSTALLMENTS` | Query available installments by BIN |
| `transactionQuery()` | `QUERYTRANSACTION` | Query transaction status |

## Testing

```bash
composer test
```

## License

MIT License. See [LICENSE](LICENSE) for details.
