<?php

namespace Botble\Marketplace\Listeners;

use Botble\Ecommerce\Models\Customer;
use Botble\Marketplace\Models\Store;
use Botble\Marketplace\Repositories\Interfaces\StoreInterface;
use Botble\Marketplace\Repositories\Interfaces\VendorInfoInterface;
use Botble\Slug\Models\Slug;
use EmailHandler;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use MarketplaceHelper;
use SlugHelper;

class SaveVendorInformationListener
{
    /**
     * @var StoreInterface
     */
    protected $storeRepository;

    /**
     * @var VendorInfoInterface
     */
    protected $vendorInfoRepository;

    /**
     * @var Request
     */
    protected $request;

    /**
     * SaveVendorInformationListener constructor.
     * @param StoreInterface $storeRepository
     * @param VendorInfoInterface $vendorInfoRepository
     * @param Request $request
     */
    public function __construct(
        StoreInterface $storeRepository,
        VendorInfoInterface $vendorInfoRepository,
        Request $request
    ) {
        $this->storeRepository = $storeRepository;
        $this->vendorInfoRepository = $vendorInfoRepository;
        $this->request = $request;
    }

    /**
     * Handle the event.
     *
     * @param Registered $event
     * @return void
     */
    public function handle(Registered $event)
    {
        $customer = $event->user;
        if (get_class($customer) == Customer::class &&
            !$customer->is_vendor &&
            $this->request->input('is_vendor') == 1) {
            $store = $this->storeRepository->getFirstBy(['customer_id' => $customer->getAuthIdentifier()]);
            if (!$store) {
                $store = $this->storeRepository->createOrUpdate([
                    'name'        => clean($this->request->input('shop_name')),
                    'phone'       => clean($this->request->input('shop_phone')),
                    'customer_id' => $customer->getAuthIdentifier(),
                ]);
            }

            if (!$store->slug) {
                Slug::create([
                    'reference_type' => Store::class,
                    'reference_id'   => $store->id,
                    'key'            => Str::slug($this->request->input('shop_url')),
                    'prefix'         => SlugHelper::getPrefix(Store::class),
                ]);
            }

            $customer->is_vendor = true;

            if (MarketplaceHelper::getSetting('verify_vendor', 1)) {
                $mailer = EmailHandler::setModule(MARKETPLACE_MODULE_SCREEN_NAME);
                if ($mailer->templateEnabled('verify_vendor')) {
                    EmailHandler::setModule(MARKETPLACE_MODULE_SCREEN_NAME)
                        ->setVariableValues([
                            'customer_name'  => $customer->name,
                            'customer_email' => $customer->email,
                            'customer_phone' => $customer->phone,
                            'store_name'     => $store->name,
                            'store_phone'    => $store->phone,
                            'store_link'     => route('marketplace.unverified-vendors.view', $customer->id),
                        ]);
                    $mailer->sendUsingTemplate('verify_vendor', get_admin_email()->first());
                }
            } else {
                $customer->vendor_verified_at = now();
            }

            if (!$customer->vendorInfo->id) {
                // Create vendor info
                $this->vendorInfoRepository->createOrUpdate([
                    'customer_id' => $customer->id,
                ]);
            }

            $customer->save();
        }
    }
}
