<?php

namespace SmartDato\CorreosSdk;

use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use SmartDato\CorreosSdk\Payloads\ShipmentPayload;
use SoapClient;
use SoapFault;

class CorreosSdk
{
    protected ?Request $lastRequest = null;

    protected ?Response $lastResponse = null;

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
        $this->lastRequest = null;
        $this->lastResponse = null;

        $response = Http::baseUrl($this->baseUrl)
            ->withBasicAuth(
                $this->username,
                $this->password
            )
            ->withQueryParameters([
                'codIdioma' => 'EN',
                'indUltEvento' => 'N',
            ])
            ->beforeSending(function (Request $request): void {
                $this->lastRequest = $request;
            })
            ->get('/canonico/eventos_envio_servicio_auth/'.$shipmentReference);

        $this->lastResponse = $response;

        return $response->json() ?? [];
    }

    /**
     * The HTTP request of the last getTracking() call, also set when the connection failed.
     */
    public function lastRequest(): ?Request
    {
        return $this->lastRequest;
    }

    /**
     * The HTTP response of the last getTracking() call.
     */
    public function lastResponse(): ?Response
    {
        return $this->lastResponse;
    }
}
