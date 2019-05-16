<?php namespace Modules\Backend\Core;

use Illuminate\Support\Facades\Facade;

class Twig extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'parse.twig';
    }
}
