<?php

namespace SmartDato\CorreosSdk\Enums;

enum DeliveryModeEnum: string
{
    case STANDARD = 'ST';
    case IN_SELECTED_BRANCH = 'LS';
    case IN_REFERENCE_BRANCH = 'OR';
    case CITYPAQ = 'CP';
}
