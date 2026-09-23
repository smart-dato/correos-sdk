# Correos SDK

[![Latest Version on Packagist](https://img.shields.io/packagist/v/smart-dato/correos-sdk.svg?style=flat-square)](https://packagist.org/packages/smart-dato/correos-sdk)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/smart-dato/correos-sdk/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/smart-dato/correos-sdk/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/smart-dato/correos-sdk/code-style.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/smart-dato/correos-sdk/actions?query=workflow%3A%22Code+style%22+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/smart-dato/correos-sdk.svg?style=flat-square)](https://packagist.org/packages/smart-dato/correos-sdk)

A small Laravel package for pre-registering Correos (Spanish postal service) shipments over SOAP and fetching their tracking events.

> For labels, customs documents and a fuller API surface, see [`smart-dato/correos-shipping-sdk`](https://github.com/smart-dato/correos-shipping-sdk).

## Requirements

- PHP 8.2+
- Laravel 10 – 13
- The `soap` PHP extension

## Installation

```bash
composer require smart-dato/correos-sdk
```

Publish the config file:

```bash
php artisan vendor:publish --tag="correos-sdk-config"
```

```dotenv
CORREOS_SDK_BASE_URL=https://your-correos-host
CORREOS_SDK_USERNAME=your-username
CORREOS_SDK_PASSWORD=your-password
```

SOAP calls go to `{base_url}/preregistroenvios`.

## Usage

The `CorreosSdk` facade and container binding use the configured credentials:

```php
use SmartDato\CorreosSdk\Facades\CorreosSdk;

$correos = CorreosSdk::getFacadeRoot();
```

Or construct the client yourself:

```php
use SmartDato\CorreosSdk\CorreosSdk;

$correos = new CorreosSdk(
    baseUrl: 'https://your-correos-host',
    username: 'your-username',
    password: 'your-password',
);
```

### Pre-register a shipment

Calls the `PreRegistroMultibulto` SOAP operation.

```php
use SmartDato\CorreosSdk\Enums\DeliveryModeEnum;
use SmartDato\CorreosSdk\Enums\LabelModeEnum;
use SmartDato\CorreosSdk\Enums\PostageTypeEnum;
use SmartDato\CorreosSdk\Payloads\AddressPayload;
use SmartDato\CorreosSdk\Payloads\ParcelPayload;
use SmartDato\CorreosSdk\Payloads\ShipmentPayload;
use SmartDato\CorreosSdk\Payloads\ShippingPartyPayload;

$result = $correos->createShipment(new ShipmentPayload(
    date: '...',                // sent as FechaOperacion
    parcelCount: 1,
    senderInfo: new ShippingPartyPayload(
        name: 'Sender S.L.',
        address: new AddressPayload(address: 'Calle Mayor 1', city: 'Madrid'),
        zipcode: '28013',
        phone: '910000000',
        email: 'sender@example.com',
    ),
    receiverInfo: new ShippingPartyPayload(
        name: 'Jane Doe',
        address: new AddressPayload(address: 'Avinguda Diagonal 100', city: 'Barcelona'),
        zipcode: '08019',
        phone: '930000000',
        email: 'jane@example.com',
    ),
    parcels: [
        new ParcelPayload(parcelNumber: 1, weight: 1.5, length: 30, height: 20, width: 10),
    ],
    totalWeight: 1.5,
    labelCode: 'your-labeler-code', // CodEtiquetador, issued by Correos
    productCode: 'your-product-code', // CodProducto
    postageType: PostageTypeEnum::POSTAGE_PAID,   // TipoFranqueo
    deliveryMode: DeliveryModeEnum::STANDARD,     // ModalidadEntrega
    modDevLabel: (int) LabelModeEnum::PDF->value,
));

$result['request'];  // raw SOAP request XML
$result['response']; // raw SOAP response XML
```

`createShipment()` returns the raw SOAP request and response rather than a parsed object, and throws `SoapFault` on transport errors.

Values such as the operation date and weights are passed to Correos unchanged — the SDK does not enforce a date format or weight unit, so use whatever your Correos contract specifies.

`postageType` and `deliveryMode` default to `POSTAGE_PAID` and `STANDARD`.

### Track a shipment

```php
$events = $correos->getTracking('your-shipment-code');
```

Returns the decoded JSON from Correos's `eventos_envio_servicio_auth` endpoint.

### Enums

| Enum | Cases |
|---|---|
| `DeliveryModeEnum` | `STANDARD` (`ST`), `IN_SELECTED_BRANCH` (`LS`), `IN_REFERENCE_BRANCH` (`OR`), `CITYPAQ` (`CP`) |
| `PostageTypeEnum` | `POSTAGE_PAID` (`FP`), `MACHINE_FRANKING` (`FM`), `CASH` (`ES`), `ONLINE_PAYMENT` (`ON`) |
| `LabelModeEnum` | `XML` (`1`), `PDF` (`2`), `ZPL` (`3`) |
| `WeightTypeEnum` | `REAL` (`R`), `VOLUMETRIC` (`V`) |

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [SmartDato](https://github.com/smart-dato)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
