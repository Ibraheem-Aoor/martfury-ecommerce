<?php

namespace Botble\Marketplace\Tables;

use BaseHelper;
use Botble\Marketplace\Repositories\Interfaces\RevenueInterface;
use Botble\Table\Abstracts\TableAbstract;
use Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;

class StoreRevenueTable extends TableAbstract
{
    /**
     * @var string
     */
    protected $type = self::TABLE_TYPE_SIMPLE;

    /**
     * @var int
     */
    protected $defaultSortColumn = 0;

    /**
     * @var string
     */
    protected $view = 'core/table::simple-table';

    /**
     * @var RevenueInterface
     */
    protected $repository;

    /**
     * StoreRevenueTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param RevenueInterface $revenueRepository
     */
    public function __construct(
        DataTables $table,
        UrlGenerator $urlGenerator,
        RevenueInterface $revenueRepository
    ) {
        parent::__construct($table, $urlGenerator);

        $this->repository = $revenueRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function ajax()
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('amount', function ($item) {
                return Html::tag('span', format_price($item->amount), ['class' => 'text-success']);
            })
            ->editColumn('sub_amount', function ($item) {
                return format_price($item->sub_amount);
            })
            ->editColumn('fee', function ($item) {
                return Html::tag('span', format_price($item->fee), ['class' => 'text-danger']);
            })
            ->editColumn('order_id', function ($item) {
                if (!$item->order->id) {
                    return $item->description;
                }

                return Html::link(route('orders.edit', $item->order->id), get_order_code($item->order->id), ['target' => '_blank']);
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
                'order_id',
                'created_at',
                'type',
                'description',
            ])
            ->with(['order'])
            ->where('customer_id', request()->route()->parameter('id'));

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
                'title' => trans('plugins/ecommerce::order.description'),
                'class' => 'text-start',
            ],
            'fee'             => [
                'title' => trans('plugins/ecommerce::shipping.fee'),
                'class' => 'text-start',
            ],
            'sub_amount'      => [
                'title' => trans('plugins/ecommerce::order.sub_amount'),
                'class' => 'text-start',
            ],
            'amount'          => [
                'title' => trans('plugins/ecommerce::order.amount'),
                'class' => 'text-start',
            ],
            'created_at'      => [
                'title' => trans('core/base::tables.created_at'),
                'class' => 'text-start',
                'width' => '100px'
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function htmlDrawCallbackFunction(): ?string
    {
        return parent::htmlDrawCallbackFunction() . '$("[data-bs-toggle=tooltip]").tooltip({placement: "top", boundary: "window"});';
    }
}
