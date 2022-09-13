<?php

namespace Botble\Marketplace\Facades;

use Botble\Marketplace\Supports\MarketplaceHelper;
use Illuminate\Support\Facades\Facade;

class MarketplaceHelperFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return MarketplaceHelper::class;
    }
}
