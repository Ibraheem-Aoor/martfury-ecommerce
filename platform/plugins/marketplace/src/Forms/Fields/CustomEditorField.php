<?php

namespace Botble\Marketplace\Forms\Fields;

use Kris\LaravelFormBuilder\Fields\FormField;
use MarketplaceHelper;

class CustomEditorField extends FormField
{

    /**
     * @param array $options
     * @param bool $showLabel
     * @param bool $showField
     * @param bool $showError
     * @return string
     */
    public function render(array $options = [], $showLabel = true, $showField = true, $showError = true)
    {
        return parent::render($options, $showLabel, $showField, $showError);
    }

    /**
     * @return string
     */
    protected function getTemplate()
    {
        return MarketplaceHelper::viewPath('dashboard.forms.fields.custom-editor');
    }
}
