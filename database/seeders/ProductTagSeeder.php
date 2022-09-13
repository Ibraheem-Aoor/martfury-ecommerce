<?php

namespace Database\Seeders;

use Botble\Ecommerce\Models\ProductTag;
use Botble\Slug\Models\Slug;
use Botble\Base\Supports\BaseSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use SlugHelper;

class ProductTagSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tags = [
            [
                'name' => 'Electronic',
            ],
            [
                'name' => 'Mobile',
            ],
            [
                'name' => 'Iphone',
            ],
            [
                'name' => 'Printer',
            ],
            [
                'name' => 'Office',
            ],
            [
                'name' => 'IT',
            ],
        ];

        ProductTag::truncate();
        Slug::where('reference_type', ProductTag::class)->delete();

        foreach ($tags as $item) {
            $tag = ProductTag::create($item);

            Slug::create([
                'reference_type' => ProductTag::class,
                'reference_id'   => $tag->id,
                'key'            => Str::slug($tag->name),
                'prefix'         => SlugHelper::getPrefix(ProductTag::class),
            ]);
        }

        DB::table('ec_product_tags_translations')->truncate();

        $translations = [
            [
                'name' => 'Electronic',
            ],
            [
                'name' => 'Mobile',
            ],
            [
                'name' => 'Iphone',
            ],
            [
                'name' => 'Printer',
            ],
            [
                'name' => 'Office',
            ],
            [
                'name' => 'IT',
            ],
        ];

        foreach ($translations as $index => $item) {
            $item['lang_code'] = 'vi';
            $item['ec_product_tags_id'] = $index + 1;

            DB::table('ec_product_tags_translations')->insert($item);
        }
    }
}
