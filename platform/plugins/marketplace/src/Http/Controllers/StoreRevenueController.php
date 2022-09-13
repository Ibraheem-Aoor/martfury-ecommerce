<?php

namespace Botble\Marketplace\Http\Controllers;

use Assets;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Marketplace\Enums\RevenueTypeEnum;
use Botble\Marketplace\Http\Requests\StoreRevenueRequest;
use Botble\Marketplace\Repositories\Interfaces\RevenueInterface;
use Botble\Marketplace\Repositories\Interfaces\StoreInterface;
use Botble\Marketplace\Tables\StoreRevenueTable;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class StoreRevenueController extends BaseController
{
    /**
     * @var StoreInterface
     */
    protected $storeRepository;

    /**
     * @var RevenueInterface
     */
    protected $revenueRepository;

    /**
     * @param StoreInterface $storeRepository
     * @param RevenueInterface $revenueRepository
     */
    public function __construct(StoreInterface $storeRepository, RevenueInterface $revenueRepository)
    {
        $this->storeRepository = $storeRepository;
        $this->revenueRepository = $revenueRepository;
    }

    /**
     * @param StoreRevenueTable $table
     * @return Factory|View
     * @throws Throwable
     */
    public function index(StoreRevenueTable $table)
    {
        return $table->renderTable();
    }

    /**
     * @param int $id
     * @param StoreRevenueTable $table
     * @return string
     */
    public function view($id, StoreRevenueTable $table)
    {
        $store = $this->storeRepository->findOrFail($id);
        $customer = $store->customer;

        if (!$customer->id) {
            abort(404);
        }

        Assets::addScriptsDirectly(['vendor/core/plugins/marketplace/js/store-revenue.js']);
        $table->setAjaxUrl(route('marketplace.store.revenue.index', $customer->id));
        page_title()->setTitle(trans('plugins/marketplace::revenue.view_store', ['store' => $store->name]));

        return view('plugins/marketplace::stores.index', compact('table', 'store', 'customer'))->render();
    }

    /**
     * @param int $id
     * @param StoreRevenueRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store($id, StoreRevenueRequest $request, BaseHttpResponse $response)
    {
        $store = $this->storeRepository->findOrFail($id);

        $customer = $store->customer;

        if (!$customer->id) {
            abort(404);
        }

        $vendorInfo = $customer->vendorInfo;

        $revenue = $this->revenueRepository->getModel();
        $amount = $request->input('amount');
        $data = [
            'fee'             => 0,
            'currency'        => get_application_currency()->title,
            'current_balance' => $customer->balance,
            'customer_id'     => $customer->getKey(),
            'order_id'        => 0,
            'user_id'         => Auth::user()->getKey(),
            'type'            => $request->input('type'),
            'description'     => $request->input('description'),
            'amount'          => $amount,
            'sub_amount'      => $amount,
        ];

        if ($request->input('type') == RevenueTypeEnum::ADD_AMOUNT) {
            $vendorInfo->total_revenue += $amount;
            $vendorInfo->balance += $amount;
        } else {
            $vendorInfo->total_revenue -= $amount;
            $vendorInfo->balance -= $amount;
        }

        DB::beginTransaction();
        try {
            $revenue->fill($data);
            $revenue->save();
            $vendorInfo->save();

            DB::commit();
        } catch (Throwable $th) {
            DB::rollBack();

            throw $th;
        }

        event(new UpdatedContentEvent(STORE_MODULE_SCREEN_NAME, $request, $store));

        return $response
            ->setData(['balance' => format_price($customer->balance)])
            ->setPreviousUrl(route('marketplace.store.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }
}
