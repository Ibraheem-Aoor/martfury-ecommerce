<?php

namespace Botble\Marketplace\Http\Controllers\Fronts;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Marketplace\Enums\RevenueTypeEnum;
use EcommerceHelper;
use Botble\Marketplace\Repositories\Interfaces\RevenueInterface;
use Botble\Marketplace\Tables\RevenueTable;
use Carbon\CarbonPeriod;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use MarketplaceHelper;

class RevenueController
{
    /**
     * @var RevenueInterface
     */
    protected $revenueRepository;

    /**
     * RevenueController constructor.
     * @param RevenueInterface $revenueRepository
     */
    public function __construct(RevenueInterface $revenueRepository)
    {
        $this->revenueRepository = $revenueRepository;
    }

    /**
     * @param RevenueTable $table
     * @return JsonResponse|View|Response
     */
    public function index(RevenueTable $table)
    {
        page_title()->setTitle(__('Revenues'));

        return $table->render(MarketplaceHelper::viewPath('dashboard.table.base'));
    }

    /**
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse|JsonResponse
     */
    public function getMonthChart(Request $request, BaseHttpResponse $response)
    {
        [$startDate, $endDate] = EcommerceHelper::getDateRangeInReport($request);

        $customerId = auth('customer')->id();

        $revenues = $this->revenueRepository
            ->getModel()
            ->selectRaw(
                'SUM(CASE WHEN type IS NULL OR type = ? THEN amount WHEN type = ? THEN amount * -1 ELSE 0 END) as amount,
                DATE(created_at) as date,
                currency', [RevenueTypeEnum::ADD_AMOUNT, RevenueTypeEnum::SUBTRACT_AMOUNT])
            ->where('customer_id', $customerId)
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->groupBy('date', 'currency')
            ->with(['currencyRelation'])
            ->get();

        $series = [];
        $dates = [];
        $revenuesGrouped = $revenues->groupBy('currency');
        $earningSales = collect([]);
        $period = CarbonPeriod::create($startDate, $endDate);

        $colors = ['#fcb800', '#80bc00'];

        foreach ($revenuesGrouped as $key => $revenues) {
            $data = [
                'name' => $key,
                'data' => collect([]),
            ];

            foreach ($period as $date) {
                $value = $revenues
                    ->where('date', $date->format('Y-m-d'))
                    ->sum('amount');
                $data['data'][] = $value;
            }

            $currency = null;
            if ($first = $revenues->first()) {
                $currency = $first->currencyRelation;
            }

            $amount = $currency && $currency->id ? format_price($data['data']->sum(),
                $currency) : human_price_text($data['data']->sum(), null, $key);
            $earningSales[] = [
                'text'  => __('Items Earning Sales: :amount', compact('amount')),
                'color' => Arr::get($colors, $earningSales->count(), Arr::first($colors)),
            ];
            $series[] = $data;
        }

        foreach ($period as $date) {
            $dates[] = $date->format('Y-m-d');
        }

        $colors = $earningSales->pluck('color');

        return $response->setData(compact('dates', 'series', 'earningSales', 'colors'));
    }
}
