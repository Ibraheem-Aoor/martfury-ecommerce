<?php

namespace Botble\Marketplace\Forms;

use Botble\Base\Forms\FormAbstract;
use Botble\Marketplace\Http\Requests\WithdrawalRequest;
use Botble\Marketplace\Models\Withdrawal;

class WithdrawalForm extends FormAbstract
{

    /**
     * {@inheritDoc}
     */
    public function buildForm()
    {
        $symbol = ' (' . get_application_currency()->symbol . ')';

        $this
            ->setupModel(new Withdrawal)
            ->setValidatorClass(WithdrawalRequest::class)
            ->withCustomFields()
            ->add('amount', 'text', [
                'label'      => trans('plugins/marketplace::withdrawal.forms.amount') . $symbol,
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'disabled' => 'disabled',
                ],
            ])
            ->add('fee', 'text', [
                'label'      => trans('plugins/marketplace::withdrawal.forms.fee') . $symbol,
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'disabled' => 'disabled',
                ],
            ])
            ->add('payment_channel', 'text', [
                'label'      => trans('plugins/marketplace::withdrawal.forms.payment_channel'),
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'placeholder'  => trans('plugins/marketplace::withdrawal.forms.payment_channel_placeholder'),
                    'data-counter' => 60,
                ],
            ])
            ->add('transaction_id', 'text', [
                'label'      => trans('plugins/marketplace::withdrawal.forms.transaction_id'),
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'placeholder'  => trans('plugins/marketplace::withdrawal.forms.transaction_id_placeholder'),
                    'data-counter' => 60,
                ],
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
            ->add('bankInfo', 'html', [
                'html' => view('plugins/marketplace::withdrawals.bank-info', [
                    'bankInfo' => $this->getModel()->bank_info,
                    'title'    => __('Bank information'),
                ])->render(),
            ])
            ->add('images[]', 'mediaImages', [
                'label'      => trans('plugins/ecommerce::products.form.image'),
                'label_attr' => ['class' => 'control-label'],
                'values'     => $this->getModel() ? $this->getModel()->images : [],
            ]);
            if ($this->getModel()->canEditStatus()) {
                $this->add('status', 'customSelect', [
                    'label'      => trans('core/base::tables.status'),
                    'label_attr' => ['class' => 'control-label required'],
                    'attr'       => [
                        'class'    => 'form-control select-full',
                    ],
                    'choices'    => $this->getModel()->getNextStatuses(),
                    'help_block' => [
                        'text' => $this->getModel()->getStatusHelper(),
                    ],
                ]);
            } else {
                $this->add('status', 'html', [
                    'html' => $this->getModel()->status->toHtml(),
                ]);
            }

            $this->setBreakFieldPoint('status');
    }
}
