<?php

namespace Botble\Marketplace\Forms;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Forms\FormAbstract;
use Botble\Ecommerce\Enums\CustomerStatusEnum;
use Botble\Ecommerce\Repositories\Interfaces\CustomerInterface;
use Botble\Marketplace\Http\Requests\StoreRequest;
use Botble\Marketplace\Models\Store;

class StoreForm extends FormAbstract
{
    /**
     * @var string
     */
    protected $template = 'core/base::forms.form-tabs';

    /**
     * @var CustomerInterface
     */
    protected $customerRepository;

    /**
     * StoreForm constructor.
     * @param CustomerInterface $customerRepository
     */
    public function __construct(CustomerInterface $customerRepository)
    {
        parent::__construct();
        $this->customerRepository = $customerRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm()
    {

        $customers = $this->customerRepository->pluck('name', 'id', ['is_vendor' => true]);

        $this
            ->setupModel(new Store)
            ->setValidatorClass(StoreRequest::class)
            ->withCustomFields()
            ->add('name', 'text', [
                'label'      => trans('core/base::forms.name'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 120,
                ],
            ])
            ->add('rowOpen1', 'html', [
                'html' => '<div class="row">',
            ])
            ->add('email', 'email', [
                'label'      => trans('plugins/marketplace::store.forms.email'),
                'label_attr' => ['class' => 'control-label'],
                'wrapper'    => [
                    'class' => $this->formHelper->getConfig('defaults.wrapper_class') . ' col-md-6',
                ],
                'attr'       => [
                    'placeholder'  => trans('plugins/marketplace::store.forms.email_placeholder'),
                    'data-counter' => 60,
                ],
            ])
            ->add('phone', 'text', [
                'label'      => trans('plugins/marketplace::store.forms.phone'),
                'label_attr' => ['class' => 'control-label'],
                'wrapper'    => [
                    'class' => $this->formHelper->getConfig('defaults.wrapper_class') . ' col-md-6',
                ],
                'attr'       => [
                    'placeholder'  => trans('plugins/marketplace::store.forms.phone_placeholder'),
                    'data-counter' => 15,
                ],
            ])
            ->add('rowClose', 'html', [
                'html' => '</div>',
            ])
            ->add('rowOpen2', 'html', [
                'html' => '<div class="row">',
            ])
            ->add('address', 'text', [
                'label'      => trans('plugins/marketplace::store.forms.address'),
                'label_attr' => ['class' => 'control-label'],
                'wrapper'    => [
                    'class' => $this->formHelper->getConfig('defaults.wrapper_class') . ' col-md-6',
                ],
                'attr'       => [
                    'placeholder'  => trans('plugins/marketplace::store.forms.address_placeholder'),
                    'data-counter' => 120,
                ],
            ])
            ->add('city', 'text', [
                'label'      => trans('plugins/marketplace::store.forms.city'),
                'label_attr' => ['class' => 'control-label'],
                'wrapper'    => [
                    'class' => $this->formHelper->getConfig('defaults.wrapper_class') . ' col-md-6',
                ],
                'attr'       => [
                    'placeholder'  => trans('plugins/marketplace::store.forms.city_placeholder'),
                    'data-counter' => 120,
                ],
            ])
            ->add('rowClose2', 'html', [
                'html' => '</div>',
            ])
            ->add('rowOpen3', 'html', [
                'html' => '<div class="row">',
            ])
            ->add('state', 'text', [
                'label'      => trans('plugins/marketplace::store.forms.state'),
                'label_attr' => ['class' => 'control-label'],
                'wrapper'    => [
                    'class' => $this->formHelper->getConfig('defaults.wrapper_class') . ' col-md-6',
                ],
                'attr'       => [
                    'placeholder'  => trans('plugins/marketplace::store.forms.state_placeholder'),
                    'data-counter' => 120,
                ],
            ])
            ->add('country', 'text', [
                'label'      => trans('plugins/marketplace::store.forms.country'),
                'label_attr' => ['class' => 'control-label'],
                'wrapper'    => [
                    'class' => $this->formHelper->getConfig('defaults.wrapper_class') . ' col-md-6',
                ],
                'attr'       => [
                    'placeholder'  => trans('plugins/marketplace::store.forms.country_placeholder'),
                    'data-counter' => 120,
                ],
            ])
            ->add('rowClose3', 'html', [
                'html' => '</div>',
            ])
            ->add('description', 'textarea', [
                'label'      => trans('core/base::forms.description'),
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'rows'         => 4,
                    'placeholder'  => trans('core/base::forms.description_placeholder'),
                    'data-counter' => 400,
                ],
            ])
            ->add('content', 'editor', [
                'label'      => trans('core/base::forms.content'),
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'rows'            => 4,
                    'placeholder'     => trans('core/base::forms.description_placeholder'),
                    'with-short-code' => false,
                ],
            ])
            ->add('status', 'customSelect', [
                'label'      => trans('core/base::tables.status'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'class' => 'form-control select-full',
                ],
                'choices'    => BaseStatusEnum::labels(),
                'help_block' => [
                    'text' => trans('plugins/marketplace::marketplace.helpers.store_status', [
                        'customer' => CustomerStatusEnum::LOCKED()->label(),
                        'status'   => BaseStatusEnum::PUBLISHED()->label(),
                    ]),
                ]
            ])
            ->add('customer_id', 'customSelect', [
                'label'      => trans('plugins/marketplace::store.forms.store_owner'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'class' => 'form-control select-full',
                ],
                'choices'    => [0 => trans('plugins/marketplace::store.forms.select_store_owner')] + $customers,
            ])
            ->add('logo', 'mediaImage', [
                'label'      => trans('plugins/marketplace::store.forms.logo'),
                'label_attr' => ['class' => 'control-label'],
            ])
            ->setBreakFieldPoint('status');
    }
}
