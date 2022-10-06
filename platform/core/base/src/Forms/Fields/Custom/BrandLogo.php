<?php

namespace Botble\Base\Forms\Fields\Custom;

use Assets;
use Kris\LaravelFormBuilder\Fields\FormField;
class BrandLogo extends FormField
{
    protected function getTemplate()
    {
        return 'core/base::forms.fields.custom.brand-logo';
    }
}
