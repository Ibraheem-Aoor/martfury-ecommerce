<?php

namespace Botble\Marketplace\Http\Controllers\Fronts;

use Assets;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Marketplace\Http\Requests\DiscountRequest;
use Botble\Ecommerce\Models\Discount;
use Botble\Ecommerce\Repositories\Interfaces\DiscountInterface;
use Botble\Marketplace\Models\Store;
use Botble\Marketplace\Tables\DiscountTable;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use MarketplaceHelper;
use Response;
use Throwable;

class DiscountController extends BaseController
{

    /**
     * @var DiscountInterface
     */
    protected $discountRepository;

    /**
     * @param DiscountInterface $discountRepository
     */
    public function __construct(DiscountInterface $discountRepository)
    {
        $this->discountRepository = $discountRepository;
    }

    /**
     * @param Request $request
     * @param DiscountTable $table
     * @return JsonResponse|View|Response
     * @throws Throwable
     */
    public function index(DiscountTable $table)
    {
        page_title()->setTitle(__('Coupons'));

        return $table->render(MarketplaceHelper::viewPath('dashboard.table.base'));
    }

    /**
     * @return string
     */
    public function create()
    {
        page_title()->setTitle(__('Create coupon'));

        Assets::addStylesDirectly(['vendor/core/plugins/ecommerce/css/ecommerce.css'])
            ->addScriptsDirectly([
                'vendor/core/plugins/marketplace/js/discount.js',
            ])
            ->addScripts(['timepicker', 'input-mask', 'blockui'])
            ->addStyles(['timepicker']);

        return MarketplaceHelper::view('dashboard.discounts.create');
    }

    /**
     * @return Store
     */
    private function getStore()
    {
        return auth('customer')->user()->store;
    }

    /**
     * @param DiscountRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Exception
     */
    public function store(DiscountRequest $request, BaseHttpResponse $response)
    {
        $request->merge([
            'can_use_with_promotion' => 0,
        ]);

        if ($request->input('is_unlimited')) {
            $request->merge(['quantity' => null]);
        }

        $request->merge([
            'start_date' => Carbon::parse($request->input('start_date') . ' ' . $request->input('start_time'))
                ->toDateTimeString(),
        ]);

        if ($request->has('end_date') && !$request->has('unlimited_time')) {
            $request->merge([
                'end_date' => Carbon::parse($request->input('end_date') . ' ' . $request->input('end_time'))
                    ->toDateTimeString(),
            ]);
        } else {
            $request->merge([
                'end_date' => null,
            ]);
        }

        /**
         * @var Discount $discount
         */
        $discount = $this->discountRepository->createOrUpdate($request->input());

        $discount->store_id = $this->getStore()->id;
        $discount->save();

        event(new CreatedContentEvent(DISCOUNT_MODULE_SCREEN_NAME, $request, $discount));

        return $response
            ->setNextUrl(route('marketplace.vendor.discounts.index'))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    /**
     * @param Request $request
     * @param int $id
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function destroy(Request $request, $id, BaseHttpResponse $response)
    {
        try {
            $discount = $this->discountRepository->findOrFail($id);
            if ($discount->store_id != $this->getStore()->id) {
                return $response
                    ->setError();
            }

            $this->discountRepository->delete($discount);

            event(new DeletedContentEvent(DISCOUNT_MODULE_SCREEN_NAME, $request, $discount));

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
            $discount = $this->discountRepository->findOrFail($id);

            if ($discount->store_id != $this->getStore()->id) {
                continue;
            }

            $this->discountRepository->delete($discount);
            event(new DeletedContentEvent(DISCOUNT_MODULE_SCREEN_NAME, $request, $discount));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }

    /**
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function postGenerateCoupon(BaseHttpResponse $response)
    {
        do {
            $code = strtoupper(Str::random(12));
        } while ($this->discountRepository->count(['code' => $code]) > 0);

        return $response->setData($code);
    }
}
