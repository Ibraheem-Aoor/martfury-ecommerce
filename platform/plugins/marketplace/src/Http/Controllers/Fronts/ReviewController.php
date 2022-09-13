<?php

namespace Botble\Marketplace\Http\Controllers\Fronts;

use Assets;
use Botble\Ecommerce\Repositories\Interfaces\ReviewInterface;
use Botble\Marketplace\Tables\ReviewTable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use MarketplaceHelper;

class ReviewController
{
    /**
     * @var ReviewInterface
     */
    protected $reviewRepository;

    /**
     * ReviewController constructor.
     * @param ReviewInterface $reviewRepository
     */
    public function __construct(ReviewInterface $reviewRepository)
    {
        $this->reviewRepository = $reviewRepository;
    }

    /**
     * @param ReviewTable $table
     * @return JsonResponse|View|Response
     */
    public function index(ReviewTable $table)
    {
        page_title()->setTitle(__('Reviews'));

        Assets::addStylesDirectly('vendor/core/plugins/ecommerce/css/review.css');

        return $table->render(MarketplaceHelper::viewPath('dashboard.table.base'));
    }
}
