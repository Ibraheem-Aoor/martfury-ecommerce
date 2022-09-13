<?php

namespace Botble\Marketplace\Tables;

use BaseHelper;
use Botble\Marketplace\Repositories\Interfaces\WithdrawalInterface;
use Botble\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;

class VendorWithdrawalTable extends TableAbstract
{
    /**
     * @var bool
     */
    protected $hasActions = false;

    /**
     * @var bool
     */
    protected $hasFilter = false;

    /**
     * @var bool
     */
    protected $hasCheckbox = false;

    /**
     * WithdrawalTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param WithdrawalInterface $revenueRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, WithdrawalInterface $revenueRepository)
    {
        $this->repository = $revenueRepository;
        parent::__construct($table, $urlGenerator);
    }

    /**
     * {@inheritDoc}
     */
    public function ajax()
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('fee', function ($item) {
                return format_price($item->fee);
            })
            ->editColumn('amount', function ($item) {
                return format_price($item->amount);
            })
            ->editColumn('status', function ($item) {
                return clean($item->status->toHtml());
            })
            ->addColumn('operations', function ($item) {
                return view('plugins/marketplace::withdrawals.tables.actions', compact('item'))->render();
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
                'fee',
                'amount',
                'status',
                'currency',
                'created_at',
            ])
            ->where('customer_id', auth('customer')->id());

        return $this->applyScopes($query);
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        return [
            'id'         => [
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'amount'     => [
                'title' => trans('plugins/ecommerce::order.amount'),
            ],
            'fee'        => [
                'title' => trans('plugins/ecommerce::shipping.fee'),
            ],
            'status'     => [
                'title' => trans('core/base::tables.status'),
                'width' => '100px',
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultButtons(): array
    {
        return [
            'export',
            'reload',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function buttons()
    {
        return $this->addCreateButton(route('marketplace.vendor.withdrawals.create'));
    }
}
