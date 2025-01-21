<?php

namespace SmartDato\CorreosSdk\Payloads;

use SmartDato\CorreosSdk\Contracts\PayloadContract;

class ParcelPayload implements PayloadContract
{
    public function __construct(
        protected int $parcelNumber,
        protected float $weight,
        protected int $length,
        protected int $height,
        protected int $width,
        protected string $weightType = 'R',
    ) {}

    public function build(): array
    {
        return [
            'NumBulto' => $this->parcelNumber,
            'Pesos' => [
                'Peso' => [
                    'TipoPeso' => $this->weightType,
                    'Valor' => $this->weight,
                ],
            ],
            'Largo' => $this->length,
            'Alto' => $this->height,
            'Ancho' => $this->width,
        ];
    }
}
