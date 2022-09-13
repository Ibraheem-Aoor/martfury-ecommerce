<?php

namespace Botble\Ecommerce\Tables;

use BaseHelper;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Exports\ProductExport;
use Botble\Ecommerce\Repositories\Interfaces\ProductCategoryInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Botble\Table\Abstracts\TableAbstract;
use Carbon\Carbon;
use Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\Facades\Auth;
use RvMedia;
use Yajra\DataTables\DataTables;

class ProductTable extends TableAbstract
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
     * ProductTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param ProductInterface $productRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, ProductInterface $productRepository)
    {
        parent::__construct($table, $urlGenerator);

        $this->repository = $productRepository;

        if (!Auth::user()->hasAnyPermission(['products.edit', 'products.destroy'])) {
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
            ->editColumn('name', function ($item) {
                if (!Auth::user()->hasPermission('products.edit')) {
                    return clean($item->name);
                }

                return Html::link(route('products.edit', $item->id), clean($item->name));
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
                return $item->price_in_table;
            })
            ->editColumn('quantity', function ($item) {
                return $item->with_storehouse_management ? $item->quantity : '&#8734;';
            })
            ->editColumn('sku', function ($item) {
                return clean($item->sku ?: '&mdash;');
            })
            ->editColumn('order', function ($item) {
                return view('plugins/ecommerce::products.partials.sort-order', compact('item'))->render();
            })
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('status', function ($item) {
                return clean($item->status->toHtml());
            })
            ->editColumn('stock_status', function ($item) {
                return clean($item->stock_status_html);
            })
            ->addColumn('operations', function ($item) {
                return $this->getOperations('products.edit', 'products.destroy', $item);
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
                'stock_status',
            ])
            ->where('is_variation', 0);

        return $this->applyScopes($query);
    }

    /**
     * {@inheritDoc}
     */
    public function htmlDrawCallbackFunction(): ?string
    {
        return parent::htmlDrawCallbackFunction() . '$(".editable").editable({mode: "inline"});';
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        return [
            'id'           => [
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'image'        => [
                'name'  => 'images',
                'title' => trans('plugins/ecommerce::products.image'),
                'width' => '100px',
                'class' => 'text-center',
            ],
            'name'         => [
                'title' => trans('core/base::tables.name'),
                'class' => 'text-start',
            ],
            'price'        => [
                'title' => trans('plugins/ecommerce::products.price'),
                'class' => 'text-start',
            ],
            'stock_status' => [
                'title' => trans('plugins/ecommerce::products.stock_status'),
                'class' => 'text-start',
            ],
            'quantity'     => [
                'title' => trans('plugins/ecommerce::products.quantity'),
                'class' => 'text-start',
            ],
            'sku'          => [
                'title' => trans('plugins/ecommerce::products.sku'),
                'class' => 'text-start',
            ],
            'order'        => [
                'title' => trans('core/base::tables.order'),
                'width' => '50px',
                'class' => 'text-center',
            ],
            'created_at'   => [
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
                'class' => 'text-center',
            ],
            'status'       => [
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
        $buttons = $this->addCreateButton(route('products.create'), 'products.create');

        if (Auth::user()->hasPermission('ecommerce.bulk-import.index')) {
            $buttons['import'] = [
                'link' => route('ecommerce.bulk-import.index'),
                'text' => '<i class="fas fa-file-import"></i> ' . trans('plugins/ecommerce::bulk-import.tables.import'),
            ];
        }

        if (Auth::user()->hasPermission('ecommerce.export.products.index')) {
            $buttons['export'] = [
                'link' => route('ecommerce.export.products.index'),
                'text' => '<i class="fas fa-file-export"></i> ' . trans('plugins/ecommerce::export.export'),
            ];
        }

        return $buttons;
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('products.deletes'), 'products.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function renderTable($data = [], $mergeData = [])
    {
        if ($this->query()->count() === 0 &&
            !$this->request()->wantsJson() &&
            $this->request()->input('filter_table_id') !== $this->getOption('id') && !$this->request()->ajax()
        ) {
            return view('plugins/ecommerce::products.intro');
        }

        return parent::renderTable($data, $mergeData);
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultButtons(): array
    {
        return [
            'reload',
        ];
    }

    /**
     * @param string|null|int $value
     * @return array
     */
    public function getCategories($value = null): array
    {
        $categorySelected = [];
        if ($value) {
            $category = app(ProductCategoryInterface::class)->findById($value);
            if ($category) {
                $categorySelected = [$category->id => $category->name];
            }
        }

        return [
            'url'      => route('product-categories.search'),
            'selected' => $categorySelected,
        ];
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        $data = $this->getBulkChanges();

        $data['category'] = array_merge($data['category'], [
            'type'  => 'select-ajax',
            'class' => 'select-search-ajax',
        ]);

        return $data;
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
            'category'   => [
                'title'    => trans('plugins/ecommerce::products.category'),
                'type'     => 'select-ajax',
                'validate' => 'required',
                'callback' => 'getCategories',
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
    public function applyFilterCondition($query, string $key, string $operator, ?string $value)
    {
        switch ($key) {
            case 'created_at':
                if (!$value) {
                    break;
                }

                $value = Carbon::createFromFormat(config('core.base.general.date_format.date'), $value)->toDateString();

                return $query->whereDate($key, $operator, $value);
            case 'category':
                if (!$value) {
                    break;
                }

                if (!BaseHelper::isJoined($query, 'ec_product_categories')) {
                    $query = $query
                        ->join('ec_product_category_product', 'ec_product_category_product.product_id', '=',
                            'ec_products.id')
                        ->join('ec_product_categories', 'ec_product_category_product.category_id', '=',
                            'ec_product_categories.id')
                        ->select($query->getModel()->getTable() . '.*');
                }

                return $query->where('ec_product_category_product.category_id', $value);
        }

        return parent::applyFilterCondition($query, $key, $operator, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function saveBulkChangeItem($item, string $inputKey, ?string $inputValue)
    {
        if ($inputKey === 'category') {
            $item->categories()->sync([$inputValue]);

            return $item;
        }

        return parent::saveBulkChangeItem($item, $inputKey, $inputValue);
    }
}
