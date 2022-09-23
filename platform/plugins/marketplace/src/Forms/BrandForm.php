<?php

namespace Botble\Marketplace\Forms;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Forms\FormAbstract;
use Botble\Ecommerce\Http\Requests\BrandRequest;
use Botble\Ecommerce\Models\Brand;
use Botble\Marketplace\Http\Controllers\Fronts\BrandController;
use Illuminate\Support\Facades\Route;
use  MarketplaceHelper;
class BrandForm extends FormAbstract
{

    public function __construct()
    {
        $this->setFormOption('template', MarketplaceHelper::viewPath('dashboard.forms.base'))
        ->setFormOption('enctype', 'multipart/form-data')
        ->setFormOption('method', 'POST');
        // ->setFormOption('url', $url);

    }



    /**
     * {@inheritDoc}
     */
    public function buildForm()
    {
        $this
            ->setupModel(new Brand)
            ->setValidatorClass(BrandRequest::class)
            ->withCustomFields()
        ->setActionButtons(MarketplaceHelper::view('dashboard.forms.actions')->render())
            ->add('name', 'text', [
                'label'      => trans('core/base::forms.name'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 120,
                ],
            ])
            ->add('description', 'editor', [
                'label'      => trans('core/base::forms.description'),
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'rows'         => 4,
                    'placeholder'  => trans('plugins/ecommerce::products.form.description'),
                    'data-counter' => 400,
                ],
            ])
            ->add('website', 'text', [
                'label'      => trans('plugins/ecommerce::brands.form.website'),
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'placeholder'  => 'Ex: https://example.com',
                    'data-counter' => 120,
                ],
            ])
            ->add('order', 'number', [
                'label'         => trans('core/base::forms.order'),
                'label_attr'    => ['class' => 'control-label'],
                'attr'          => [
                    'placeholder' => trans('core/base::forms.order_by_placeholder'),
                ],
                'default_value' => 0,
            ])
            ->add('status', 'customSelect', [
                'label'      => trans('core/base::tables.status'),
                'label_attr' => ['class' => 'control-label required'],
                'choices'    => BaseStatusEnum::labels(),
            ])
            // ->add('logo', 'mediaImage', [
            //     'label'      => trans('plugins/ecommerce::brands.logo'),
            //     'label_attr' => ['class' => 'control-label'],
            // ])
            ->add('is_featured', 'onOff', [
                'label'         => trans('plugins/ecommerce::brands.form.is_featured'),
                'label_attr'    => ['class' => 'control-label'],
                'default_value' => false,
            ])
            ->setBreakFieldPoint('status');
    }
}
