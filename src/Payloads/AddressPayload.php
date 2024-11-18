<?php

namespace SmartDato\CorreosSdk\Payloads;

use SmartDato\CorreosSdk\Contracts\PayloadContract;

class AddressPayload implements PayloadContract
{
    public function __construct(
        protected string $address,
        protected string $city,
        protected ?string $state = null,
    ) {}

    public function build(): array
    {
        $payload = [
            'Direccion' => $this->address,
            'Localidad' => $this->city,
        ];

        if ($this->state) {
            $payload['Provincia'] = $this->state;
        }

        return $payload;
    }
}
