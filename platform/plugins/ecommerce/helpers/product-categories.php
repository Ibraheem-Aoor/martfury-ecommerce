<?php

if (!function_exists('get_product_categories')) {
    /**
     * @return \Illuminate\Support\Collection
     * @deprecated
     */
    function get_product_categories() {
        return ProductCategoryHelper::getAllProductCategories();
    }
}

if (!function_exists('get_product_categories_with_children')) {
    /**
     * @return array
     * @deprecated
     */
    function get_product_categories_with_children()
    {
        return ProductCategoryHelper::getAllProductCategoriesWithChildren();
    }
}
