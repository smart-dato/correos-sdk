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
        protected string $labelCode,
        protected string $productCode,
        protected string $deliveryMode = 'FP',
        protected string $shippingType = 'ST',
        protected int $modDevLabel = 2,

    ) {}

    public function build(): array
    {
        return [
            'FechaOperacion' => $this->date,
            'CodEtiquetador' => $this->labelCode,
            'Care' => '',
            'TotalBultos' => $this->parcelCount,
            'ModDevEtiqueta' => $this->modDevLabel,
            'Remitente' => $this->senderInfo->build(),
            'Destinatario' => $this->receiverInfo->build(),
            'Envios' => array_map(
                fn (ParcelPayload $parcel) => $parcel->build(),
                $this->parcels
            ),
            'PesoTotal' => $this->totalWeight,
            'CodProducto' => $this->productCode,
            'ModalidadEntrega' => $this->shippingType,
            'TipoFranqueo' => $this->deliveryMode,
        ];
    }
}
