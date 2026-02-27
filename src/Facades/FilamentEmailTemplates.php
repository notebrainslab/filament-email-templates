<?php

namespace NoteBrainsLab\FilamentEmailTemplates\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \NoteBrainsLab\FilamentEmailTemplates\FilamentEmailTemplates
 */
class FilamentEmailTemplates extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \NoteBrainsLab\FilamentEmailTemplates\FilamentEmailTemplates::class;
    }
}
