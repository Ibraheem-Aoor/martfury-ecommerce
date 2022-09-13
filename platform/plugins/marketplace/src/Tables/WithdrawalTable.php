<?php

namespace Botble\Marketplace\Tables;

use BaseHelper;
use Botble\Marketplace\Enums\WithdrawalStatusEnum;
use Botble\Marketplace\Repositories\Interfaces\WithdrawalInterface;
use Botble\Table\Abstracts\TableAbstract;
use Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

class WithdrawalTable extends TableAbstract
{

    /**
     * @var bool
     */
    protected $hasActions = true;

    /**
     * @var bool
     */
    protected $hasFilter = true;

    /**
     * WithdrawalTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param WithdrawalInterface $withdrawalRepository
     */
    public function __construct(
        DataTables $table,
        UrlGenerator $urlGenerator,
        WithdrawalInterface $withdrawalRepository
    ) {
        parent::__construct($table, $urlGenerator);

        $this->repository = $withdrawalRepository;

        if (!Auth::user()->hasAnyPermission(['marketplace.withdrawal.edit', 'marketplace.withdrawal.destroy'])) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function ajax()
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('customer_id', function ($item) {
                if (!Auth::user()->hasPermission('customers.edit')) {
                    return clean($item->customer->name);
                }

                if (!$item->customer->id) {
                    return '&mdash;';
                }

                return Html::link(route('customers.edit', $item->customer->id), clean($item->customer->name));
            })
            ->editColumn('fee', function ($item) {
                return format_price($item->fee);
            })
            ->editColumn('amount', function ($item) {
                return format_price($item->amount);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('status', function ($item) {
                return clean($item->status->toHtml());
            })
            ->addColumn('operations', function ($item) {
                return $this->getOperations('marketplace.withdrawal.edit', null, $item);
            });

        return $this->toJson($data);
    }

    /**
     * {@inheritDoc}
     */
    public function query()
    {
        $query = $this->repository->getModel()
            ->select([
                'id',
                'customer_id',
                'amount',
                'fee',
                'currency',
                'created_at',
                'status',
            ])
            ->with(['customer']);

        return $this->applyScopes($query);
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        return [
            'id'          => [
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'customer_id' => [
                'title' => trans('plugins/marketplace::withdrawal.vendor'),
                'class' => 'text-start',
            ],
            'amount'      => [
                'title' => trans('plugins/marketplace::withdrawal.amount'),
                'class' => 'text-start',
            ],
            'fee'         => [
                'title' => trans('plugins/ecommerce::shipping.fee'),
                'class' => 'text-start',
            ],
            'created_at'  => [
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
            ],
            'status'      => [
                'title' => trans('core/base::tables.status'),
                'width' => '100px',
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges(): array
    {
        return [
            'status' => [
                'title'    => trans('core/base::tables.status'),
                'type'     => 'select',
                'choices'  => WithdrawalStatusEnum::labels(),
                'validate' => 'required|' . Rule::in(WithdrawalStatusEnum::values()),
            ],
        ];
    }
}
