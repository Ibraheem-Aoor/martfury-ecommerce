<?php

namespace Botble\Marketplace\Http\Controllers\Fronts;

use Assets;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Marketplace\Http\Requests\SettingRequest;
use Botble\Marketplace\Models\Store;
use Botble\Marketplace\Repositories\Interfaces\StoreInterface;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use MarketplaceHelper;
use RvMedia;
use SlugHelper;

class SettingController
{
    /**
     * DashboardController constructor.
     * @param Repository $config
     */
    public function __construct(Repository $config)
    {
        Assets::setConfig($config->get('plugins.marketplace.assets', []));
    }

    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        page_title()->setTitle(__('Settings'));

        $store = auth('customer')->user()->store;

        return MarketplaceHelper::view('dashboard.settings', compact('store'));
    }

    /**
     * @param SettingRequest $request
     * @param StoreInterface $storeRepository
     * @param BaseHttpResponse $response
     */
    public function saveSettings(SettingRequest $request, StoreInterface $storeRepository, BaseHttpResponse $response)
    {
        $store = auth('customer')->user()->store;

        $existing = SlugHelper::getSlug($request->input('slug'), SlugHelper::getPrefix(Store::class), Store::class);

        if ($existing && $existing->reference_id != $store->id) {
            return $response->setError()->setMessage(__('Shop URL is existing. Please choose another one!'));
        }

        if ($request->hasFile('logo_input')) {
            $result = RvMedia::handleUpload($request->file('logo_input'), 0, 'stores');
            if ($result['error'] == false) {
                $file = $result['data'];
                $request->merge(['logo' => $file->url]);
            }
        }

        $store->fill($request->input());

        $storeRepository->createOrUpdate($store);

        $customer = $store->customer;
        if ($customer && $customer->id) {
            $vendorInfo = $customer->vendorInfo;
            $vendorInfo->bank_info = $request->input('bank_info');
            $vendorInfo->save();
        }

        event(new UpdatedContentEvent(STORE_MODULE_SCREEN_NAME, $request, $store));

        return $response
            ->setNextUrl(route('marketplace.vendor.settings'))
            ->setMessage(__('Update successfully!'));
    }
}
