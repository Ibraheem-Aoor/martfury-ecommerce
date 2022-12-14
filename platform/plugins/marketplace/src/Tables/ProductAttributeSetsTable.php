<?php

namespace Botble\Marketplace\Tables;


use BaseHelper;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Repositories\Interfaces\ProductAttributeSetInterface;
use Botble\Table\Abstracts\TableAbstract;
use Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class ProductAttributeSetsTable extends TableAbstract
{

    /**
     * @var bool
     */
    protected $hasActions = true;

    /**
     * @var bool
     */
    protected $hasFilter = false;

    /**
     * ProductAttributeSetsTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param ProductAttributeSetInterface $productAttributeSetRepository
     */
    public function __construct(
        DataTables $table,
        UrlGenerator $urlGenerator,
        ProductAttributeSetInterface $productAttributeSetRepository
    ) {
        parent::__construct($table, $urlGenerator);

        $this->repository = $productAttributeSetRepository;

    }

    /**
     * {@inheritDoc}
     */
    public function ajax()
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('title', function ($item) {

                return Html::link(route('marketplace.vendor.product-attribute-sets.edit', $item->id), clean($item->title));
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
                return $this->getOperations('marketplace.vendor.product-attribute-sets.edit', null, $item);
            });

        return $this->toJson($data);
    }

    /**
     * {@inheritDoc}
     */
    public function query()
    {
        $query = $this->repository->getModel()->where('created_by_id' , auth('customer')->id())->select([
            'id',
            'created_at',
            'title',
            'slug',
            'order',
            'status',
        ]);

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
                'class' => 'text-center',
            ],
            'title'      => [
                'title' => trans('core/base::tables.title'),
                'class' => 'text-start',
            ],
            'slug'       => [
                'title' => trans('core/base::tables.slug'),
                'class' => 'text-start',
            ],
            'order'      => [
                'title' => trans('core/base::tables.order'),
                'class' => 'text-start',
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
                'class' => 'text-start',
            ],
            'status'     => [
                'title' => trans('core/base::tables.status'),
                'width' => '100px',
                'class' => 'text-start',
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function buttons()
    {
        return $this->addCreateButton(route('marketplace.vendor.product-attribute-sets.create'), null);
    }

    /**
     * {@inheritDoc}
     */
    // public function bulkActions(): array
    // {
    //     return $this->addDeleteAction(route('product-attribute-sets.deletes'), 'product-attribute-sets.destroy',
    //         parent::bulkActions());
    // }

    /**
     * {@inheritDoc}
     */
    // public function getBulkChanges(): array
    // {
    //     return [
    //         'title'      => [
    //             'title'    => trans('core/base::tables.name'),
    //             'type'     => 'text',
    //             'validate' => 'required|max:120',
    //         ],
    //         'status'     => [
    //             'title'    => trans('core/base::tables.status'),
    //             'type'     => 'select',
    //             'choices'  => BaseStatusEnum::labels(),
    //             'validate' => 'required|in:' . implode(',', BaseStatusEnum::values()),
    //         ],
    //         'created_at' => [
    //             'title' => trans('core/base::tables.created_at'),
    //             'type'  => 'date',
    //         ],
    //     ];
    // }

    /**
     * {@inheritDoc}
     */
    // public function renderTable($data = [], $mergeData = [])
    // {
    //     if ($this->query()->count() === 0 &&
    //         !$this->request()->wantsJson() &&
    //         $this->request()->input('filter_table_id') !== $this->getOption('id') && !$this->request()->ajax()
    //     ) {
    //         return view('plugins/ecommerce::product-attributes.intro');
    //     }

    //     return parent::renderTable($data, $mergeData);
    // }
}
