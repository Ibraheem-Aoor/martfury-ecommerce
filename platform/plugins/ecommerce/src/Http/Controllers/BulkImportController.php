<?php

namespace Botble\Ecommerce\Http\Controllers;

use Assets;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Exports\TemplateProductExport;
use Botble\Ecommerce\Http\Requests\BulkImportRequest;
use Botble\Ecommerce\Http\Requests\ProductRequest;
use Botble\Ecommerce\Imports\CustomProductImporter;
use Botble\Ecommerce\Imports\ProductImport;
use Botble\Ecommerce\Imports\ValidateProductImport;
use Botble\Ecommerce\Imports\VendorProductImport;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Facades\Excel as FacadesExcel;

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

        return view('plugins/ecommerce::bulk-import.index');
    }

    /**
     * @param BulkImportRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function postImport(BulkImportRequest $request, BaseHttpResponse $response)
    {
        @ini_set('max_execution_time', -1);
        @ini_set('memory_limit', -1);

        $file = $request->file('file');
        $importer = new CustomProductImporter();
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

        return (new TemplateProductExport($extension))->download('template_products_import.' . $extension);
    }
}
