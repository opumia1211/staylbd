<?php

namespace App\Routing;

use Illuminate\Routing\UrlGenerator;

/**
 * Normalizes positional route parameters for storefront routes that use {locale?} prefix.
 */
class StorefrontUrlGenerator extends UrlGenerator
{
    public function route($name, $parameters = [], $absolute = true)
    {
        $parameters = storefront_normalize_route_parameters($name, $parameters);

        return parent::route($name, $parameters, $absolute);
    }
}
