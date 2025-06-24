<?php

namespace SmartDato\CorreosSdk\Payloads\AddOnValue;

use SmartDato\CorreosSdk\Contracts\AddOnValueContract;

class RefundPayload implements AddOnValueContract
{
    public function __construct(
        private float $amount,
        private string $type = '01' // Default type is '01' for cash on delivery
    ) {}

    public function build(): array
    {
        return [
            'Reembolso' => [
                'Importe' => $this->amount,
                'TipoReembolso' => $this->type,
            ],
        ];
    }
}
