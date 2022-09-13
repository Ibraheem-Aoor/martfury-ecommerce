<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\Language\Models\LanguageMeta;
use Botble\Setting\Models\Setting;
use Botble\SimpleSlider\Models\SimpleSlider;
use Botble\SimpleSlider\Models\SimpleSliderItem;

class SimpleSliderSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->uploadFiles('sliders');

        SimpleSlider::truncate();
        SimpleSliderItem::truncate();
        LanguageMeta::where('reference_type', SimpleSlider::class)->delete();

        $sliders = [
            'en_US' => [
                [
                    'name'        => 'Home slider',
                    'key'         => 'home-slider',
                    'description' => 'The main slider on homepage',
                ]
            ],
            'vi'    => [
                [
                    'name' => 'Slider trang chủ',
                    'key'  => 'slider-trang-chu',
                    'description' => 'Slider chính trên trang chủ',
                ],
            ],
        ];

        $sliderItems = [
            'en_US' => [
                [
                    'title' => 'Slider 1',
                ],
                [
                    'title' => 'Slider 2',
                ],
                [
                    'title' => 'Slider 3',
                ],
            ],
            'vi'    => [
                [
                    'title' => 'Slider 1',
                ],
                [
                    'title' => 'Slider 2',
                ],
                [
                    'title' => 'Slider 3',
                ],
            ],
        ];

        foreach ($sliders as $locale => $sliderItem) {
            foreach ($sliderItem as $index => $value) {

                $slider = SimpleSlider::create($value);

                $originValue = null;

                if ($locale !== 'en_US') {
                    $originValue = LanguageMeta::where([
                        'reference_id'   => $index + 1,
                        'reference_type' => SimpleSlider::class,
                    ])->value('lang_meta_origin');
                }

                LanguageMeta::saveMetaData($slider, $locale, $originValue);

                foreach ($sliderItems[$locale] as $key => $item) {
                    $item['link'] = '/products';
                    $item['image'] = 'sliders/' . ($key + 1) . '.jpg';
                    $item['order'] = $key + 1;
                    $item['simple_slider_id'] = $slider->id;

                    SimpleSliderItem::create($item);
                }
            }
        }

        Setting::insertOrIgnore([
            [
                'key'   => 'simple_slider_using_assets',
                'value' => 0,
            ],
        ]);
    }
}

