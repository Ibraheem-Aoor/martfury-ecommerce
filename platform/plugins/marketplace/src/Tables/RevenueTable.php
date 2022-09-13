<?php

namespace Botble\Marketplace\Tables;

use BaseHelper;
use Botble\Marketplace\Repositories\Interfaces\RevenueInterface;
use Botble\Table\Abstracts\TableAbstract;
use Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;

class RevenueTable extends TableAbstract
{

    /**
     * @var bool
     */
    protected $hasActions = false;

    /**
     * @var bool
     */
    protected $hasFilter = true;

    /**
     * @var bool
     */
    protected $hasOperations = false;

    /**
     * @var bool
     */
    protected $hasCheckbox = false;

    /**
     * RevenueTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param RevenueInterface $revenueRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, RevenueInterface $revenueRepository)
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
            ->editColumn('current_balance', function ($item) {
                return format_price($item->current_balance);
            })
            ->editColumn('amount', function ($item) {
                return format_price($item->amount) . $item->description_tooltip;
            })
            ->editColumn('sub_amount', function ($item) {
                return format_price($item->sub_amount) . $item->description_tooltip;
            })
            ->editColumn('fee', function ($item) {
                return format_price($item->fee);
            })
            ->editColumn('order_id', function ($item) {
                if (!$item->order->id) {
                    return '&mdash;';
                }

                return Html::link(route('marketplace.vendor.orders.edit', $item->order->id),
                    get_order_code($item->order->id), ['target' => '_blank']);
            })
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
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
                'sub_amount',
                'fee',
                'amount',
                'current_balance',
                'currency',
                'order_id',
                'created_at',
                'type',
                'description',
            ])
            ->with(['order'])
            ->where('customer_id', auth('customer')->id());

        return $this->applyScopes($query);
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        return [
            'id'              => [
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
                'class' => 'text-start',
            ],
            'order_id'        => [
                'title' => trans('plugins/ecommerce::order.order'),
                'class' => 'text-center',
            ],
            'sub_amount'      => [
                'title' => trans('plugins/ecommerce::order.sub_amount'),
                'class' => 'text-center',
            ],
            'fee'             => [
                'title' => trans('plugins/ecommerce::shipping.fee'),
                'class' => 'text-center',
            ],
            'amount'          => [
                'title' => trans('plugins/ecommerce::order.amount'),
                'class' => 'text-center',
            ],
            'currency'        => [
                'title' => trans('plugins/ecommerce::payment.currency'),
                'class' => 'text-center',
            ],
            'current_balance' => [
                'title' => trans('plugins/marketplace::marketplace.current_balance'),
                'class' => 'text-center',
            ],
            'created_at'      => [
                'title' => trans('core/base::tables.created_at'),
                'class' => 'text-center',
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
}
