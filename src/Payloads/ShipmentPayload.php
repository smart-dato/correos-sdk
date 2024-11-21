<?php

namespace SmartDato\CorreosSdk\Payloads;

use SmartDato\CorreosSdk\Contracts\PayloadContract;
use SmartDato\CorreosSdk\Enums\DeliveryModeEnum;
use SmartDato\CorreosSdk\Enums\LabelModeEnum;
use SmartDato\CorreosSdk\Enums\PostageTypeEnum;

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
        protected string $aggregationCode = '000000',
        protected DeliveryModeEnum $deliveryMode = DeliveryModeEnum::STANDARD,
        protected PostageTypeEnum $postageType = PostageTypeEnum::POSTAGE_PAID,
        protected LabelModeEnum $labelMode = LabelModeEnum::PDF,
    ) {}

    public function build(): array
    {
        return [
            'FechaOperacion' => $this->date,
            'CodEtiquetador' => $this->labelCode,
            'Care' => $this->aggregationCode,
            'TotalBultos' => $this->parcelCount,
            'ModDevEtiqueta' => $this->labelMode->value,
            'Remitente' => $this->senderInfo->build(),
            'Destinatario' => $this->receiverInfo->build(),
            'Envios' => array_map(
                fn (ParcelPayload $parcel) => $parcel->build(),
                $this->parcels
            ),
            'PesoTotal' => $this->totalWeight,
            'CodProducto' => $this->productCode,
            'ModalidadEntrega' => $this->deliveryMode->value,
            'TipoFranqueo' => $this->postageType->value,
        ];
    }
}
