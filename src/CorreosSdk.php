<?php

namespace SmartDato\CorreosSdk;

use Illuminate\Support\Facades\Http;
use SmartDato\CorreosSdk\Payloads\ShipmentPayload;
use SoapClient;
use SoapFault;

class CorreosSdk
{
    public function __construct(
        protected string $baseUrl = '',
        protected string $username = '',
        protected string $password = '',
    ) {}

    /**
     * @throws SoapFault
     */
    public function createShipment(ShipmentPayload $payload): array
    {
        $wsdl = __DIR__.'/../resources/wsdl/shipment.wsdl';
        $options = [
            'trace' => true,
            'exceptions' => true,
            'login' => $this->username,
            'password' => $this->password,
            'cache_wsdl' => WSDL_CACHE_NONE,
        ];
        $client = new SoapClient($wsdl, $options);
        $client->__setLocation($this->baseUrl.'/preregistroenvios');

        $client->PreRegistroMultibulto($payload->build());

        return [
            'request' => $client->__getLastRequest(),
            'response' => $client->__getLastResponse(),
        ];
    }

    public function getTracking(string $shipmentReference): array
    {
        $response = Http::baseUrl($this->baseUrl)
            ->withBasicAuth(
                $this->username,
                $this->password
            )
            ->withQueryParameters([
                'codIdioma' => 'EN',
                'indUltEvento' => 'N',
            ])
            ->get('/canonico/eventos_envio_servicio_auth/'.$shipmentReference);

        return $response->json();
    }
}
