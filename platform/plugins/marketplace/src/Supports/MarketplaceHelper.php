<?php

namespace Botble\Marketplace\Supports;

use Botble\Ecommerce\Enums\DiscountTypeOptionEnum;
use Illuminate\Support\Arr;
use Theme;

class MarketplaceHelper
{
    /**
     * @param string $view
     * @param array $data
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Response
     */
    public function view(string $view, array $data = [])
    {
        return view($this->viewPath($view), $data);
    }

    /**
     * @return string
     */
    public function viewPath(string $view)
    {
        $themeView = Theme::getThemeNamespace() . '::views.marketplace.' . $view;

        if (view()->exists($themeView)) {
            return $themeView;
        }

        return 'plugins/marketplace::themes.' . $view;
    }

    /**
     * @param string $key
     * @param null $default
     * @return string
     */
    function getSetting($key, $default = '')
    {
        return setting($this->getSettingKey($key), $default);
    }

    /**
     * @param string $key
     * @return string
     */
    public function getSettingKey($key = '')
    {
        return config('plugins.marketplace.general.prefix') . $key;
    }

    /**
     * @return array
     */
    public function discountTypes()
    {
        return Arr::except(DiscountTypeOptionEnum::labels(), [DiscountTypeOptionEnum::SAME_PRICE]);
    }

    /**
     * @return string
     */
    public function getAssetVersion(): string
    {
        return '1.0.0';
    }
}
