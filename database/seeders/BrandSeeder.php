<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\Ecommerce\Models\Brand;
use Botble\Slug\Models\Slug;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use SlugHelper;

class BrandSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->uploadFiles('brands');

        $brands = [
            [
                'name' => 'Fashion live',
            ],
            [
                'name' => 'Hand crafted',
            ],
            [
                'name' => 'Mestonix',
            ],
            [
                'name' => 'Sunshine',
            ],
            [
                'name' => 'Pure',
            ],
            [
                'name' => 'Anfold',
            ],
            [
                'name' => 'Automotive',
            ],
        ];

        Brand::truncate();
        Slug::where('reference_type', Brand::class)->delete();

        foreach ($brands as $key => $item) {
            $item['order'] = $key;
            $item['is_featured'] = true;
            $item['logo'] = 'brands/' . ($key + 1) . '.jpg';
            $brand = Brand::create($item);

            Slug::create([
                'reference_type' => Brand::class,
                'reference_id'   => $brand->id,
                'key'            => Str::slug($brand->name),
                'prefix'         => SlugHelper::getPrefix(Brand::class),
            ]);
        }

        DB::table('ec_brands_translations')->truncate();

        $translations = [
            [
                'name' => 'Fashion live',
            ],
            [
                'name' => 'Hand crafted',
            ],
            [
                'name' => 'Mestonix',
            ],
            [
                'name' => 'Sunshine',
            ],
            [
                'name' => 'Pure',
            ],
            [
                'name' => 'Anfold',
            ],
            [
                'name' => 'Automotive',
            ],
        ];

        foreach ($translations as $index => $item) {
            $item['lang_code'] = 'vi';
            $item['ec_brands_id'] = $index + 1;

            DB::table('ec_brands_translations')->insert($item);
        }
    }
}
