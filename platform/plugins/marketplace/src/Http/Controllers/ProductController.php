<?php

namespace Botble\Marketplace\Http\Controllers;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use EmailHandler;
use MarketplaceHelper;

class ProductController extends BaseController
{
    /**
     * @param int $id
     * @param ProductInterface $productRepository
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function approveProduct($id, ProductInterface $productRepository, BaseHttpResponse $response)
    {
        $product = $productRepository->findOrFail($id);
        $product->status = $product->price == 0 ? BaseStatusEnum::PENDING :  BaseStatusEnum::PUBLISHED;
        $product->approved_by = auth()->id();

        $product->save();

        if (MarketplaceHelper::getSetting('enable_product_approval', 1)) {
            $store = $product->store;

            EmailHandler::setModule(MARKETPLACE_MODULE_SCREEN_NAME)
                ->setVariableValues([
                    'store_name' => $store->name,
                ])
                ->sendUsingTemplate('product-approved', $store->email);
        }

        $responsse_message = $product->status == 'pending' ?   trans('plugins/marketplace::store.approve_product_success_empty_price') :   trans('plugins/marketplace::store.approve_product_success');
        return $response->setMessage($responsse_message);
    }
}
