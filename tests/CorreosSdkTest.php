<?php

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use SmartDato\CorreosSdk\CorreosSdk;
use SmartDato\CorreosSdk\Enums\DeliveryModeEnum;
use SmartDato\CorreosSdk\Enums\PostageTypeEnum;
use SmartDato\CorreosSdk\Facades\CorreosSdk as CorreosSdkFacade;
use SmartDato\CorreosSdk\Payloads\AddressPayload;
use SmartDato\CorreosSdk\Payloads\ParcelPayload;
use SmartDato\CorreosSdk\Payloads\ShipmentPayload;
use SmartDato\CorreosSdk\Payloads\ShippingPartyPayload;

function correosShipment(array $overrides = []): ShipmentPayload
{
    $party = new ShippingPartyPayload(
        name: 'Jane Doe',
        address: new AddressPayload(address: 'Calle Mayor 1', city: 'Madrid'),
        zipcode: '28013',
        phone: '910000000',
        email: 'jane@example.com',
    );

    return new ShipmentPayload(...array_merge([
        'date' => '2026-09-23',
        'parcelCount' => 1,
        'senderInfo' => $party,
        'receiverInfo' => $party,
        'parcels' => [new ParcelPayload(parcelNumber: 1, weight: 1.5, length: 30, height: 20, width: 10)],
        'totalWeight' => 1.5,
        'labelCode' => 'LABELER',
        'productCode' => 'PRODUCT',
    ], $overrides));
}

it('sends the postage type and delivery mode to the matching fields', function () {
    $payload = correosShipment([
        'postageType' => PostageTypeEnum::ONLINE_PAYMENT,
        'deliveryMode' => DeliveryModeEnum::CITYPAQ,
    ])->build();

    expect($payload['TipoFranqueo'])->toBe('ON')
        ->and($payload['ModalidadEntrega'])->toBe('CP');
});

it('defaults to postage paid and standard delivery', function () {
    $payload = correosShipment()->build();

    expect($payload['TipoFranqueo'])->toBe('FP')
        ->and($payload['ModalidadEntrega'])->toBe('ST');
});

it('resolves the facade from the config', function () {
    config()->set('correos-sdk.base_url', 'https://correos.example');

    expect(CorreosSdkFacade::getFacadeRoot())->toBeInstanceOf(CorreosSdk::class);
});

it('keeps the last tracking request and response', function () {
    Http::fake(['*' => Http::response(['eventos' => [['codEvento' => 'A1']]])]);

    $sdk = new CorreosSdk('https://correos.example', 'user', 'secret');

    $events = $sdk->getTracking('PQ123');

    expect($events)->toBe(['eventos' => [['codEvento' => 'A1']]])
        ->and($sdk->lastRequest()->method())->toBe('GET')
        ->and($sdk->lastRequest()->url())->toBe('https://correos.example/canonico/eventos_envio_servicio_auth/PQ123?codIdioma=EN&indUltEvento=N')
        ->and($sdk->lastResponse()->body())->toBe('{"eventos":[{"codEvento":"A1"}]}');
});

it('returns no events when the tracking response is not json', function () {
    Http::fake(['*' => Http::response('<html>Unauthorized</html>', 401)]);

    $sdk = new CorreosSdk('https://correos.example', 'user', 'secret');

    expect($sdk->getTracking('PQ123'))->toBe([])
        ->and($sdk->lastResponse()->status())->toBe(401);
});

it('keeps the last tracking request when the connection fails', function () {
    Http::fake(fn () => throw new ConnectionException('timeout'));

    $sdk = new CorreosSdk('https://correos.example', 'user', 'secret');

    expect(fn () => $sdk->getTracking('PQ123'))->toThrow(ConnectionException::class)
        ->and($sdk->lastRequest()->url())->toContain('/PQ123')
        ->and($sdk->lastResponse())->toBeNull();
});
