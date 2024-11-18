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
        protected string $email,
        protected ?AddressPayload $address2 = null,
    ) {}

    public function build(): array
    {
        $payload = [
            'Identificacion' => [
                'Nombre' => $this->name,
            ],
            'DatosDireccion' => $this->address->build(),
            'CP' => $this->zipcode,
            'Telefonocontacto' => $this->phone,
            'Email' => $this->email,
        ];

        if (! empty($this->address2)) {
            $payload['DatosDireccion2'] = $this->address2->build();
        }

        return $payload;
    }
}
