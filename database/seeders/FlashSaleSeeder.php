<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\Ecommerce\Models\FlashSale;
use Botble\Ecommerce\Models\Product;
use Illuminate\Support\Facades\DB;

class FlashSaleSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        FlashSale::truncate();
        DB::table('ec_flash_sale_products')->truncate();


        $flashSale = FlashSale::create([
            'name'     => 'Winter Sale',
            'end_date' => now()->addDays(30)->toDateString(),
        ]);

        for ($i = 1; $i <= 10; $i++) {
            $product = Product::where('id', $i)->where('is_variation', 0)->first();

            if (!$product) {
                continue;
            }

            $price = $product->price;

            if ($product->front_sale_price !== $product->price) {
                $price = $product->front_sale_price;
            }

            $flashSale->products()->attach([$i => ['price' => $price - ($price * rand(10, 70) / 100), 'quantity' => rand(6, 20), 'sold' => rand(1, 5)]]);
        }

        DB::table('ec_flash_sales_translations')->truncate();

        $translations = [
            [
                'name' => 'Khuyến mãi mùa đông.',
            ],
        ];

        foreach ($translations as $index => $item) {
            $item['lang_code'] = 'vi';
            $item['ec_flash_sales_id'] = $index + 1;

            DB::table('ec_flash_sales_translations')->insert($item);
        }
    }
}
