<?php

namespace SmartDato\CorreosSdk\Payloads;

use SmartDato\CorreosSdk\Contracts\PayloadContract;

class ShipmentPayload implements PayloadContract
{
    /**
     * @param  ParcelPayload[]  $parcels
     */
    public function __construct(
        protected string $date,
        protected int $parcelCount,
        protected ShippingPartyPayload $senderInfo,
        protected ShippingPartyPayload $receiverInfo,
        protected array $parcels,
        protected float $totalWeight,
    ) {}

    public function build(): array
    {
        return [
            'FechaOperacion' => $this->date,
            'CodEtiquetador' => 'XXX1',
            'Care' => '',
            'TotalBultos' => $this->parcelCount,
            'ModDevEtiqueta' => 2,
            'Remitente' => $this->senderInfo->build(),
            'Destinatario' => $this->receiverInfo->build(),
            'Envios' => array_map(
                fn (ParcelPayload $parcel) => $parcel->build(),
                $this->parcels
            ),
            'PesoTotal' => $this->totalWeight,
            'CodProducto' => 'S0132',
            'ModalidadEntrega' => 'ST',
            'TipoFranqueo' => 'FP',
        ];
    }
}
