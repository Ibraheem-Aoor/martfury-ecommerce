<?php

namespace Botble\Marketplace\Tables;

use Auth;
use BaseHelper;
use Botble\Ecommerce\Repositories\Interfaces\CustomerInterface;
use Botble\Table\Abstracts\TableAbstract;
use Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;

class UnverifiedVendorTable extends TableAbstract
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
     * UnverifiedVendorTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param CustomerInterface $customerRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, CustomerInterface $customerRepository)
    {
        $this->repository = $customerRepository;
        parent::__construct($table, $urlGenerator);

        if (!Auth::user()->hasAnyPermission(['marketplace.unverified-vendors.edit'])) {
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
                if (!Auth::user()->hasPermission('marketplace.unverified-vendors.edit')) {
                    return clean($item->name);
                }

                return Html::link(route('marketplace.unverified-vendors.view', $item->id), clean($item->name));
            })
            ->editColumn('avatar', function ($item) {
                if ($this->request()->input('action') == 'excel' ||
                    $this->request()->input('action') == 'csv') {
                    return $item->avatar_url;
                }

                return Html::image($item->avatar_url, clean($item->name), ['width' => 50]);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('store_name', function ($item) {
                return clean($item->store->name);
            })
            ->editColumn('store_phone', function ($item) {
                return clean($item->store->phone);
            })
            ->addColumn('operations', function ($item) {
                return Html::link(route('marketplace.unverified-vendors.view', $item->id),
                    '<i class="fa fa-eye"></i>',
                    ['class' => 'btn btn-icon btn-sm btn-primary'], null, false);
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
                'created_at',
                'is_vendor',
                'avatar',
            ])
            ->where([
                'is_vendor'          => true,
                'vendor_verified_at' => null,
            ])
            ->with(['store']);

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
            'avatar'      => [
                'title' => trans('plugins/ecommerce::customer.avatar'),
                'class' => 'text-center',
            ],
            'name'        => [
                'title' => trans('core/base::tables.name'),
                'class' => 'text-start',
            ],
            'store_name'  => [
                'title'      => trans('plugins/marketplace::unverified-vendor.forms.store_name'),
                'class'      => 'text-start',
                'searchable' => false,
                'orderable'  => false,
            ],
            'store_phone' => [
                'title'      => trans('plugins/marketplace::unverified-vendor.forms.store_phone'),
                'class'      => 'text-start',
                'searchable' => false,
                'orderable'  => false,
            ],
            'created_at'  => [
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges(): array
    {
        return [];
    }
}
