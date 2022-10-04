<?php

namespace Botble\Marketplace\Http\Controllers\Fronts;

use Assets;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Supports\Helper;
use Botble\Base\Supports\Language as SupportsLanguage;
use Botble\Ecommerce\Http\Requests\ProductRequest;
use Botble\Ecommerce\Http\Requests\ProductVersionRequest;
use Botble\Ecommerce\Http\Requests\Vendor\Product\ProductFiffthRequest;
use Botble\Ecommerce\Http\Requests\Vendor\Product\ProductFirstStepRequest;
use Botble\Ecommerce\Http\Requests\Vendor\Product\ProductFourthRequest;
use Botble\Ecommerce\Http\Requests\Vendor\Product\ProductSeondRequest;
use Botble\Ecommerce\Http\Requests\Vendor\Product\ProductStep_1;
use Botble\Ecommerce\Http\Requests\Vendor\Product\ProductStep_1Request;
use Botble\Ecommerce\Http\Requests\Vendor\Product\ProductThirdRequest;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductCategory;
use Botble\Ecommerce\Repositories\Eloquent\ProductVariationRepository;
use Botble\Ecommerce\Repositories\Interfaces\BrandInterface;
use Botble\Ecommerce\Repositories\Interfaces\GroupedProductInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductAttributeInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductAttributeSetInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductVariationInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductVariationItemInterface;
use Botble\Ecommerce\Services\Products\StoreAttributesOfProductService;
use Botble\Ecommerce\Services\Products\StoreProductService;
use Botble\Ecommerce\Services\StoreProductTagService;
use Botble\Ecommerce\Traits\ProductActionsTrait;
use Botble\Marketplace\Forms\ProductForm;
use Botble\Marketplace\Forms\ProductForm2;
use Botble\Marketplace\Forms\ProductForm_2;
use Botble\Marketplace\Tables\ProductTable;
use Carbon\Carbon;
use EmailHandler;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use JetBrains\PhpStorm\Language;
use MarketplaceHelper;
use ProductCategoryHelper;
use Throwable;

class ProductController extends BaseController
{
    use ProductActionsTrait {
        ProductActionsTrait::postAddVersion as basePostAddVersion;
        ProductActionsTrait::postUpdateVersion as basePostUpdateVersion;
        ProductActionsTrait::deleteVersionItem as baseDeleteVersionItem;
    }

    /**
     * @param ProductTable $dataTable
     * @return Factory|View
     * @throws Throwable
     */
    public function index(ProductTable $table)
    {
        page_title()->setTitle(__('Products'));

        return $table->render(MarketplaceHelper::viewPath('dashboard.table.base'));
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder , Request $request)
    {
        page_title()->setTitle(trans('plugins/ecommerce::products.create'));

        Assets::addStyles(['datetimepicker'])
            ->addScripts([
                'moment',
                'datetimepicker',
                'jquery-ui',
                'input-mask',
                'blockui',
            ])
            ->addStylesDirectly(['vendor/core/plugins/ecommerce/css/ecommerce.css'])
            ->addScriptsDirectly([
                'vendor/core/plugins/ecommerce/js/edit-product.js',
                'vendor/core/plugins/ecommerce/js/product-custom.js',
            ]);

        return $formBuilder->create(ProductForm::class)->renderForm();
    }


    public function showProductCreateFirstStep()
    {
        $data['categories'] = ProductCategoryHelper::getAllProductCategories()
        ->where('status', BaseStatusEnum::PUBLISHED)->where('parent_id' , null)->where('id' , '!=' , 1);
        return MarketplaceHelper::view('dashboard.products.create-step-1' , $data);
    }

    /**
     * create product step 1
     * save product name and category only
     */
    public function postProductFirstStep(ProductFirstStepRequest $request)
    {
        $data['name'] = $request->name;
        $splited_name = explode(' ' , strtolower($request->name));
        if(count($splited_name) > 0)
        {

            $data['slug'] = $this->getProductSlug($splited_name);
        }

        $data['categories'] = [$request->parent_id , $request->sub_1_id , $request->sub_2_id];
        $request->session()->put('product_data' , $data);
        return response(['status' => true  , 'route' => route('marketplace.vendor.products.get_create_step_2')] , 200);
    }


