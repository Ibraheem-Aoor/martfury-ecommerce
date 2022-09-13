<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\Widget\Models\Widget as WidgetModel;
use Theme;

class WidgetSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        WidgetModel::truncate();

        $data = [
            'en_US' => [
                [
                    'widget_id'  => 'CustomMenuWidget',
                    'sidebar_id' => 'footer_sidebar',
                    'position'   => 1,
                    'data'       => [
                        'id'      => 'CustomMenuWidget',
                        'name'    => 'Quick links',
                        'menu_id' => 'quick-links',
                    ],
                ],
                [
                    'widget_id'  => 'CustomMenuWidget',
                    'sidebar_id' => 'footer_sidebar',
                    'position'   => 2,
                    'data'       => [
                        'id'      => 'CustomMenuWidget',
                        'name'    => 'Company',
                        'menu_id' => 'company',
                    ],
                ],
                [
                    'widget_id'  => 'CustomMenuWidget',
                    'sidebar_id' => 'footer_sidebar',
                    'position'   => 3,
                    'data'       => [
                        'id'      => 'CustomMenuWidget',
                        'name'    => 'Business',
                        'menu_id' => 'business',
                    ],
                ],

                [
                    'widget_id'  => 'BlogSearchWidget',
                    'sidebar_id' => 'primary_sidebar',
                    'position'   => 1,
                    'data'       => [
                        'id'      => 'BlogSearchWidget',
                        'name'    => 'Search',
                    ],
                ],
                [
                    'widget_id'  => 'BlogCategoriesWidget',
                    'sidebar_id' => 'primary_sidebar',
                    'position'   => 2,
                    'data'       => [
                        'id'      => 'BlogCategoriesWidget',
                        'name'    => 'Categories',
                    ],
                ],
                [
                    'widget_id'  => 'RecentPostsWidget',
                    'sidebar_id' => 'primary_sidebar',
                    'position'   => 3,
                    'data'       => [
                        'id'      => 'RecentPostsWidget',
                        'name'    => 'Recent Posts',
                    ],
                ],
                [
                    'widget_id'  => 'TagsWidget',
                    'sidebar_id' => 'primary_sidebar',
                    'position'   => 4,
                    'data'       => [
                        'id'      => 'TagsWidget',
                        'name'    => 'Popular Tags',
                    ],
                ],
                [
                    'widget_id'  => 'ProductCategoriesWidget',
                    'sidebar_id' => 'bottom_footer_sidebar',
                    'position'   => 1,
                    'data'       => [
                        'id'         => 'ProductCategoriesWidget',
                        'name'       => 'Consumer Electric',
                        'categories' => [18, 2, 3, 4, 5, 6, 7],
                    ],
                ],
                [
                    'widget_id'  => 'ProductCategoriesWidget',
                    'sidebar_id' => 'bottom_footer_sidebar',
                    'position'   => 2,
                    'data'       => [
                        'id'         => 'ProductCategoriesWidget',
                        'name'       => 'Clothing & Apparel',
                        'categories' => [8, 9, 10, 11, 12],
                    ],
                ],
                [
                    'widget_id'  => 'ProductCategoriesWidget',
                    'sidebar_id' => 'bottom_footer_sidebar',
                    'position'   => 3,
                    'data'       => [
                        'id'         => 'ProductCategoriesWidget',
                        'name'       => 'Home, Garden & Kitchen',
                        'categories' => [13, 14, 15, 16, 17],
                    ],
                ],
                [
                    'widget_id'  => 'ProductCategoriesWidget',
                    'sidebar_id' => 'bottom_footer_sidebar',
                    'position'   => 4,
                    'data'       => [
                        'id'         => 'ProductCategoriesWidget',
                        'name'       => 'Health & Beauty',
                        'categories' => [20, 21, 22, 23, 24],
                    ],
                ],
                [
                    'widget_id'  => 'ProductCategoriesWidget',
                    'sidebar_id' => 'bottom_footer_sidebar',
                    'position'   => 5,
                    'data'       => [
                        'id'         => 'ProductCategoriesWidget',
                        'name'       => 'Computer & Technologies',
                        'categories' => [25, 26, 27, 28, 29, 19],
                    ],
                ],
            ],
            'vi'    => [
                [
                    'widget_id'  => 'CustomMenuWidget',
                    'sidebar_id' => 'footer_sidebar',
                    'position'   => 1,
                    'data'       => [
                        'id'      => 'CustomMenuWidget',
                        'name'    => 'Liên kết nhanh',
                        'menu_id' => 'lien-ket-nhanh',
                    ],
                ],
                [
                    'widget_id'  => 'CustomMenuWidget',
                    'sidebar_id' => 'footer_sidebar',
                    'position'   => 2,
                    'data'       => [
                        'id'      => 'CustomMenuWidget',
                        'name'    => 'Công ty',
                        'menu_id' => 'cong-ty',
                    ],
                ],
                [
                    'widget_id'  => 'CustomMenuWidget',
                    'sidebar_id' => 'footer_sidebar',
                    'position'   => 3,
                    'data'       => [
                        'id'      => 'CustomMenuWidget',
                        'name'    => 'Doanh nghiệp',
                        'menu_id' => 'doanh-nghiep',
                    ],
                ],

                [
                    'widget_id'  => 'BlogSearchWidget',
                    'sidebar_id' => 'primary_sidebar',
                    'position'   => 1,
                    'data'       => [
                        'id'      => 'BlogSearchWidget',
                        'name'    => 'Tìm kiếm',
                    ],
                ],
                [
                    'widget_id'  => 'BlogCategoriesWidget',
                    'sidebar_id' => 'primary_sidebar',
                    'position'   => 2,
                    'data'       => [
                        'id'      => 'BlogCategoriesWidget',
                        'name'    => 'Danh mục bài viết',
                    ],
                ],
                [
                    'widget_id'  => 'RecentPostsWidget',
                    'sidebar_id' => 'primary_sidebar',
                    'position'   => 3,
                    'data'       => [
                        'id'      => 'RecentPostsWidget',
                        'name'    => 'Bài viết gần đây',
                    ],
                ],
                [
                    'widget_id'  => 'TagsWidget',
                    'sidebar_id' => 'primary_sidebar',
                    'position'   => 4,
                    'data'       => [
                        'id'      => 'TagsWidget',
                        'name'    => 'Tags phổ biến',
                    ],
                ],
                [
                    'widget_id'  => 'ProductCategoriesWidget',
                    'sidebar_id' => 'bottom_footer_sidebar',
                    'position'   => 1,
                    'data'       => [
                        'id'         => 'ProductCategoriesWidget',
                        'name'       => 'Đồ dùng điện tử',
                        'categories' => [18, 2, 3, 4, 5, 6, 7],
                    ],
                ],
                [
                    'widget_id'  => 'ProductCategoriesWidget',
                    'sidebar_id' => 'bottom_footer_sidebar',
                    'position'   => 2,
                    'data'       => [
                        'id'         => 'ProductCategoriesWidget',
                        'name'       => 'Quần áo & may mặc',
                        'categories' => [8, 9, 10, 11, 12],
                    ],
                ],
                [
                    'widget_id'  => 'ProductCategoriesWidget',
                    'sidebar_id' => 'bottom_footer_sidebar',
                    'position'   => 3,
                    'data'       => [
                        'id'         => 'ProductCategoriesWidget',
                        'name'       => 'Dụng cụ nhà bếp',
                        'categories' => [13, 14, 15, 16, 17],
                    ],
                ],
                [
                    'widget_id'  => 'ProductCategoriesWidget',
                    'sidebar_id' => 'bottom_footer_sidebar',
                    'position'   => 4,
                    'data'       => [
                        'id'         => 'ProductCategoriesWidget',
                        'name'       => 'Sức khỏe & làm đẹp',
                        'categories' => [20, 21, 22, 23, 24],
                    ],
                ],
                [
                    'widget_id'  => 'ProductCategoriesWidget',
                    'sidebar_id' => 'bottom_footer_sidebar',
                    'position'   => 5,
                    'data'       => [
                        'id'         => 'ProductCategoriesWidget',
                        'name'       => 'Máy tính & công nghệ',
                        'categories' => [25, 26, 27, 28, 29, 19],
                    ],
                ],
            ],
        ];

        $theme = Theme::getThemeName();

        foreach ($data as $locale => $widgets) {
            foreach ($widgets as $item) {
                $item['theme'] = $locale == 'en_US' ? $theme : ($theme . '-' . $locale);
                WidgetModel::create($item);
            }
        }
    }
}
