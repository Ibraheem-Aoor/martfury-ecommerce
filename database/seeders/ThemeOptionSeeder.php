<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\Setting\Models\Setting as SettingModel;
use Theme;

class ThemeOptionSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->uploadFiles('general');

        $theme = Theme::getThemeName();
        SettingModel::where('key', 'LIKE', 'theme-' . $theme . '-%')->delete();

        SettingModel::insertOrIgnore([
            [
                'key'   => 'theme',
                'value' => $theme,
            ],
            [
                'key'   => 'admin_logo',
                'value' => 'general/logo-light.png',
            ],
            [
                'key'   => 'theme-' . $theme . '-site_title',
                'value' => 'Martfury - Laravel Ecommerce system',
            ],
            [
                'key'   => 'theme-' . $theme . '-copyright',
                'value' => '© ' . now()->format('Y') . ' Martfury. All Rights Reserved.',
            ],
            [
                'key'   => 'theme-' . $theme . '-favicon',
                'value' => 'general/favicon.png',
            ],
            [
                'key'   => 'theme-' . $theme . '-logo',
                'value' => 'general/logo.png',
            ],
            [
                'key'   => 'theme-' . $theme . '-welcome_message',
                'value' => 'Welcome to Martfury Online Shopping Store!',
            ],
            [
                'key'   => 'theme-' . $theme . '-address',
                'value' => '502 New Street, Brighton VIC, Australia',
            ],
            [
                'key'   => 'theme-' . $theme . '-hotline',
                'value' => '1800 97 97 69',
            ],
            [
                'key'   => 'theme-' . $theme . '-email',
                'value' => 'contact@martfury.co',
            ],
            [
                'key'   => 'theme-' . $theme . '-payment_methods',
                'value' => json_encode([
                    'general/payment-method-1.jpg',
                    'general/payment-method-2.jpg',
                    'general/payment-method-3.jpg',
                    'general/payment-method-4.jpg',
                    'general/payment-method-5.jpg',
                ]),
            ],
            [
                'key'   => 'theme-' . $theme . '-newsletter_image',
                'value' => 'general/newsletter.jpg',
            ],
            [
                'key'   => 'theme-' . $theme . '-homepage_id',
                'value' => '1',
            ],
            [
                'key'   => 'theme-' . $theme . '-blog_page_id',
                'value' => '6',
            ],
            [
                'key'   => 'theme-' . $theme . '-cookie_consent_message',
                'value' => 'Your experience on this site will be improved by allowing cookies ',
            ],
            [
                'key'   => 'theme-' . $theme . '-cookie_consent_learn_more_url',
                'value' => url('cookie-policy'),
            ],
            [
                'key'   => 'theme-' . $theme . '-cookie_consent_learn_more_text',
                'value' => 'Cookie Policy',
            ],
            [
                'key'   => 'theme-' . $theme . '-number_of_products_per_page',
                'value' => 40,
            ],
            [
                'key'   => 'theme-' . $theme . '-product_feature_1_title',
                'value' => 'Shipping worldwide',
            ],
            [
                'key'   => 'theme-' . $theme . '-product_feature_1_icon',
                'value' => 'icon-network',
            ],
            [
                'key'   => 'theme-' . $theme . '-product_feature_2_title',
                'value' => 'Free 7-day return if eligible, so easy',
            ],
            [
                'key'   => 'theme-' . $theme . '-product_feature_2_icon',
                'value' => 'icon-3d-rotate',
            ],
            [
                'key'   => 'theme-' . $theme . '-product_feature_3_title',
                'value' => 'Supplier give bills for this product.',
            ],
            [
                'key'   => 'theme-' . $theme . '-product_feature_3_icon',
                'value' => 'icon-receipt',
            ],
            [
                'key'   => 'theme-' . $theme . '-product_feature_4_title',
                'value' => 'Pay online or when receiving goods',
            ],
            [
                'key'   => 'theme-' . $theme . '-product_feature_4_icon',
                'value' => 'icon-credit-card',
            ],

            [
                'key'   => 'theme-' . $theme . '-contact_info_box_1_title',
                'value' => 'Contact Directly',
            ],
            [
                'key'   => 'theme-' . $theme . '-contact_info_box_1_subtitle',
                'value' => 'contact@martfury.com',
            ],
            [
                'key'   => 'theme-' . $theme . '-contact_info_box_1_details',
                'value' => '(+004) 912-3548-07',
            ],

            [
                'key'   => 'theme-' . $theme . '-contact_info_box_2_title',
                'value' => 'Headquarters',
            ],
            [
                'key'   => 'theme-' . $theme . '-contact_info_box_2_subtitle',
                'value' => '17 Queen St, Southbank, Melbourne 10560, Australia',
            ],
            [
                'key'   => 'theme-' . $theme . '-contact_info_box_2_details',
                'value' => '',
            ],

            [
                'key'   => 'theme-' . $theme . '-contact_info_box_3_title',
                'value' => 'Work With Us',
            ],
            [
                'key'   => 'theme-' . $theme . '-contact_info_box_3_subtitle',
                'value' => 'Send your CV to our email:',
            ],
            [
                'key'   => 'theme-' . $theme . '-contact_info_box_3_details',
                'value' => 'career@martfury.com',
            ],

            [
                'key'   => 'theme-' . $theme . '-contact_info_box_4_title',
                'value' => 'Customer Service',
            ],
            [
                'key'   => 'theme-' . $theme . '-contact_info_box_4_subtitle',
                'value' => 'customercare@martfury.com',
            ],
            [
                'key'   => 'theme-' . $theme . '-contact_info_box_4_details',
                'value' => '(800) 843-2446',
            ],

            [
                'key'   => 'theme-' . $theme . '-contact_info_box_5_title',
                'value' => 'Media Relations',
            ],
            [
                'key'   => 'theme-' . $theme . '-contact_info_box_5_subtitle',
                'value' => 'media@martfury.com',
            ],
            [
                'key'   => 'theme-' . $theme . '-contact_info_box_5_details',
                'value' => '(801) 947-3564',
            ],

            [
                'key'   => 'theme-' . $theme . '-contact_info_box_6_title',
                'value' => 'Vendor Support',
            ],
            [
                'key'   => 'theme-' . $theme . '-contact_info_box_6_subtitle',
                'value' => 'vendorsupport@martfury.com',
            ],
            [
                'key'   => 'theme-' . $theme . '-contact_info_box_6_details',
                'value' => '(801) 947-3100',
            ],
            [
                'key'   => 'theme-' . $theme . '-number_of_cross_sale_products_in_cart_page',
                'value' => 7,
            ],
            [
                'key'   => 'theme-' . $theme . '-logo_in_the_checkout_page',
                'value' => 'general/logo-dark.png',
            ],
            [
                'key'   => 'theme-' . $theme . '-logo_in_invoices',
                'value' => 'general/logo-dark.png',
            ],
            [
                'key'   => 'theme-' . $theme . '-logo_vendor_dashboard',
                'value' => 'general/logo-dark.png',
            ],
        ]);

        $socials = [
            [
                'name' => 'Facebook',
                'url'  => 'https://www.facebook.com/',
                'icon' => 'fa-facebook',
                'color' => '#3b5999',
            ],
            [
                'name' => 'Twitter',
                'url'  => 'https://www.twitter.com/',
                'icon' => 'fa-twitter',
                'color' => '#55ACF9',
            ],
            [
                'name' => 'Instagram',
                'url'  => 'https://www.instagram.com/',
                'icon' => 'fa-instagram',
                'color' => '#E1306C',
            ],
            [
                'name' => 'Youtube',
                'url'  => 'https://www.youtube.com/',
                'icon' => 'fa-youtube',
                'color' => '#FF0000',
            ],
        ];

        foreach ($socials as $index => $social) {
            foreach ($social as $key => $value) {
                $item['key'] = 'theme-' . $theme . '-social-' . $key . '-' . ($index + 1);
                $item['value'] = $value;

                SettingModel::create($item);
            }
        }

        SettingModel::insertOrIgnore([
            [
                'key'   => 'theme-' . $theme . '-vi-copyright',
                'value' => '© ' . now()->format('Y') . ' Martfury. Tất cả quyền đã được bảo hộ.',
            ],
            [
                'key'   => 'theme-' . $theme . '-vi-welcome_message',
                'value' => 'Chào mừng đến với Martfury - Cửa Hàng Mua Sắm Online!',
            ],
            [
                'key'   => 'theme-' . $theme . '-vi-homepage_id',
                'value' => '1',
            ],
            [
                'key'   => 'theme-' . $theme . '-vi-blog_page_id',
                'value' => '6',
            ],
            [
                'key'   => 'theme-' . $theme . '-vi-cookie_consent_message',
                'value' => 'Trải nghiệm của bạn trên trang web này sẽ được cải thiện bằng cách cho phép cookie ',
            ],
            [
                'key'   => 'theme-' . $theme . '-vi-cookie_consent_learn_more_url',
                'value' => url('cookie-policy'),
            ],
            [
                'key'   => 'theme-' . $theme . '-vi-cookie_consent_learn_more_text',
                'value' => 'Chính sách cookie',
            ],
            [
                'key'   => 'theme-' . $theme . '-vi-product_feature_1_title',
                'value' => 'Vận chuyển toàn cầu',
            ],
            [
                'key'   => 'theme-' . $theme . '-vi-product_feature_2_title',
                'value' => 'Miễn phí hoàn hàng 7 ngày',
            ],
            [
                'key'   => 'theme-' . $theme . '-vi-product_feature_3_title',
                'value' => 'Nhà cung cấp sẽ cấp hóa đơn cho sản phẩm này',
            ],
            [
                'key'   => 'theme-' . $theme . '-vi-product_feature_4_title',
                'value' => 'Thanh toán online hoặc trực tiếp',
            ],
        ]);
    }
}
