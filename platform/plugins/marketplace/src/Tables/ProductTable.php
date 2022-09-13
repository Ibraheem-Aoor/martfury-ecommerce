<?php

namespace Botble\Marketplace\Tables;

use BaseHelper;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Exports\ProductExport;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Botble\Table\Abstracts\TableAbstract;
use Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use MarketplaceHelper;
use RvMedia;
use Yajra\DataTables\DataTables;

class ProductTable extends TableAbstract
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
     * @var string
     */
    protected $exportClass = ProductExport::class;

    /**
     * ProductTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param ProductInterface $productRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, ProductInterface $productRepository)
    {
        $this->repository = $productRepository;
        parent::__construct($table, $urlGenerator);
    }

    /**
     * {@inheritDoc}
     */
    public function ajax()
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('name', function ($item) {
                return Html::link(route('marketplace.vendor.products.edit', $item->id), clean($item->name));
            })
            ->editColumn('image', function ($item) {
                if ($this->request()->input('action') == 'csv') {
                    return RvMedia::getImageUrl($item->image, null, false, RvMedia::getDefaultImage());
                }

                if ($this->request()->input('action') == 'excel') {
                    return RvMedia::getImageUrl($item->image, 'thumb', false, RvMedia::getDefaultImage());
                }

                return $this->displayThumbnail($item->image);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('price', function ($item) {
                return $item->price_in_table;;
            })
            ->editColumn('quantity', function ($item) {
                return $item->with_storehouse_management ? $item->quantity : '&#8734;';
            })
            ->editColumn('sku', function ($item) {
                return clean($item->sku ?: '&mdash;');
            })
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('status', function ($item) {
                return clean($item->status->toHtml());
            })
            ->addColumn('operations', function ($item) {
                return view(MarketplaceHelper::viewPath('dashboard.table.actions'), [
                    'edit'   => 'marketplace.vendor.products.edit',
                    'delete' => 'marketplace.vendor.products.destroy',
                    'item'   => $item,
                ])->render();
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
                'name',
                'order',
                'created_at',
                'status',
                'sku',
                'images',
                'price',
                'sale_price',
                'sale_type',
                'start_date',
                'end_date',
                'quantity',
                'with_storehouse_management',
            ])
            ->where('is_variation', 0)
            ->where('store_id', auth('customer')->user()->store->id);

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
            'image'      => [
                'name'  => 'images',
                'title' => trans('plugins/ecommerce::products.image'),
                'width' => '100px',
                'class' => 'text-center',
            ],
            'name'       => [
                'title' => trans('core/base::tables.name'),
                'class' => 'text-start',
            ],
            'price'      => [
                'title' => trans('plugins/ecommerce::products.price'),
                'class' => 'text-start',
            ],
            'quantity'   => [
                'title' => trans('plugins/ecommerce::products.quantity'),
                'class' => 'text-start',
            ],
            'sku'        => [
                'title' => trans('plugins/ecommerce::products.sku'),
                'class' => 'text-start',
            ],
            'order'      => [
                'title' => trans('core/base::tables.order'),
                'width' => '50px',
                'class' => 'text-center',
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
                'class' => 'text-center',
            ],
            'status'     => [
                'title' => trans('core/base::tables.status'),
                'width' => '100px',
                'class' => 'text-center',
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function buttons()
    {
        return $this->addCreateButton(route('marketplace.vendor.products.create'));
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('marketplace.vendor.products.deletes'), null, parent::bulkActions());
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
            'order'      => [
                'title'    => trans('core/base::tables.order'),
                'type'     => 'number',
                'validate' => 'required|min:0',
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
    public function getDefaultButtons(): array
    {
        return [
            'export',
            'reload',
        ];
    }
}