    public function getProductSlug($splited_name)
    {
        $slug = '';
        foreach ($splited_name as $part)
            $slug .= $part.'-';
        return $slug;
    }


    public function showProductCreateSecondStep(Request $request)
    {
        Assets::addStyles(['datetimepicker'])
        ->addScripts([
            'moment',
            'datetimepicker',
            'jquery-ui',
            'input-mask',
            'blockui',
            ])
            ->addStylesDirectly(['vendor/core/plugins/ecommerce/css/ecommerce.css'])
            ->addScriptsDirectly([
                'vendor/core/plugins/ecommerce/js/edit-product.js',
            ]);
            Assets::addScriptsDirectly(config('core.base.general.editor.ckeditor.js'));
            Assets::addScriptsDirectly('vendor/core/core/base/js/editor.js');
            $data['brands'] = app(BrandInterface::class)->pluck('name', 'id');
            return MarketplaceHelper::view('dashboard.products.create-step-2' , $data);
    }



     /**
         * create product step 2
         *
         */
        public function postProductSecondStep(ProductSeondRequest $request)
        {
            $request->session()->put('product_data' ,  array_merge($request->session()->get('product_data') , $request->all()));
            return response()->json(['status' => true , 'route' => route('marketplace.vendor.products.get_create_step_3')]);
        }




        public function showProductCreateThirdStep(Request $request)
        {
            $data['countries']   = Helper::countries();
            $data['languages'] = SupportsLanguage::getListLanguages();
            return MarketplaceHelper::view('dashboard.products.create-step-3' , $data);
        }

        public function postProductCreateThirdStep(ProductThirdRequest $request)
        {
            $request->session()->put('product_data' ,  array_merge($request->session()->get('product_data') , $request->toArray()));
            return response(['status' => true  , 'route' => route('marketplace.vendor.products.get_create_step_4')] , 200);
        }

        public function showProductCreateFourthStep(Request $request)
        {
            $data = [];
            return MarketplaceHelper::view('dashboard.products.create-step-4' , $data);
        }

        public function postProductCreateFourthStep(ProductFourthRequest $request)
        {
            $request->session()->put('product_data' ,  array_merge($request->session()->get('product_data') , $request->toArray()));
            return response(['status' => true  , 'route' => route('marketplace.vendor.products.get_create_step_5')] , 200);
        }
        public function showProductCreateFifthStep(Request $request , FormBuilder $formBuilder)
        {
            page_title()->setTitle(trans('plugins/ecommerce::products.create'));

            Assets::addStyles(['datetimepicker'])
                ->addScripts([
                    'moment',
                    'datetimepicker',
                    'jquery-ui',
                    'input-mask',
                    'blockui',
                ])
                ->addStylesDirectly(['vendor/core/plugins/ecommerce/css/ecommerce.css'])
                ->addScriptsDirectly([
                    'vendor/core/plugins/ecommerce/js/edit-product.js',
                    'vendor/core/plugins/ecommerce/js/product-custom.js',
                ]);

            return $formBuilder->create(ProductForm2::class)->renderForm();

        }




