<?php

namespace Botble\Marketplace\Http\Controllers\Fronts;

use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Http\Requests\BrandRequest;
use Botble\Ecommerce\Models\Brand;
use Botble\Ecommerce\Repositories\Interfaces\BrandInterface;
use Botble\Marketplace\Tables\BrandTable;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;
use MarketplaceHelper;
use Botble\Marketplace\Forms\BrandForm;
use Assets;
use Illuminate\Support\Facades\Route;

class BrandController extends BaseController
{
    /**
     * @var BrandInterface
     */
    protected $brandRepository;

    /**
     * BrandController constructor.
     * @param BrandInterface $brandRepository
     */
    public function __construct(BrandInterface $brandRepository)
    {
        $this->brandRepository = $brandRepository;
    }

    /**
     * @param BrandTable $dataTable
     * @return Factory|View
     * @throws Throwable
     */
    public function index(BrandTable $dataTable)
    {
        page_title()->setTitle(trans('plugins/ecommerce::brands.menu'));

        return $dataTable->render(MarketplaceHelper::viewPath('dashboard.table.base'));

    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/ecommerce::brands.create'));
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

        return $formBuilder->create(BrandForm::class)->renderForm();
    }

    /**
     * @param BrandRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(BrandRequest $request, BaseHttpResponse $response)
    {
        $request['created_by_id'] = auth('customer')->id();
        $brand = $this->brandRepository->createOrUpdate($request->input());
        event(new CreatedContentEvent(BRAND_MODULE_SCREEN_NAME, $request, $brand));

        return $response
            ->setPreviousUrl(route('marketplace.vendor.brands.index'))
            ->setNextUrl(route('marketplace.vendor.brands.edit', $brand->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    /**
     * @param int $id
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function edit($id, FormBuilder $formBuilder)
    {
        $brand = $this->brandRepository->findOrFail($id);
        page_title()->setTitle(trans('plugins/ecommerce::brands.edit') . ' "' . $brand->name . '"');

        return $formBuilder->create(BrandForm::class, ['model' => $brand])->renderForm();
    }

    /**
     * @param int $id
     * @param BrandRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, BrandRequest $request, BaseHttpResponse $response)
    {
        $brand = $this->brandRepository->findOrFail($id);
        $brand->fill($request->input());

        $this->brandRepository->createOrUpdate($brand);

        event(new UpdatedContentEvent(BRAND_MODULE_SCREEN_NAME, $request, $brand));

        return $response
            ->setPreviousUrl(route('marketplace.vendor.brands.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
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
            $brand = $this->brandRepository->findOrFail($id);
            $this->brandRepository->delete($brand);

            event(new DeletedContentEvent(BRAND_MODULE_SCREEN_NAME, $request, $brand));

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
            $brand = $this->brandRepository->findOrFail($id);
            $this->brandRepository->delete($brand);
            event(new DeletedContentEvent(BRAND_MODULE_SCREEN_NAME, $request, $brand));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }


    public function vendorCreateBrand(Request $request)
    {
        $request->validate(['name' => 'required']);
        Brand::create($request->all());
        return response()->json(['status' => true , 'is_added' => true , 'message' => __('Brand Created Successfully')] , 200);
    }
}
