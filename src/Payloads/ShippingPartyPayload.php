<?php

namespace SmartDato\CorreosSdk\Payloads;

use SmartDato\CorreosSdk\Contracts\PayloadContract;

class ShippingPartyPayload implements PayloadContract
{
    public function __construct(
        protected string $name,
        protected AddressPayload $address,
        protected string $zipcode,
        protected string $phone,
        protected ?string $email,
        protected ?AddressPayload $address2 = null,
        protected ?string $country = null,
        protected bool $useCP = true,
    ) {}

    public function build(): array
    {
        $payload = [
            'Identificacion' => [
                'Nombre' => $this->name,
            ],
            'DatosDireccion' => $this->address->build(),
            'Telefonocontacto' => $this->phone,
            'Email' => $this->email,
        ];

        if ($this->useCP) {
            // only take first 5 characters
            $payload['CP'] = str($this->zipcode)->take(5)->value();
        } else {
            $payload['ZIP'] = $this->zipcode;
        }

        if (! empty($this->address2)) {
            $payload['DatosDireccion2'] = $this->address2->build();
        }

        if (! empty($this->country)) {
            $payload['Pais'] = $this->country;
        }

        return $payload;
    }
}
