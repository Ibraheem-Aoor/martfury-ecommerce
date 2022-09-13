<?php

namespace Botble\Marketplace\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Marketplace\Forms\StoreForm;
use Botble\Marketplace\Http\Requests\StoreRequest;
use Botble\Marketplace\Repositories\Interfaces\RevenueInterface;
use Botble\Marketplace\Repositories\Interfaces\StoreInterface;
use Botble\Marketplace\Tables\StoreTable;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class StoreController extends BaseController
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
     * @param StoreTable $table
     * @return Factory|View
     * @throws Throwable
     */
    public function index(StoreTable $table)
    {
        page_title()->setTitle(trans('plugins/marketplace::store.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/marketplace::store.create'));

        return $formBuilder->create(StoreForm::class)->renderForm();
    }

    /**
     * @param StoreRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(StoreRequest $request, BaseHttpResponse $response)
    {
        $store = $this->storeRepository->createOrUpdate($request->input());

        event(new CreatedContentEvent(STORE_MODULE_SCREEN_NAME, $request, $store));

        return $response
            ->setPreviousUrl(route('marketplace.store.index'))
            ->setNextUrl(route('marketplace.store.edit', $store->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function edit($id, FormBuilder $formBuilder, Request $request)
    {
        $store = $this->storeRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $store));

        page_title()->setTitle(trans('plugins/marketplace::store.edit') . ' "' . $store->name . '"');

        return $formBuilder->create(StoreForm::class, ['model' => $store])->renderForm();
    }

    /**
     * @param int $id
     * @param StoreRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, StoreRequest $request, BaseHttpResponse $response)
    {
        $store = $this->storeRepository->findOrFail($id);

        $store->fill($request->input());

        $this->storeRepository->createOrUpdate($store);

        $customer = $store->customer;
        if ($customer && $customer->id) {
            $vendorInfo = $customer->vendorInfo;
            $vendorInfo->bank_info = $request->input('bank_info');
            $vendorInfo->save();
        }

        event(new UpdatedContentEvent(STORE_MODULE_SCREEN_NAME, $request, $store));

        return $response
            ->setPreviousUrl(route('marketplace.store.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function destroy(Request $request, $id, BaseHttpResponse $response)
    {
        try {
            $store = $this->storeRepository->findOrFail($id);

            $this->storeRepository->delete($store);

            event(new DeletedContentEvent(STORE_MODULE_SCREEN_NAME, $request, $store));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Exception
     */
    public function deletes(Request $request, BaseHttpResponse $response)
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return $response
                ->setError()
                ->setMessage(trans('core/base::notices.no_select'));
        }

        foreach ($ids as $id) {
            $store = $this->storeRepository->findOrFail($id);
            $this->storeRepository->delete($store);
            event(new DeletedContentEvent(STORE_MODULE_SCREEN_NAME, $request, $store));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
