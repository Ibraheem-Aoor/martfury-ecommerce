<?php

namespace Botble\Marketplace\Forms;

use Botble\Base\Forms\FormAbstract;
use Botble\Marketplace\Http\Requests\VendorEditWithdrawalRequest;
use Botble\Marketplace\Http\Requests\VendorWithdrawalRequest;
use Botble\Marketplace\Models\Withdrawal;
use MarketplaceHelper;

class VendorWithdrawalForm extends FormAbstract
{

    /**
     * {@inheritDoc}
     */
    public function buildForm()
    {
        $fee = MarketplaceHelper::getSetting('fee_withdrawal', 0);

        $exists = $this->getModel() && $this->getModel()->id;

        $actionButtons = view('plugins/marketplace::withdrawals.forms.actions')->render();
        if ($exists) {
            $fee = null;
            if (!$this->getModel()->vendor_can_edit) {
                $actionButtons = ' ';
            }
        }

        $model = auth('customer')->user();
        if ($exists) {
            $model = $this->getModel();
        }

        $disabled = ['disabled' => 'disabled'];

        $this
            ->setupModel(new Withdrawal)
            ->setValidatorClass($exists ? VendorEditWithdrawalRequest::class : VendorWithdrawalRequest::class)
            ->setFormOption('template', MarketplaceHelper::viewPath('dashboard.forms.base'))
            ->withCustomFields()
            ->add('amount', 'number', [
                'label'      => trans('plugins/marketplace::withdrawal.forms.amount'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => array_merge([
                    'placeholder'  => trans('plugins/marketplace::withdrawal.forms.amount_placeholder'),
                    'data-counter' => 120,
                    'max'          => $model->balance,
                ], $exists ? $disabled : []),
                'help_block' => [
                    'text' => $fee ? trans('plugins/marketplace::withdrawal.forms.fee_helper',
                        ['fee' => format_price($fee)]) : '',
                ],
            ]);

        if ($exists) {
            $this->add('fee', 'number', [
                'label'      => trans('plugins/marketplace::withdrawal.forms.fee'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => $disabled,
            ]);
        }

        $this
            ->add('payment_channel', 'text', [
                'label'      => trans('plugins/marketplace::withdrawal.forms.payment_channel'),
                'label_attr' => ['class' => 'control-label'],
                'attr'       => $disabled,
            ])->add('transaction_id', 'text', [
                'label'      => trans('plugins/marketplace::withdrawal.forms.transaction_id'),
                'label_attr' => ['class' => 'control-label'],
                'attr'       => $disabled,
            ]);

        $this
            ->add('description', 'textarea', [
                'label'      => trans('core/base::forms.description'),
                'label_attr' => ['class' => 'control-label'],
                'attr'       => array_merge([
                    'rows'         => 3,
                    'placeholder'  => trans('core/base::forms.description_placeholder'),
                    'data-counter' => 200,
                ], $exists && !$this->getModel()->vendor_can_edit ? $disabled : []),
            ])
            ->add('bankInfo', 'html', [
                'html' => view('plugins/marketplace::withdrawals.bank-info', [
                    'bankInfo' => $model->bank_info,
                    'link'     => $exists ? null : route('marketplace.vendor.settings', ['#tab_bank_info']),
                ])
                    ->render(),
            ]);

        if ($exists) {
            if ($model->images) {
                $this->addMetaBoxes([
                    'images' => [
                        'title'    => __('Withdrawal images'),
                        'content'  => view('plugins/marketplace::withdrawals.forms.images', compact('model'))->render(),
                        'priority' => 4,
                    ],
                ]);
            }

            if ($this->getModel()->vendor_can_edit) {
                $this->add('cancel', 'onOff', [
                    'label'      => __('Do you want to cancel?'),
                    'label_attr' => ['class' => 'control-label'],
                    'help_block' => [
                        'text' => __('After cancel amount and fee will be refunded back in your balance'),
                    ],
                ]);
            } else {
                $this->add('cancel', 'html', [
                    'label' => trans('core/base::tables.status'),
                    'html'  => $model->status->toHtml(),
                ]);
            }
        }

        $this
            ->setBreakFieldPoint('cancel')
            ->setActionButtons($actionButtons);
    }
}
