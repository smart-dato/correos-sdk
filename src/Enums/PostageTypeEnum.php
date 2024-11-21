<?php

namespace SmartDato\CorreosSdk\Enums;

enum PostageTypeEnum: string
{
    case POSTAGE_PAID = 'FP';
    case MACHINE_FRANKING = 'FM';
    case CASH = 'ES';
    case ONLINE_PAYMENT = 'ON';
}
