<?php

namespace Botble\Marketplace\Http\Controllers\Fronts;
use Botble\Ecommerce\Imports\VendorProductImport;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Assets;
use Botble\Ecommerce\Exports\TemplateProductExport;
use Botble\Ecommerce\Http\Requests\BulkImportRequest;
use Botble\Ecommerce\Http\Requests\ProductRequest;
use Botble\Ecommerce\Imports\ProductImport;
use Botble\Ecommerce\Imports\ValidateProductImport;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Facades\Excel as FacadesExcel;
use MarketplaceHelper;


class BulkImportController extends BaseController
{

    /**
     * @var ProductImport
     */
    protected $productImport;

    /**
     * @var ProductImport
     */
    protected $validateProductImport;

    /**
     * BulkImportController constructor.
     * @param ProductImport $productImport
     */
    public function __construct(ProductImport $productImport, ValidateProductImport $validateProductImport)
    {
        $this->productImport = $productImport;
        $this->validateProductImport = $validateProductImport;
    }

    /**
     * @return Factory|View
     */
    public function index()
    {
        page_title()->setTitle(trans('plugins/ecommerce::bulk-import.name'));

        Assets::addScriptsDirectly(['vendor/core/plugins/ecommerce/js/bulk-import.js']);

        return MarketplaceHelper::view('dashboard.products.import.index');
    }

    /**
     * @param BulkImportRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function postImport(Request $request, BaseHttpResponse $response)
    {
        @ini_set('max_execution_time', -1);
        @ini_set('memory_limit', -1);

        $file = $request->file('file');
        $importer = new VendorProductImport();
        if(FacadesExcel::import($importer , $file)){
            $message = trans('plugins/ecommerce::bulk-import.imported_successfully');
        }else{
            $message = trans('plugins/ecommerce::bulk-import.import_failed_description');
        }
        return $response->setMessage($message);

    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadTemplate(Request $request)
    {
        $extension = $request->input('extension');
        $extension = $extension == 'csv' ? $extension : Excel::XLSX;
        $file = 'product-import-template/product_bulk_demo.'.$extension;
        ob_clean();
        return Storage::download($file);
    }
}
