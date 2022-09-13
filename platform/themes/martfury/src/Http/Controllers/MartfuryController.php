<?php

namespace Theme\Martfury\Http\Controllers;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Blog\Repositories\Interfaces\PostInterface;
use Botble\Ecommerce\Repositories\Interfaces\FlashSaleInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductCategoryInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductVariationInterface;
use Botble\Ecommerce\Services\Products\GetProductService;
use Botble\Theme\Http\Controllers\PublicController;
use Cart;
use DB;
use EcommerceHelper;
use EmailHandler;
use Illuminate\Http\Request;
use Theme;
use Theme\Martfury\Http\Requests\SendDownloadAppLinksRequest;
use Theme\Martfury\Http\Resources\BrandResource;
use Theme\Martfury\Http\Resources\PostResource;
use Theme\Martfury\Http\Resources\ProductCategoryResource;
use Theme\Martfury\Http\Resources\ReviewResource;

class MartfuryController extends PublicController
{
    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function ajaxGetProducts(Request $request, BaseHttpResponse $response)
    {
        if (!$request->ajax() || !$request->wantsJson()) {
            return $response->setNextUrl(route('public.index'));
        }

        $withCount = [];
        if (EcommerceHelper::isReviewEnabled()) {
            $withCount = [
                'reviews',
                'reviews as reviews_avg' => function ($query) {
                    $query->select(DB::raw('avg(star)'));
                },
            ];
        }

        $products = get_products_by_collections([
            'collections' => [
                'by'       => 'id',
                'value_in' => [$request->input('collection_id')],
            ],
            'take'        => (int) $request->input('limit', 10),
            'with'        => [
                'slugable',
                'variations',
                'productCollections',
                'variationAttributeSwatchesForProductList',
            ],
            'withCount'   => $withCount,
        ]);

        $data = [];
        foreach ($products as $product) {
            $data[] = Theme::partial('product-item', compact('product'));
        }

        return $response->setData($data);
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function getFeaturedProductCategories(Request $request, BaseHttpResponse $response)
    {
        if (!$request->ajax() || !$request->wantsJson()) {
            return $response->setNextUrl(route('public.index'));
        }

        $categories = get_featured_product_categories(['take' => null]);

        return $response->setData(ProductCategoryResource::collection($categories));
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function ajaxGetTrendingProducts(Request $request, BaseHttpResponse $response)
    {
        if (!$request->ajax() || !$request->wantsJson()) {
            return $response->setNextUrl(route('public.index'));
        }

        $withCount = [];
        if (EcommerceHelper::isReviewEnabled()) {
            $withCount = [
                'reviews',
                'reviews as reviews_avg' => function ($query) {
                    $query->select(DB::raw('avg(star)'));
                },
            ];
        }

        $products = get_trending_products([
            'take'      => (int) $request->input('limit', 10),
            'with'      => [
                'slugable',
                'variations',
                'productCollections',
                'variationAttributeSwatchesForProductList',
            ],
            'withCount' => $withCount,
        ]);

        $data = [];
        foreach ($products as $product) {
            $data[] = Theme::partial('product-item', compact('product'));
        }

        return $response->setData($data);
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function ajaxGetFeaturedBrands(Request $request, BaseHttpResponse $response)
    {
        if (!$request->ajax() || !$request->wantsJson()) {
            return $response->setNextUrl(route('public.index'));
        }

        $brands = get_featured_brands();

        return $response->setData(BrandResource::collection($brands));
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function ajaxGetFeaturedProducts(Request $request, BaseHttpResponse $response)
    {
        if (!$request->ajax() || !$request->wantsJson()) {
            return $response->setNextUrl(route('public.index'));
        }

        $data = [];

        $withCount = [];
        if (EcommerceHelper::isReviewEnabled()) {
            $withCount = [
                'reviews',
                'reviews as reviews_avg' => function ($query) {
                    $query->select(DB::raw('avg(star)'));
                },
            ];
        }

        $products = get_featured_products([
            'take'      => (int) $request->input('limit', 10),
            'with'      => [
                'slugable',
                'variations',
                'productCollections',
                'variationAttributeSwatchesForProductList',
            ],
            'withCount' => $withCount,
        ]);

        foreach ($products as $product) {
            $data[] = Theme::partial('product-item', compact('product'));
        }

        return $response->setData($data);
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function ajaxGetTopRatedProducts(Request $request, BaseHttpResponse $response)
    {
        if (!$request->ajax() || !$request->wantsJson()) {
            return $response->setNextUrl(route('public.index'));
        }

        $withCount = [];
        if (EcommerceHelper::isReviewEnabled()) {
            $withCount = [
                'reviews',
                'reviews as reviews_avg' => function ($query) {
                    $query->select(DB::raw('avg(star)'));
                },
            ];
        }

        $products = get_top_rated_products((int) $request->input('limit', 10), [
            'slugable',
            'variations',
            'productCollections',
            'variationAttributeSwatchesForProductList',
        ], $withCount);

        $data = [];
        foreach ($products as $product) {
            $data[] = Theme::partial('product-item', compact('product'));
        }

        return $response->setData($data);
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function ajaxGetOnSaleProducts(Request $request, BaseHttpResponse $response)
    {
        if (!$request->ajax() || !$request->wantsJson()) {
            return $response->setNextUrl(route('public.index'));
        }

        $withCount = [];
        if (EcommerceHelper::isReviewEnabled()) {
            $withCount = [
                'reviews',
                'reviews as reviews_avg' => function ($query) {
                    $query->select(DB::raw('avg(star)'));
                },
            ];
        }

        $products = get_products_on_sale([
            'take'      => (int) $request->input('limit', 10),
            'with'      => [
                'slugable',
                'variations',
                'productCollections',
                'variationAttributeSwatchesForProductList',
            ],
            'withCount' => $withCount,
        ]);

        $data = [];
        foreach ($products as $product) {
            $data[] = Theme::partial('product-item', compact('product'));
        }

        return $response->setData($data);
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function ajaxCart(Request $request, BaseHttpResponse $response)
    {
        if (!$request->ajax()) {
            return $response->setNextUrl(route('public.index'));
        }

        return $response->setData([
            'count' => Cart::instance('cart')->count(),
            'html'  => Theme::partial('cart'),
        ]);
    }

    /**
     * @param Request $request
     * @param int $id
     * @param BaseHttpResponse $response
     * @return mixed
     */
    public function getQuickView(Request $request, $id, BaseHttpResponse $response)
    {
        if (!$request->ajax()) {
            return $response->setNextUrl(route('public.index'));
        }

        $withCount = [];
        if (EcommerceHelper::isReviewEnabled()) {
            $withCount = [
                'reviews',
                'reviews as reviews_avg' => function ($query) {
                    $query->select(DB::raw('avg(star)'));
                },
            ];
        }

        $product = get_products([
            'condition' => [
                'ec_products.id'     => $id,
                'ec_products.status' => BaseStatusEnum::PUBLISHED,
            ],
            'take'      => 1,
            'with'      => [
                'defaultProductAttributes',
                'slugable',
                'tags',
                'tags.slugable',
            ],
            'withCount' => $withCount,
        ]);

        if (!$product) {
            return $response->setNextUrl(route('public.index'));
        }

        $productImages = $product->images;
        if ($product->is_variation) {
            $product = $product->original_product;
            $selectedAttrs = app(ProductVariationInterface::class)->getAttributeIdsOfChildrenProduct($product->id);
            if (count($productImages) == 0) {
                $productImages = $product->images;
            }
        } else {
            $selectedAttrs = $product->defaultVariation->productAttributes;
        }

        Theme::asset()->remove('app-js');

        return $response->setData(Theme::partial('quick-view', compact('product', 'selectedAttrs', 'productImages')));
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @param PostInterface $postRepository
     * @return BaseHttpResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function ajaxGetFeaturedPosts(Request $request, BaseHttpResponse $response, PostInterface $postRepository)
    {
        if (!$request->ajax() || !$request->wantsJson()) {
            return $response->setNextUrl(route('public.index'));
        }

        $posts = $postRepository->getFeatured(3);

        return $response
            ->setData(PostResource::collection($posts))
            ->toApiResponse();
    }

    /**
     * @param int $id
     * @param Request $request
     * @param BaseHttpResponse $response
     * @param ProductInterface $productRepository
     * @return BaseHttpResponse
     */
    public function ajaxGetRelatedProducts(
        $id,
        Request $request,
        BaseHttpResponse $response,
        ProductInterface $productRepository
    ) {
        if (!$request->ajax() || !$request->wantsJson()) {
            return $response->setNextUrl(route('public.index'));
        }

        $product = $productRepository->findOrFail($id);

        $products = get_related_products($product, $request->input('limit'));

        $data = [];
        foreach ($products as $product) {
            $data[] = Theme::partial('product-item', compact('product'));
        }

        return $response->setData($data);
    }

    /**
     * @param int $id
     * @param Request $request
     * @param BaseHttpResponse $response
     * @param ProductInterface $productRepository
     * @return BaseHttpResponse
     */
    public function ajaxGetProductReviews(
        $id,
        Request $request,
        BaseHttpResponse $response,
        ProductInterface $productRepository
    ) {
        if (!$request->ajax() || !$request->wantsJson()) {
            return $response->setNextUrl(route('public.index'));
        }

        $product = $productRepository->getFirstBy([
            'id'           => $id,
            'status'       => BaseStatusEnum::PUBLISHED,
            'is_variation' => 0,
        ], [], ['variations']);

        if (!$product) {
            abort(404);
        }

        $star = (int)$request->input('star');
        $perPage = (int)$request->input('per_page', 10);

        $reviews = EcommerceHelper::getProductReviews($product, $star, $perPage);

        if ($star) {
            $message = __(':total review(s) ":star star" for ":product"', [
                'total'   => $reviews->total(),
                'product' => $product->name,
                'star'    => $star,
            ]);
        } else {
            $message = __(':total review(s) for ":product"', [
                'total'   => $reviews->total(),
                'product' => $product->name,
            ]);
        }

        return $response
            ->setData(ReviewResource::collection($reviews))
            ->setMessage($message)
            ->toApiResponse();
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function ajaxSearchProducts(Request $request, BaseHttpResponse $response, GetProductService $productService)
    {
        if (!$request->ajax()) {
            return $response->setNextUrl(route('public.index'));
        }

        $request->merge(['num' => 10]);

        $with = [
            'slugable',
            'variations',
            'productCollections',
            'variationAttributeSwatchesForProductList',
        ];

        $withCount = [];
        if (EcommerceHelper::isReviewEnabled()) {
            $withCount = [
                'reviews',
                'reviews as reviews_avg' => function ($query) {
                    $query->select(DB::raw('avg(star)'));
                },
            ];
        }

        $products = $productService->getProduct($request, null, null, $with, $withCount);

        $query = $request->input('q');

        return $response->setData(Theme::partial('ajax-search-results', compact('products', 'query')));
    }

    /**
     * @param SendDownloadAppLinksRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Throwable
     */
    public function ajaxSendDownloadAppLinks(SendDownloadAppLinksRequest $request, BaseHttpResponse $response)
    {
        if (!$request->ajax() || !$request->wantsJson()) {
            return $response->setNextUrl(route('public.index'));
        }

        EmailHandler::setModule(Theme::getThemeName())
            ->sendUsingTemplate('download_app', $request->input('email'), [], false, 'themes', __('Download apps'));

        return $response->setMessage(__('We sent an email with download links to your email, please check it!'));
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @param ProductInterface $productRepository
     * @param ProductCategoryInterface $productCategoryRepository
     * @return BaseHttpResponse
     */
    public function ajaxGetProductsByCategoryId(
        Request $request,
        BaseHttpResponse $response,
        ProductInterface $productRepository,
        ProductCategoryInterface $productCategoryRepository
    ) {
        if (!$request->ajax() || !$request->wantsJson()) {
            return $response->setNextUrl(route('public.index'));
        }

        $categoryId = $request->input('category_id');

        if (!$categoryId) {
            return $response;
        }

        $category = $productCategoryRepository->findById($categoryId);

        if (!$category) {
            return $response;
        }

        $withCount = [];
        if (EcommerceHelper::isReviewEnabled()) {
            $withCount = [
                'reviews',
                'reviews as reviews_avg' => function ($query) {
                    $query->select(DB::raw('avg(star)'));
                },
            ];
        }

        $products = $productRepository->getProductsByCategories([
            'categories' => [
                'by'       => 'id',
                'value_in' => $category->getChildrenIds($category, [$category->id]),
            ],
            'take'       => (int) $request->input('limit', 10),
            'withCount'  => $withCount,
        ]);

        $data = [];
        foreach ($products as $product) {
            $data[] = Theme::partial('product-item', compact('product'));
        }

        return $response->setData($data);
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @param ProductCategoryInterface $productCategoryRepository
     * @return BaseHttpResponse
     */
    public function ajaxGetProductCategories(
        Request $request,
        BaseHttpResponse $response,
        ProductCategoryInterface $productCategoryRepository
    ) {

        if (!$request->ajax() || !$request->wantsJson()) {
            return $response->setNextUrl(route('public.index'));
        }

        $categoryIds = $request->input('categories', []);
        if (empty($categoryIds)) {
            return $response;
        }

        $categories = $productCategoryRepository->advancedGet([
            'condition' => [
                'status' => BaseStatusEnum::PUBLISHED,
                ['id', 'IN', $categoryIds],
            ],
            'with'      => ['slugable'],
        ]);

        return $response->setData(ProductCategoryResource::collection($categories));
    }

    /**
     * @param Request $request
     * @param int $id
     * @param BaseHttpResponse $response
     * @param FlashSaleInterface $flashSaleRepository
     * @return BaseHttpResponse
     */
    public function ajaxGetFlashSale(
        Request $request,
        $id,
        BaseHttpResponse $response,
        FlashSaleInterface $flashSaleRepository
    ) {
        if (!$request->ajax() || !$request->wantsJson()) {
            return $response->setNextUrl(route('public.index'));
        }

        $flashSale = $flashSaleRepository->getModel()
            ->notExpired()
            ->where('id', $id)
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->with([
                'products' => function ($query) {
                    $withCount = [];
                    if (EcommerceHelper::isReviewEnabled()) {
                        $withCount = [
                            'reviews',
                            'reviews as reviews_avg' => function ($query) {
                                $query->select(DB::raw('avg(star)'));
                            },
                        ];
                    }

                    return $query->where('status', BaseStatusEnum::PUBLISHED)->withCount($withCount);
                },
            ])
            ->first();

        if (!$flashSale) {
            return $response->setData([]);
        }

        $data = [];
        foreach ($flashSale->products as $product) {
            $data[] = Theme::partial('flash-sale-product', compact('product', 'flashSale'));
        }

        return $response->setData($data);
    }
}
