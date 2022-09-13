<?php

namespace Database\Seeders;

use Botble\Ads\Models\Ads;
use Botble\Base\Supports\BaseSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdsSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->uploadFiles('promotion');

        Ads::truncate();

        $items = [
            [
                'name'     => 'Top Slider Image 1',
                'location' => 'not_set',
                'key'      => 'VC2C8Q1UGCBG',
            ],
            [
                'name'     => 'Top Slider Image 2',
                'location' => 'not_set',
                'key'      => 'NBDWRXTSVZ8N',
            ],
            [
                'name'     => 'Homepage middle 1',
                'location' => 'not_set',
                'key'      => 'IZ6WU8KUALYD',
            ],
            [
                'name'     => 'Homepage middle 2',
                'location' => 'not_set',
                'key'      => 'ILSFJVYFGCPZ',
            ],
            [
                'name'     => 'Homepage middle 3',
                'location' => 'not_set',
                'key'      => 'ZDOZUZZIU7FT',
            ],
            [
                'name'     => 'Homepage big 1',
                'location' => 'not_set',
                'key'      => 'Q9YDUIC9HSWS',
            ],
            [
                'name'     => 'Homepage bottom small',
                'location' => 'not_set',
            ],
            [
                'name'     => 'Product sidebar',
                'location' => 'product-sidebar',
            ],
            [
                'name'     => 'Homepage big 2',
                'location' => 'not_set',
                'key'      => 'IZ6WU8KUALYE',
            ],
        ];

        foreach ($items as $index => $item) {
            $item['order'] = $index + 1;
            if (!isset($item['key'])) {
                $item['key'] = strtoupper(Str::random(12));
            }
            $item['expired_at'] = now()->addYears(5)->toDateString();
            $item['image'] = 'promotion/' . ($index + 1) . '.jpg';
            $item['url'] = '/products';

            Ads::create($item);
        }

        DB::table('ads_translations')->truncate();

        $translations = [
            [
                'name' => 'Top Slider Image 1',
            ],
            [
                'name' => 'Top Slider Image 2',
            ],
            [
                'name' => 'Homepage middle 1',
            ],
            [
                'name' => 'Homepage middle 2',
            ],
            [
                'name' => 'Homepage middle 3',
            ],
            [
                'name' => 'Homepage big 1',
            ],
            [
                'name' => 'Homepage bottom small',
            ],
            [
                'name' => 'Product sidebar',
            ],
            [
                'name' => 'Homepage big 2',
            ],
        ];

        foreach ($translations as $index => $item) {
            $item['lang_code'] = 'vi';
            $item['ads_id'] = $index + 1;
            $item['image'] = 'promotion/' . ($index + 1) . '.jpg';
            $item['url'] = '/vi/products';

            DB::table('ads_translations')->insert($item);
        }
    }
}