        /**
         * @param ProductRequest $request
     * @param StoreProductService $service
     * @param BaseHttpResponse $response
     * @param ProductVariationInterface $variationRepository
     * @param ProductVariationItemInterface $productVariationItemRepository
     * @param GroupedProductInterface $groupedProductRepository
     * @param StoreAttributesOfProductService $storeAttributesOfProductService
     * @param StoreProductTagService $storeProductTagService
     * @return BaseHttpResponse
     * @throws Exception
     */
    public function store(
        ProductFiffthRequest $request,
        StoreProductService $service,
        BaseHttpResponse $response,
        ProductVariationInterface $variationRepository,
        ProductVariationItemInterface $productVariationItemRepository,
        GroupedProductInterface $groupedProductRepository,
        StoreAttributesOfProductService $storeAttributesOfProductService,
        StoreProductTagService $storeProductTagService
    ) {
        // dd($request->session()->get('product_data'));
        foreach($request->session()->get('product_data') as $key => $value)
        {
            $request[$key] = $value;
        }
        // dd($request);
        $request['status'] = BaseStatusEnum::PENDING;
        $request['category_id'] = $request->session()->get('product_data')['categories'][0];
        $request['model'] = Product::class;
        $product = $this->productRepository->getModel();
        $product->status = BaseStatusEnum::PENDING;
        $request->merge([
            'store_id' => auth('customer')->user()->store->id,
            'images'   => json_decode($request->input('images')),
        ]);
        $product->ean_code = $request->input('ean_code');
        $product->sku = $this->generateBorvatCode();
        $product->status = MarketplaceHelper::getSetting('enable_product_approval',
            1) ? BaseStatusEnum::PENDING : BaseStatusEnum::PUBLISHED;

        $product = $service->execute($request, $product);

        $product->created_by_id = auth('customer')->id();
        $product->created_by_type = Customer::class;
        $product->save();

        $storeProductTagService->execute($request, $product);

        $addedAttributes = $request->input('added_attributes', []);

        if ($request->input('is_added_attributes') == 1 && $addedAttributes) {
            $storeAttributesOfProductService->execute($product, array_keys($addedAttributes));

            $variation = $variationRepository->create([
                'configurable_product_id' => $product->id,
            ]);

            foreach ($addedAttributes as $attribute) {
                $productVariationItemRepository->createOrUpdate([
                    'attribute_id' => $attribute,
                    'variation_id' => $variation->id,
                ]);
            }

            $variation = $variation->toArray();

            $variation['variation_default_id'] = $variation['id'];

            $variation['sku'] = $product->sku ?? time();
            foreach ($addedAttributes as $attributeId) {
                $attribute = $this->productAttributeRepository->findById($attributeId);
                if ($attribute) {
                    $variation['sku'] .= '-' . $attribute->slug;
                }
            }

            $this->postSaveAllVersions([$variation['id'] => $variation], $variationRepository, $product->id, $response);
        }

        if ($request->has('grouped_products')) {
            $groupedProductRepository->createGroupedProducts($product->id, array_map(function ($item) {
                return [
                    'id'  => $item,
                    'qty' => 1,
                ];
            }, array_filter(explode(',', $request->input('grouped_products', '')))));
        }

        if (MarketplaceHelper::getSetting('enable_product_approval', 1)) {
            EmailHandler::setModule(MARKETPLACE_MODULE_SCREEN_NAME)
                ->setVariableValues([
                    'product_name' => $product->name,
                    'product_url'  => route('products.edit', $product->id),
                    'store_name'   => auth('customer')->user()->store->name,
                ])
                ->sendUsingTemplate('pending-product-approval');
        }
        $product->status = BaseStatusEnum::PENDING;
        $product->save();
        return $response
            ->setPreviousUrl(route('marketplace.vendor.products.index'))
            ->setNextUrl(route('marketplace.vendor.products.edit', $product->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    /**
     * @param int $id
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function edit($id, FormBuilder $formBuilder)
    {
        $product = $this->productRepository->findOrFail($id);

        if ($product->is_variation || $product->store_id != auth('customer')->user()->store->id) {
            abort(404);
        }

        page_title()->setTitle(trans('plugins/ecommerce::products.edit', ['name' => $product->name]));

        Assets::addStyles(['datetimepicker'])
            ->addScripts([
                'moment',
                'datetimepicker',
                'jquery-ui',
                'input-mask',
                'blockui',
            ])
            ->addStylesDirectly(['vendor/core/plugins/ecommerce/css/ecommerce.css'])
            ->addScriptsDirectly([
                'vendor/core/plugins/ecommerce/js/edit-product.js',
                'vendor/core/plugins/ecommerce/js/product-custom.js',
            ]);
        return $formBuilder
            ->create(ProductForm::class, ['model' => $product])
            ->renderForm();
    }

    /**
     * @param int $id
     * @param ProductRequest $request
     * @param StoreProductService $service
     * @param GroupedProductInterface $groupedProductRepository
     * @param BaseHttpResponse $response
     * @param ProductVariationInterface $variationRepository
     * @param ProductVariationItemInterface $productVariationItemRepository
     * @param StoreProductTagService $storeProductTagService
     * @return BaseHttpResponse|JsonResponse|RedirectResponse
     */
    public function update(
        $id,
        ProductRequest $request,
        StoreProductService $service,
        GroupedProductInterface $groupedProductRepository,
        BaseHttpResponse $response,
        ProductVariationInterface $variationRepository,
        ProductVariationItemInterface $productVariationItemRepository,
        StoreProductTagService $storeProductTagService
    ) {
        $product = $this->productRepository->findOrFail($id);
        $product->status = $product->status;

        if ($product->is_variation || $product->store_id != auth('customer')->user()->store->id) {
            abort(404);
        }



        $request->merge([
            'store_id' => auth('customer')->user()->store->id,
            'images'   => json_decode($request->input('images')),
        ]);

        $product = $service->execute($request, $product);
        $storeProductTagService->execute($request, $product);

        $variationRepository
            ->getModel()
            ->where('configurable_product_id', $product->id)
            ->update(['is_default' => 0]);

        $defaultVariation = $variationRepository->findById($request->input('variation_default_id'));
        if ($defaultVariation) {
            $defaultVariation->is_default = true;
            $defaultVariation->save();
        }

        $addedAttributes = $request->input('added_attributes', []);

        if ($request->input('is_added_attributes') == 1 && $addedAttributes) {
            $result = $variationRepository->getVariationByAttributesOrCreate($id, $addedAttributes);

            /**
             * @var Collection $variation
             */
            $variation = $result['variation'];

            foreach ($addedAttributes as $attribute) {
                $productVariationItemRepository->createOrUpdate([
                    'attribute_id' => $attribute,
                    'variation_id' => $variation->id,
                ]);
            }

            $variation = $variation->toArray();
            $variation['variation_default_id'] = $variation['id'];

            $product->productAttributeSets()->sync(array_keys($addedAttributes));

            $variation['sku'] = $product->sku ?? time();
            foreach (array_keys($addedAttributes) as $attributeId) {
                $attribute = $this->productAttributeRepository->findById($attributeId);
                if ($attribute) {
                    $variation['sku'] .= '-' . $attribute->slug;
                }
            }

            $this->postSaveAllVersions([$variation['id'] => $variation], $variationRepository, $product->id, $response);
        } elseif ($product->variations()->count() === 0) {
            $product->productAttributeSets()->detach();
            $product->productAttributes()->detach();
        }

        if ($request->has('grouped_products')) {
            $groupedProductRepository->createGroupedProducts($product->id, array_map(function ($item) {
                return [
                    'id'  => $item,
                    'qty' => 1,
                ];
            }, array_filter(explode(',', $request->input('grouped_products', '')))));
        }
        return $response
            ->setPreviousUrl(route('marketplace.vendor.products.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    /**
     * @param int $id
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @return BaseHttpResponse
     * @throws Throwable
     */
    public function getRelationBoxes($id, BaseHttpResponse $response)
    {
        $product = null;
        if ($id) {
            $product = $this->productRepository->findById($id);
        }

        $dataUrl = route('marketplace.vendor.products.get-list-product-for-search',
            ['product_id' => $product ? $product->id : 0]);

        return $response->setData(view('plugins/ecommerce::products.partials.extras',
            compact('product', 'dataUrl'))->render());
    }

    /**
     * {@inheritDoc}
     */
    public function postAddVersion(
        ProductVersionRequest $request,
        ProductVariationInterface $productVariation,
        $id,
        BaseHttpResponse $response
    ) {
        $request->merge([
            'images' => json_decode($request->input('images', '[]')),
        ]);

        return $this->basePostAddVersion($request, $productVariation, $id, $response);
    }

    /**
     * @param Request $request
     * @param ProductVariationRepository|ProductVariationInterface $productVariation
     * @param int $id
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Exception
     */
    public function postUpdateVersion(
        ProductVersionRequest $request,
        ProductVariationInterface $productVariation,
        $id,
        BaseHttpResponse $response
    ) {
        $request->merge([
            'images' => json_decode($request->input('images', '[]')),
        ]);

        return $this->basePostUpdateVersion($request, $productVariation, $id, $response);
    }

    /**
     * {@inheritDoc}
     */
    public function getVersionForm(
        $id,
        Request $request,
        ProductVariationInterface $productVariation,
        BaseHttpResponse $response,
        ProductAttributeSetInterface $productAttributeSetRepository,
        ProductAttributeInterface $productAttributeRepository,
        ProductVariationItemInterface $productVariationItemRepository
    ) {
        $product = null;
        $variation = null;
        $productVariationsInfo = [];

        if ($id) {
            $variation = $productVariation->findOrFail($id);
            $product = $this->productRepository->findOrFail($variation->product_id);
            $productVariationsInfo = $productVariationItemRepository->getVariationsInfo([$id]);
        }

        $productId = $variation ? $variation->configurable_product_id : $request->input('product_id');

        if ($productId) {
            $productAttributeSets = $productAttributeSetRepository->getByProductId($productId);
        } else {
            $productAttributeSets = $productAttributeSetRepository->getAllWithSelected($productId);
        }

        $originalProduct = $product;

        return $response
            ->setData(
                MarketplaceHelper::view('dashboard.products.product-variation-form', compact(
                    'productAttributeSets',
                    'product',
                    'productVariationsInfo',
                    'originalProduct'
                ))->render()
            );
    }

    /**
     * @param ProductVariationInterface $productVariation
     * @param ProductVariationItemInterface $productVariationItem
     * @param int $variationId
     * @return bool
     * @throws Exception
     */
    protected function deleteVersionItem(
        ProductVariationInterface $productVariation,
        ProductVariationItemInterface $productVariationItem,
        $variationId
    ) {
        $variation = $productVariation->findOrFail($variationId);

        $product = $variation->product()->first();

        if (!$product || $product->original_product->store_id != auth('customer')->user()->store->id) {
            abort(404);
        }

        return $this->baseDeleteVersionItem($productVariation, $productVariationItem, $variationId);
    }


    public function isProductEanCodeExists(Request $request)
    {

        $request->validate(['ean_code_check' => 'required|digits:13'] , ['ean_code_check.required' => 'EAN CODE REQUIRED' , 'ean_code_check.digits' => 'EAN CODE NOT VALID']);
        $ean_code = $request->ean_code_check;
        $product = Product::query()->where('ean_code' , $ean_code)->first();
        if($product){
            $new_product = $product->replicate();
            $new_product->save();
            $new_product->update([
            'created_at' => Carbon::now(),
            'created_by_id' => auth('customer')->id(),
            'created_by_type' => Customer::class,
            'store_id' => auth('customer')->user()->store->id,
            'status' => BaseStatusEnum::PENDING,
            ]);
            return response()->json(['status' => true , 'is_unique' => false , 'route' => route('marketplace.vendor.products.edit' , $new_product->id)] , 200);
        }
        session()->put('checked_ean_code' , $ean_code);
        return response()->json(['status' => true , 'is_unique' => true , 'route' => route('marketplace.vendor.products.get_create_step_1')] , 200);
    }


    public function getChildrenCategories(Request $request)
    {
        if($request->id)
        {
            $categories =  ProductCategory::whereParentId($request->id)->get();
            return response()->json(['status' => true , 'categories' => $categories] , 200);
        }
    }
}
