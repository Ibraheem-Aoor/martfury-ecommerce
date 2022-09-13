<?php

namespace Botble\Marketplace\Forms\Fields;

use Kris\LaravelFormBuilder\Fields\FormField;
use MarketplaceHelper;

class CustomImagesField extends FormField
{

    /**
     * @return string
     */
    protected function getTemplate()
    {
        return MarketplaceHelper::viewPath('dashboard.forms.fields.custom-images');
    }
}
