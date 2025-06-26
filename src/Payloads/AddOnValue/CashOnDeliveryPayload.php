<?php

namespace SmartDato\CorreosSdk\Payloads\AddOnValue;

use SmartDato\CorreosSdk\Contracts\AddOnValueContract;

class CashOnDeliveryPayload implements AddOnValueContract
{
    public function __construct(
        private float $amount, // euro
        private string $type = 'RC', // RC = Reembolso, RT = Reembolso con transferencia
        private string $accountNumber = '', // aka IBAN
        private string $groupedTransfer = 'S'
    ) {}

    public function build(): array
    {
        return [
            'Reembolso' => [
                'Importe' => (int) ($this->amount * 100),
                'TipoReembolso' => $this->type,
                'NumeroCuenta' => $this->accountNumber,
                'Transferagrupada' => $this->groupedTransfer,
            ],
        ];
    }
}
