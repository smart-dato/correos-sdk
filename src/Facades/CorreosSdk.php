<?php

namespace SmartDato\CorreosSdk\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \SmartDato\CorreosSdk\CorreosSdk
 */
class CorreosSdk extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \SmartDato\CorreosSdk\CorreosSdk::class;
    }
}
