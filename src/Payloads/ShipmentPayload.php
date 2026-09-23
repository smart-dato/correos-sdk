<?php

namespace SmartDato\CorreosSdk\Payloads;

use SmartDato\CorreosSdk\Contracts\AddOnValueContract;
use SmartDato\CorreosSdk\Contracts\PayloadContract;
use SmartDato\CorreosSdk\Enums\DeliveryModeEnum;
use SmartDato\CorreosSdk\Enums\PostageTypeEnum;

class ShipmentPayload implements PayloadContract
{
    /**
     * @param  ParcelPayload[]  $parcels
     * @param  AddOnValueContract[]  $addOnValues
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
        protected PostageTypeEnum $postageType = PostageTypeEnum::POSTAGE_PAID,
        protected DeliveryModeEnum $deliveryMode = DeliveryModeEnum::STANDARD,
        protected int $modDevLabel = 2,
        protected array $addOnValues = []

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
            'ModalidadEntrega' => $this->deliveryMode->value,
            'TipoFranqueo' => $this->postageType->value,
            'ValoresAnadidos' => array_merge(
                [],
                ...array_map(
                    fn (AddOnValueContract $addOn) => (array) $addOn->build(),
                    $this->addOnValues
                )
            ),
        ];
    }
}
