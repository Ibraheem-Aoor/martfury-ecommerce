<?php

namespace Botble\Ecommerce\Http\Controllers;

use Assets;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Exports\TemplateProductExport;
use Botble\Ecommerce\Http\Requests\BulkImportRequest;
use Botble\Ecommerce\Http\Requests\ProductRequest;
use Botble\Ecommerce\Imports\ProductImport;
use Botble\Ecommerce\Imports\ValidateProductImport;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Excel;

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

        $this->validateProductImport
            ->setValidatorClass(new ProductRequest)
            ->import($file);

        if ($this->validateProductImport->failures()->count()) {
            $data = [
                'total_failed'  => $this->validateProductImport->failures()->count(),
                'total_error'   => $this->validateProductImport->errors()->count(),
                'failures'      => $this->validateProductImport->failures(),
            ];

            $message = trans('plugins/ecommerce::bulk-import.import_failed_description');

            return $response
                ->setError()
                ->setData($data)
                ->setMessage($message);
        }

        $this->productImport
            ->setValidatorClass(new ProductRequest)
            ->setImportType($request->input('type'))
            ->import($file); // Start import

        $data = [
            'total_success' => $this->productImport->successes()->count(),
            'total_failed'  => $this->productImport->failures()->count(),
            'total_error'   => $this->productImport->errors()->count(),
            'failures'      => $this->productImport->failures(),
            'successes'     => $this->productImport->successes(),
        ];

        $message = trans('plugins/ecommerce::bulk-import.imported_successfully');

        $result = trans('plugins/ecommerce::bulk-import.results', [
            'success' => $data['total_success'],
            'failed'  => $data['total_failed'],
        ]);

        return $response->setData($data)->setMessage($message . ' ' . $result);
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
