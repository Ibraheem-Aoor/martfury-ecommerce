<?php

namespace Botble\Marketplace\Tables;

use Auth;
use BaseHelper;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Marketplace\Repositories\Interfaces\StoreInterface;
use Botble\Table\Abstracts\TableAbstract;
use Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use RvMedia;
use Yajra\DataTables\DataTables;

class StoreTable extends TableAbstract
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
     * @var bool
     */
    protected $canEditWalletBalance = false;

    /**
     * StoreTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param StoreInterface $storeRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, StoreInterface $storeRepository)
    {
        $this->repository = $storeRepository;
        parent::__construct($table, $urlGenerator);

        if (!Auth::user()->hasAnyPermission(['marketplace.store.edit', 'marketplace.store.destroy'])) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }

        if (Auth::user()->hasAnyPermission(['marketplace.store.view'])) {
            $this->canEditWalletBalance = true;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function ajax()
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('name', function ($item) {
                if (!Auth::user()->hasPermission('marketplace.store.edit')) {
                    return clean($item->name);
                }

                return Html::link(route('marketplace.store.edit', $item->id), clean($item->name));
            })
            ->editColumn('logo', function ($item) {
                return Html::image(RvMedia::getImageUrl($item->logo, 'thumb', false, RvMedia::getDefaultImage()),
                    clean($item->name), ['width' => 50]);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('earnings', function ($item) {
                return $item->customer->id ? format_price($item->customer->balance ?: 0) : '--';
            })
            ->editColumn('products_count', function ($item) {
                return $item->products_count;
            })
            ->editColumn('status', function ($item) {
                return clean($item->status->toHtml());
            })
            ->addColumn('operations', function ($item) {
                $viewBtn = '';
                if ($this->canEditWalletBalance && $item->customer->id) {
                    $viewBtn = Html::link(route('marketplace.store.view', $item->id),
                        '<i class="fa fa-eye"></i>', [
                            'class'                  => 'btn btn-info',
                            'data-bs-toggle'         => 'tooltip',
                            'data-bs-original-title' => trans('plugins/marketplace::store.view'),
                        ], null, false);
                }

                return $this->getOperations('marketplace.store.edit', 'marketplace.store.destroy', $item, $viewBtn);
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
                'logo',
                'name',
                'created_at',
                'status',
                'customer_id',
            ])
            ->with(['customer', 'customer.vendorInfo'])
            ->withCount(['products']);

        return $this->applyScopes($query);
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        return [
            'id'             => [
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'logo'           => [
                'title' => trans('plugins/marketplace::store.forms.logo'),
                'width' => '70px',
            ],
            'name'           => [
                'title' => trans('core/base::tables.name'),
                'class' => 'text-start',
            ],
            'earnings'       => [
                'title'      => trans('plugins/marketplace::marketplace.tables.earnings'),
                'class'      => 'text-start',
                'searchable' => false,
                'orderable'  => false,
                'width'      => '100px',
            ],
            'products_count' => [
                'title'      => trans('plugins/marketplace::marketplace.tables.products_count'),
                'searchable' => false,
                'orderable'  => false,
            ],
            'created_at'     => [
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
            ],
            'status'         => [
                'title' => trans('core/base::tables.status'),
                'width' => '100px',
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function buttons()
    {
        return $this->addCreateButton(route('marketplace.store.create'), 'marketplace.store.create');
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('marketplace.store.deletes'), 'marketplace.store.destroy',
            parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges(): array
    {
        return [
            'name'       => [
                'title'    => trans('core/base::tables.name'),
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
            'status'     => [
                'title'    => trans('core/base::tables.status'),
                'type'     => 'select',
                'choices'  => BaseStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', BaseStatusEnum::values()),
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type'  => 'date',
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getOperationsHeading()
    {
        return [
            'operations' => [
                'title'      => trans('core/base::tables.operations'),
                'width'      => '180px',
                'class'      => 'text-end',
                'orderable'  => false,
                'searchable' => false,
                'exportable' => false,
                'printable'  => false,
            ],
        ];
    }
}
