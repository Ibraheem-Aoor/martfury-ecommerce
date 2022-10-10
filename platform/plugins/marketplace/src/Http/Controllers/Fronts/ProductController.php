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
use Botble\Slug\Facades\SlugHelperFacade;
use Botble\Slug\Models\Slug;
use Botble\Slug\SlugHelper;
use Carbon\Carbon;
use EmailHandler;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;
use JetBrains\PhpStorm\Language;
use MarketplaceHelper;
use ProductCategoryHelper;
use Throwable;
use RvMedia;
use TypeError;
use Str;
use function PHPSTORM_META\type;

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
        ->where('status', BaseStatusEnum::PUBLISHED)->where('parent_id' , 0)->where('id' , '!=' , 1);
        return MarketplaceHelper::view('dashboard.products.create-step-1' , $data);
    }

    /**
     * create product step 1
     * save product name and category only
     */
    public function postProductFirstStep(ProductFirstStepRequest $request)
    {
        $data['name'] = strtolower($request->name);
        $splited_name = explode(' ' , strtolower($request->name));
        if(count($splited_name) > 0)
        {
            $data['slug'] = $this->getProductSlug($splited_name);
        }
        $data['categories'] = [$request->parent_id , $request->sub_1_id , $request->sub_2_id];
        $product_data = $request->session()->get('product_data');
        $request->session()->put('product_data' , array_merge($product_data , $data));
        return response(['status' => true  , 'route' => route('marketplace.vendor.products.get_create_step_2')] , 200);
    }



    public function getProductSlug($splited_name)
    {
        $slug = '';
        foreach ($splited_name as $part)
            $slug .= $part.'-';
        while(Slug::whereKey($slug)->exists())
        {
            $slug .= '-'.mt_rand(10000,90000);
        }
        return $slug;
    }


    public function showProductCreateSecondStep(Request $request)
    {
        page_title()->setTitle(trans('plugins/ecommerce::products.form.description'));
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
            $data = $request->session()->get('product_data');
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
            page_title()->setTitle(__('Product Attributes'));
            $data = $request->session()->get('product_data');
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
            page_title()->setTitle(__('Product Shipping Attributes'));
            $data =  $request->session()->get('product_data');
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
        foreach($request->session()->get('product_data') as $key => $value)
        {
            $request[$key] = $value;
        }
        if ($request->hasFile('image_input')) {
            $result = RvMedia::handleUpload($request->file('image_input'), 0, 'brands');
            if ($result['error'] == false) {
                $file = $result['data'];
                $request->merge(['image' => $file->url]);
            }
        }
        $request['ean_code'] = $request->session()->get('checked_ean_code');
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
        Slug::create([
            'key' => $request->slug,
            'prefix' => 'products',
            'reference_type' => Product::class,
            'reference_id' => $product->id,
        ]);
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

        if ($request->hasFile('image_input')) {
            $result = RvMedia::handleUpload($request->file('image_input'), 0, 'brands');
            if ($result['error'] == false) {
                $file = $result['data'];
                $request->merge(['image' => $file->url]);
            }
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
        $request->session()->put('product_data'  , []);
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


    /**
     * Back To previous page with old data.
     */
    public function backToPreviousStep($step_no  , Request $request)
    {
        $data = $request->session()->get('product_data');
        $data['brands'] = app(BrandInterface::class)->pluck('name', 'id');
        $data['product_categories'] = $data['categories'];
        $data['categories'] = ProductCategoryHelper::getAllProductCategories();
        $data['countries']   = Helper::countries();
        $data['languages'] = SupportsLanguage::getListLanguages();
        if(($id = $data['product_categories'][1]) != null)
            $data['sub_1_category'] = ProductCategory::whereId($id)->first();
        if(($id = $data['product_categories'][2] )!= null)
            $data['sub_2_category'] = ProductCategory::whereId($id)->first();
        $view = 'dashboard.products.create-step-'.$step_no;
        return  MarketplaceHelper::view($view , $data);
    }

    /**
     * Change  Quantity
     */
    public function changeQuantity(Request $request)
    {
        if($request->has('id') && $request->has('quantity')){
            $product = Product::findOrFail($request->id);
            $product->quantity = $request->quantity;
            $product->save();
            return response()->json(['status' => true , 'message' => trans('plugins/ecommerce::products.form.quantity_change_success')] , 200);
        }
        return response()->json(['status' => true , 'message' => trans('plugins/ecommerce::products.error')] , 500);

    }





    public function seedData()
{
        $new_categories = [
            [
                "name" => 'Supermarket' , 'children' => [
                ["name" => "Food Cupboard",
                    "children" => ["Grains & Rice",
                    "Pasta & Noodles",
                    "Cooking Oil",
                    "Vinegar",
                    "Sauce & Dressings",
                    "Sugars & Sweeteners",
                    "Flour",
                    "Herbs & Spices",]],
                    ["name" => "Beverages",
                    "children" => ["Juices",
                    "Soft Drinks",
                    "Coffee, Tea & Cocoa",
                    "Water",
                    "Powdered Drink Mixes & Flavorings",]],
                    ["name" => "Canned, Jarred & Packaged Foods",
                    "children" => ["Antipasto",
                    "Beans & Peas",
                    "Vegetables",
                    "Meat, Poultry & Seafood",]],
                    ["name" => "Breakfast Foods",
                    "children" => ["Cereal",
                    "Breakfast Biscuits & Cookies",
                    "Jams, Jellies & Sweet Spreads",
                    "Candy & Chocolate",
                    "Crisps & Chips",
                    "Nuts & Seeds",]],
                    ["name" => "Pet Supplies",
                    "children" => ["Dogs Supplies",
                    "Cats Supplies",]],
                    ["name" => "Laundry",
                    "children" => ["Liquid Detergent",
                    "Powder Detergent",
                    "Fabric Softener",
                    "Stain Removal",
                    "Bleach",]],
                    ["name" => "Household Cleaning",
                    "children" => ["Dishwashing",
                    "Air Fresheners",
                    "Kitchen Cleaners",
                    "Bathroom Cleaners",
                    "Floor Cleaners",
                    "Glass Cleaners",
                    "Disinfectants",
                    "Cleaning Tools",
                    "Paper & Plastic",]]
            ] ],

            [
                'name' => 'Fashion' , 'children' => [
                ["name" => "Women\'s Fashion",
                        "children" => ["Blouses",
                        "Shirts",
                        "Dresses",
                        "Skirts",
                        "Pants",
                        "Jeans",
                        "Swimsuits",
                        "Slippers",
                        "Sneakers",
                        "Flats & Ballerinas",
                        "Heels",
                        "Jumpsuits",
                        "Sleepwear",
                        "Sunglasses",
                        "Bags",
                        "Jewelry",
                        "Watches",]],
                        ["name" => "Men\'s Fashion",
                        "children" => ["T-Shirts",
                        "Polos",
                        "Shirts",
                        "Pants",
                        "Jeans",
                        "Shorts",
                        "Underwear",
                        "Swimsuits",
                        "Sportswear",
                        "Sneakers",
                        "Loafers",
                        "Slippers",
                        "Sandals",
                        "Jewelry",
                        "Watches",
                        "Belts",
                        "Sunglasses",]],
                        ["name" => "Baby",
                        "children" => ["Baby Boys",
                        "Baby Girls",]],
                        ["name" => "Kid\'s Fashion",
                        "children" => ["Boys Fashion",
                        "Girls Fashion",]],
                        ["name" => "Top Brands",
                        "children" => ["American Eagle",
                        "DeFacto",
                        "Reebok",
                        "Adidas",
                        "LC Waikiki",]]
                    ] ],

            [
                'name' => 'Health & Beauty' , 'children' => [
                    ["name" => "Beauty & Personal Care",
                "children" => ["Skin Care",
                "Feminine Care",
                "Shave & Hair Removal",]],
                ["name" => "Hair Care",
                "children" => ["Styling Tools & Appliances",
                "Styling Products",
                "Shampoo",]],
                ["name" => "Fragrance",
                "children" => ["Women\'s",
                "Men\'s",]],
                ["name" => "Makeup",
                "children" => ["Foundation",
                "Powder",
                "Concealers & Neutralizers",
                "Lipstick",
                "Lip Liners",
                "Lip Glosses",
                "Mascara",
                "Eyeliner",
                "Eyeshadow",]],
                ["name" => "Health Care",
                "children" => ["Wellness & Relaxation",
                "Sexual Wellness",
                "Medical Supplies & Equipment",]],
                ["name" => "Top Brands",
                "children" => ["Braun",
                "L\'oreal",
                "Durex ",
                "Maybelline",
                "Veet",
                "The Body Shop",
                "Nivea",
                "P&G",
                "Johnson\'s",
                "GSK",]]
            ] ],

            [
                'name' => 'Baby Products' , 'children' =>  [
                ["name" => "Diapering",
            "children" => ["Diapers",
            "Baby Wipes",
            "Diaper bags",]],
            ["name" => "Baby Feeding",
            "children" => ["Bottle Feeding",
            "Breast feeding",
            "Baby Food",]],
            ["name" => "Bath & Skin Care",
            "children" => ["Baby Creams & Lotions",
            "Baby Shampoo",
            "Baby Soaps",
            "Baby Conditioners",]],
            ["name" => "Baby Safety",
            "children" => ["Rails & Rail Guards",
            "Kitchen Safety",
            "Monitors",]],
            ["name" => "Strollers & Accessories",
            "children" => ["Strollers Accessories",
            "Strollers",]],
            ["name" => "Gear",
            "children" => ["Swings, Jumpers & Bouncers",
            "Backpacks & Carriers",]],
            ["name" => "Nursery",
            "children" => ["Beds, Cribs & Bedding",
            "Nursery Decor",]],
            ["name" => "Baby & Toddler Toys",
            "children" => ["Toy Gift Sets",
            "Blocks",]],
            ["name" => "Toys & Games",
            "children" => ["Dolls & Accessories",
            "Learning & Education",
            "Action Figures & Statues",
            "Arts & Crafts",
            "Dress Up & Pretend Play",
            "Puzzles",
            "Toy Remote Control & Play Vehicles",]]
            ] ],

            [
                'name' => 'Phones & Tablets' , 'children' => [
                    ["name" => "Mobile Phones",
                "children" => ["Smartphones",
                "Cell Phones",]],
                ["name" => "Tablets",
                "children" => ["iPad Tablets",
                "Tablet Accessories",
                "Bags & Cases",]],
                ["name" => "Mobile Accessories",
                "children" => ["Phone Cases",
                "Screen Protectors",
                "Bluetooth Headsets",
                "Corded Headsets",
                "Cables",
                "Portable Power Banks",
                "Smart Watches",
                "Memory Cards",
                "Chargers",
                "Car Accessories",
                "Mounts & Stands",
                "Selfie Sticks & Tripods",]],
                ["name" => "Top Brands",
                "children" => ["Realme",
                "Samsung",
                "Huawei",
                "Xiaomi",
                "Lenovo",
                "Tecno",
                "Infinix",]]
                ]
            ],
            [
                'name' => 'Home & Kitchen' , 'children' => [
                ["name" => "Home & Kitchen",
            "children" => ["Bedding",
            "Bath",
            " Storage & Organization",
            "Kitchen & Dining",
            "Furniture",
            "Home Decor",
            "Lighting",]],
            ["name" => "Tools & Home Improvement",
            "children" => ["Building Supplies",
            "Electrical",
            "Hardware",
            "Light Bulbs",
            "Power & Hand Tools",
            "Painting Supplies & Wall Treatments",]],
            ["name" => "Office Products",
            "children" => ["Office Electronics",
            "Office Furniture & Lighting",]],
            ["name" => "Small Appliances",
            "children" => ["Blenders",
            "Mixers",
            "Ovens & Toasters",
            "Microwave Ovens",
            "Food Processors",
            "Deep Fryers",
            "Juicers",
            "Coffee, Tea & Espresso Appliances",]],
            ["name" => "Heating, Cooling & Air Quality",
            "children" => ["Air Conditioners",
            "Household Fans",
            "Space Heaters",]],
            ["name" => "Appliances",
            "children" => ["Dishwashers",
            "Freezers",
            "Refrigerators",
            "Washers & Dryers",]],
            ["name" => "Cooking Appliances",
            "children" => ["Cookers",
            "Cook Top",]]
                ]
            ],
            [
                'name' => 'Electronics' , 'children' => ["name" => "Television & Video",
                    "children" => ["LED & LCD TVs",
                    "Receiver",
                    "Streaming Media Players",]],
                    ["name" => "Cameras",
                    "children" => ["Digital Cameras",
                    "Wearable & Action cameras",]],
                    ["name" => "Home Audio",
                    "children" => ["Home Theater Systems",
                    "Speakers",
                    "Portable Speakers & Docks",]],
                    ["name" => "Headphones",
                    "children" => ["Over-Ear Headphones",
                    "Earbud Headphones",
                    "On-Ear Headphones",]
                    ]
            ],

            [
                'name' => 'computing' , 'children' => [["name" => "Laptops",
                "children" => ["2 in 1 Laptops",
                "Gaming Laptops",
                "Traditional Laptops",
                "Macbooks",]],
                ["name" => "Data Storage",
                "children" => ["USB Flash Drives",
                "External Hard Drives",]],
                ["name" => "Computers & Accessories",
                "children" => ["Laptop Accessories",
                "Desktops",
                "Monitors",
                "Printers",
                "Scanners",]],
                ["name" => "Computer Components",
                "children" => ["Internal Hard Drives",
                "Graphics Cards",
                "Fans & Cooling",]],
                ["name" => "Computer Accessories",
                "children" => ["Audio & Video Accessories",
                "Computer Cable Adapters",
                "Keyboards, Mice & Accessories",
                "Printer Ink & Toner",
                "USP Gadgets",]],
                ["name" => "Networking Products",
                "children" => ["Routers",
                "Wireless Access Points",]],
                ["name" => "Top Brands",
                "children" => ["HP",
                "Lenovo",
                "Dell",
                "Apple",]]]
        ],
        [
            'name' => 'Sporting Goods' , 'children' =>  ["name" => "Cardio Training",
            "children" => ["Treadmills",
            "Exercise Bike",
            "Elliptical Trainers",]],
            ["name" => "Strength Training Equipment",
            "children" => ["Dumbbells",
            "Bars",
            "Core & Abdominal Trainers",]],
            ["name" => "Accessories",
            "children" => ["Exercise Bands",
            "Jump Ropes",
            "Exercise Mats",]],
            ["name" => "Sports & Fitness",
            "children" => ["Accessories",
            "Swimming",
            "Team Sports",]],
            ["name" => "Outdoor & Adventure",
            "children" => ["Cycling",
            "Running",]]
        ],
        [
            'name' => 'Gaming'  , 'children' => [["name" => "PlayStation 5",
            "children" => ["Consoles",
            "Games",
            "Controllers",
            "Cards",
            "Accessories",]],
            ["name" => "PlayStation 4",
            "children" => ["PS4",
            "PS4 Games",
            "PS4 Controllers",
            "Cards",
            "Accessories",
            "PS4 Cases",]],
            ["name" => "Xbox",
            "children" => ["Games",
            "Controllers",
            "Accessories",
            "Nintendo Switch",]],
            ["name" => "PC Gaming",
            "children" => ["Gaming Laptops",
            "Headsets",
            "Keyboards",
            "Mouse",
            "Gaming Chairs",
            "Monitors",]
            ],
            ]
        ],
        [
            'name' => 'Automobile' , 'children' => ["name" => "Car Care",
            "children" => ["Cleaning Kits",
            "Exterior Care",
            " Interior Care",
            "Finishing",
            "Glass Care",]],
            ["name" => "Car Electronics & Accessories",
            "children" => ["Car Electronics",
            "Car Electronics Accessories",]],
            ["name" => "Lights & Lighting Accessories",
            "children" => ["Light Covers",
            "Bulbs",
            "Accent & Off Road Lighting",]],
            ["name" => "Oils & Fluids",
            "children" => ["Brake Fluids",
            "Flushes",
            "Greases & Lubricants",
            "Oils",]],
            ["name" => "Exterior Accessories",
            "children" => ["Car Covers",
            "Mirrors",
            "Bumper Stickers, Decals & Magnets",]],
            ["name" => "Interior Accessories",
            "children" => ["Air Fresheners",
            "Consoles & Organizers",
            "Covers",
            "Cup Holders",
            "Mirrors",
            "Key Chains",
            "Floor Mats & Cargo Liners",
            "Sun Protection",
            "Seat Covers & Accessories",]],
        ],
        [
            'name' => 'Other Categories' , 'children' => ["name" => "Garden & Outdoors",
        "children" => ["Outdoor Decor",
        "Outdoor Furniture & Accessories",
        "Grills & Outdoor Cooking",
        "Gardening & Lawn Care",
        "Watering Equipment",
        "Farm & Ranch",]],
        ["name" => "Books, Movies and Music",
        "children" => ["Art & Humanities",
        "Bestselling Books",
        "Biography & Autobiography Books",
        "Business & Finance Books",
        "Education & Learning",
        "Entertainment Books",
        "Family & Lifestyle Books",
        "Fiction Books",
        "Journals & Planners",
        "Magazines",
        "Motivational & Self-Help Books",
        "Religion Books",
        "Science & Technology Books",]],
        ["name" => "Hand Crafted ",
        "children" => ["Women Accessories",
        "Baby Products",
        "Bags",
        "Bedding",
        "Home Décor",
        "Jewelry & Accessories",]],
        ["name" => "Industrial & Scientific",
        "children" => []],
        ["name" => "Pet Supplies",
        "children" => ["Dogs",
        "Cats",
        "Birds",]],
    ],
];


    ProductCategory::truncate();
    //parent categories
        $i = 0;
        foreach($new_categories as $category)
        {
            $parent = ProductCategory::create(['name' => $category['name'] , 'order' => $i++]);
            Slug::create([
                'reference_type' => ProductCategory::class,
                'reference_id'   => $parent->id,
                'key'            => Str::slug($parent->name),
                'prefix'         => SlugHelperFacade::getPrefix(ProductCategory::class),
            ]);
            $this->createChildren($parent->id, $category);

        }
        dd('Done  Successfully');

}
public function createChildren($parent_id , $category)
{
    //child and sub child categories
    if(isset($category['children']) &&  @$category['children'] != null)
    {
        foreach($category['children'] as $child)
        {
            try{
                $name = '';
                if(is_array($child))
                {
                    if(isset($child['name']))
                        $name = $child['name'];
                    else{
                        foreach($child as $c)
                        {

                            $new_c = ProductCategory::create(['name' => $c  , 'parent_id' => $parent_id]);
                            Slug::create([
                                'reference_type' => ProductCategory::class,
                                'reference_id'   => $new_c->id,
                                'key'            => Str::slug($new_c->name),
                                'prefix'         => SlugHelperFacade::getPrefix(ProductCategory::class),
                            ]);
                        }
                    }
                }else{
                    $name = $child;
                }
                $new_child = ProductCategory::create(['name' => $name  , 'parent_id' => $parent_id]);
                Slug::create([
                    'reference_type' => ProductCategory::class,
                    'reference_id'   => $new_child->id,
                    'key'            => Str::slug($new_child->name),
                    'prefix'         => SlugHelperFacade::getPrefix(ProductCategory::class),
                ]);
                if(is_array($child) &&  isset($child['children']) && @$child['children'] != null)
                {
                    foreach($child['children'] as $child)
                        {
                            $new_sub_child = ProductCategory::create(['name' => $child  , 'parent_id' => $new_child->id]);
                            Slug::create([
                                'reference_type' => ProductCategory::class,
                                'reference_id'   => $new_sub_child->id,
                                'key'            => Str::slug($new_sub_child->name),
                                'prefix'         => SlugHelperFacade::getPrefix(ProductCategory::class),
                            ]);
                        }
                }
            }catch(Throwable $ex)
            {
                dd($child);
            }
        }
    }
}


}
