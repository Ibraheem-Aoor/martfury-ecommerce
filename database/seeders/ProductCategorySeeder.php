<?php

namespace Database\Seeders;

use Botble\Base\Models\MetaBox as MetaBoxModel;
use Botble\Base\Supports\BaseSeeder;
use Botble\Ecommerce\Models\ProductCategory;
use Botble\Slug\Models\Slug;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use MetaBox;
use SlugHelper;

class ProductCategorySeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->uploadFiles('product-categories');

        $categories = [
            [
                'name' => 'Hot Promotions',
                'icon' => 'icon-star',
            ],
            [
                'name'        => 'Electronics',
                'icon'        => 'icon-laundry',
                'image'       => 'product-categories/1.jpg',
                'is_featured' => true,
                'children'    => [
                    [
                        'name'     => 'Consumer Electronic',
                        'children' => [
                            [
                                'name' => 'Home Audio & Theaters',
                            ],
                            [
                                'name' => 'TV & Videos',
                            ],
                            [
                                'name' => 'Camera, Photos & Videos',
                            ],
                            [
                                'name' => 'Cellphones & Accessories',
                            ],
                            [
                                'name' => 'Headphones',
                            ],
                            [
                                'name' => 'Videos games',
                            ],
                            [
                                'name' => 'Wireless Speakers',
                            ],
                            [
                                'name' => 'Office Electronic',
                            ],
                        ],
                    ],
                    [
                        'name'     => 'Accessories & Parts',
                        'children' => [
                            [
                                'name' => 'Digital Cables',
                            ],
                            [
                                'name' => 'Audio & Video Cables',
                            ],
                            [
                                'name' => 'Batteries',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'name'        => 'Clothing',
                'icon'        => 'icon-shirt',
                'image'       => 'product-categories/2.jpg',
                'is_featured' => true,
            ],
            [
                'name'        => 'Computers',
                'icon'        => 'icon-desktop',
                'image'       => 'product-categories/3.jpg',
                'is_featured' => true,
                'children'    => [
                    [
                        'name'     => 'Computer & Technologies',
                        'children' => [
                            [
                                'name' => 'Computer & Tablets',
                            ],
                            [
                                'name' => 'Laptop',
                            ],
                            [
                                'name' => 'Monitors',
                            ],
                            [
                                'name' => 'Computer Components',
                            ],
                        ],
                    ],
                    [
                        'name'     => 'Networking',
                        'children' => [
                            [
                                'name' => 'Drive & Storages',
                            ],
                            [
                                'name' => 'Gaming Laptop',
                            ],
                            [
                                'name' => 'Security & Protection',
                            ],
                            [
                                'name' => 'Accessories',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'name'        => 'Home & Kitchen',
                'icon'        => 'icon-lampshade',
                'image'       => 'product-categories/4.jpg',
                'is_featured' => true,
            ],
            [
                'name'        => 'Health & Beauty',
                'icon'        => 'icon-heart-pulse',
                'image'       => 'product-categories/5.jpg',
                'is_featured' => true,
            ],
            [
                'name'        => 'Jewelry & Watch',
                'icon'        => 'icon-diamond2',
                'image'       => 'product-categories/6.jpg',
                'is_featured' => true,
            ],
            [
                'name'        => 'Technology Toys',
                'icon'        => 'icon-desktop',
                'image'       => 'product-categories/7.jpg',
                'is_featured' => true,
            ],
            [
                'name'        => 'Phones',
                'icon'        => 'icon-smartphone',
                'image'       => 'product-categories/8.jpg',
                'is_featured' => true,
            ],
            [
                'name' => 'Babies & Moms',
                'icon' => 'icon-baby-bottle',
            ],
            [
                'name' => 'Sport & Outdoor',
                'icon' => 'icon-baseball',
            ],
            [
                'name' => 'Books & Office',
                'icon' => 'icon-book2',
            ],
            [
                'name' => 'Cars & Motorcycles',
                'icon' => 'icon-car-siren',
            ],
            [
                'name' => 'Home Improvements',
                'icon' => 'icon-wrench',
            ],
        ];

        ProductCategory::truncate();
        Slug::where('reference_type', ProductCategory::class)->delete();
        MetaBoxModel::where('reference_type', ProductCategory::class)->delete();

        foreach ($categories as $index => $item) {
            $this->createCategoryItem($index, $item);
        }

        // Translations
        DB::table('ec_product_categories_translations')->truncate();

        $translations = [
            [
                'name' => 'Khuyến mãi hấp dẫn',
            ],
            [
                'name' => 'Điện tử',
                'children' => [
                    [
                        'name' => 'Điện tử tiêu dùng',
                        'children' => [
                            [
                                'name' => 'Thiết bị nghe nhìn',
                            ],
                            [
                                'name' => 'TV & Videos',
                            ],
                            [
                                'name' => 'Camera, Photos & Videos',
                            ],
                            [
                                'name' => 'Điện thoại di động & Phụ kiện',
                            ],
                            [
                                'name' => 'Tai nghe',
                            ],
                            [
                                'name' => 'Trò chơi video',
                            ],
                            [
                                'name' => 'Loa không dây',
                            ],
                            [
                                'name' => 'Điện tử văn phòng',
                            ],
                        ],
                    ],
                    [
                        'name' => 'Phụ kiện & Phụ tùng',
                        'children' => [
                            [
                                'name' => 'Digital Cables',
                            ],
                            [
                                'name' => 'Audio & Video Cables',
                            ],
                            [
                                'name' => 'Pin',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Quần áo',
            ],
            [
                'name' => 'Máy tính',
                'children' => [
                    [
                        'name' => 'Máy tính & Công nghệ',
                        'children' => [
                            [
                                'name' => 'Máy tính & Máy tính bảng',
                            ],
                            [
                                'name' => 'Máy tính xách tay',
                            ],
                            [
                                'name' => 'Màn hình',
                            ],
                            [
                                'name' => 'Linh kiện Máy tính',
                            ],
                        ],
                    ],
                    [
                        'name' => 'Mạng máy tính',
                        'children' => [
                            [
                                'name' => 'Thiết bị lưu trữ',
                            ],
                            [
                                'name' => 'Máy tính xách tay chơi game',
                            ],
                            [
                                'name' => 'Thiết bị bảo mật',
                            ],
                            [
                                'name' => 'Phụ kiện',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Đồ dùng làm bếp',
            ],
            [
                'name' => 'Sức khỏe & làm đẹp',
            ],
            [
                'name' => 'Trang sức & Đồng hồ',
            ],
            [
                'name' => 'Đồ chơi công nghệ',
            ],
            [
                'name' => 'Điện thoại',
            ],
            [
                'name' => 'Mẹ và bé',
            ],
            [
                'name' => 'Thể thao & ngoài trời',
            ],
            [
                'name' => 'Sách & Văn phòng',
            ],
            [
                'name' => 'Ô tô & Xe máy',
            ],
            [
                'name' => 'Cải tiến nhà cửa',
            ],
        ];

        $count = 1;
        foreach ($translations as $translation) {

            $translation['lang_code'] = 'vi';
            $translation['ec_product_categories_id'] = $count;

            DB::table('ec_product_categories_translations')->insert(Arr::except($translation, ['children']));

            $count++;

            if (isset($translation['children'])) {
                foreach ($translation['children'] as $child) {

                    $child['lang_code'] = 'vi';
                    $child['ec_product_categories_id'] = $count;

                    DB::table('ec_product_categories_translations')->insert(Arr::except($child, ['children']));

                    $count++;

                    if (isset($child['children'])) {
                        foreach ($child['children'] as $item) {

                            $item['lang_code'] = 'vi';
                            $item['ec_product_categories_id'] = $count;

                            DB::table('ec_product_categories_translations')->insert(Arr::except($item, ['children']));

                            $count++;
                        }
                    }
                }
            }
        }
    }

    /**
     * @param int $index
     * @param array $category
     * @param int $parentId
     */
    protected function createCategoryItem(int $index, array $category, int $parentId = 0): void
    {
        $category['parent_id'] = $parentId;
        $category['order'] = $index;

        if (Arr::has($category, 'children')) {
            $children = $category['children'];
            unset($category['children']);
        } else {
            $children = [];
        }

        $createdCategory = ProductCategory::create(Arr::except($category, ['icon']));

        Slug::create([
            'reference_type' => ProductCategory::class,
            'reference_id'   => $createdCategory->id,
            'key'            => Str::slug($createdCategory->name),
            'prefix'         => SlugHelper::getPrefix(ProductCategory::class),
        ]);

        if (isset($category['icon'])) {
            MetaBox::saveMetaBoxData($createdCategory, 'icon', $category['icon']);
        }

        if ($children) {
            foreach ($children as $childIndex => $child) {
                $this->createCategoryItem($childIndex, $child, $createdCategory->id);
            }
        }
    }
}
