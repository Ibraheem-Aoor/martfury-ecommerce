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
        $parent_categories =     [ "Fashion",
        "health and beauty",
        "Baby Care Products",
        "Mobiles and Tablets",
        "Home and Office",
        "electronics",
        "Computer",
        "Sporting Goods",
        "Toys",
        "Automobile and Motorcycle Supplies",
        "Other Sections",];

        $new_categories = [
                [ "name" => 'Supermarket' , 'children' => [
                    ["name" => "Food Cupboard",
                        "children" => ["Grains & Rice",
                        "Pasta & Noodles",
                        "Cooking Oil",
                        "Vinegar",
                        "Sauce & Dressings",
                        "Sugars & Sweeteners",
                        "Flour",
                        "Herbs & Spices",]],
                        ["name" => "Beverages",
                        "children" => ["Juices",
                        "Soft Drinks",
                        "Coffee, Tea & Cocoa",
                        "Water",
                        "Powdered Drink Mixes & Flavorings",]],
                        ["name" => "Canned, Jarred & Packaged Foods",
                        "children" => ["Antipasto",
                        "Beans & Peas",
                        "Vegetables",
                        "Meat, Poultry & Seafood",]],
                        ["name" => "Breakfast Foods",
                        "children" => ["Cereal",
                        "Breakfast Biscuits & Cookies",
                        "Jams, Jellies & Sweet Spreads",
                        "Candy & Chocolate",
                        "Crisps & Chips",
                        "Nuts & Seeds",]],
                        ["name" => "Pet Supplies",
                        "children" => ["Dogs Supplies",
                        "Cats Supplies",]],
                        ["name" => "Laundry",
                        "children" => ["Liquid Detergent",
                        "Powder Detergent",
                        "Fabric Softener",
                        "Stain Removal",
                        "Bleach",]],
                        ["name" => "Household Cleaning",
                        "children" => ["Dishwashing",
                        "Air Fresheners",
                        "Kitchen Cleaners",
                        "Bathroom Cleaners",
                        "Floor Cleaners",
                        "Glass Cleaners",
                        "Disinfectants",
                        "Cleaning Tools",
                        "Paper & Plastic",]]
                ] ],

                ['name' => 'Fashion' , 'children' => [
                    ["name" => "Women\'s Fashion",
                            "children" => ["Blouses",
                            "Shirts",
                            "Dresses",
                            "Skirts",
                            "Pants",
                            "Jeans",
                            "Swimsuits",
                            "Slippers",
                            "Sneakers",
                            "Flats & Ballerinas",
                            "Heels",
                            "Jumpsuits",
                            "Sleepwear",
                            "Sunglasses",
                            "Bags",
                            "Jewelry",
                            "Watches",]],
                            ["name" => "Men\'s Fashion",
                            "children" => ["T-Shirts",
                            "Polos",
                            "Shirts",
                            "Pants",
                            "Jeans",
                            "Shorts",
                            "Underwear",
                            "Swimsuits",
                            "Sportswear",
                            "Sneakers",
                            "Loafers",
                            "Slippers",
                            "Sandals",
                            "Jewelry",
                            "Watches",
                            "Belts",
                            "Sunglasses",]],
                            ["name" => "Baby",
                            "children" => ["Baby Boys",
                            "Baby Girls",]],
                            ["name" => "Kid\'s Fashion",
                            "children" => ["Boys Fashion",
                            "Girls Fashion",]],
                            ["name" => "Top Brands",
                            "children" => ["American Eagle",
                            "DeFacto",
                            "Reebok",
                            "Adidas",
                            "LC Waikiki",]]
                        ] ],

                [ 'name' => 'Health & Beauty' , 'children' => [
                        ["name" => "Beauty & Personal Care",
                    "children" => ["Skin Care",
                    "Feminine Care",
                    "Shave & Hair Removal",]],
                    ["name" => "Hair Care",
                    "children" => ["Styling Tools & Appliances",
                    "Styling Products",
                    "Shampoo",]],
                    ["name" => "Fragrance",
                    "children" => ["Women\'s",
                    "Men\'s",]],
                    ["name" => "Makeup",
                    "children" => ["Foundation",
                    "Powder",
                    "Concealers & Neutralizers",
                    "Lipstick",
                    "Lip Liners",
                    "Lip Glosses",
                    "Mascara",
                    "Eyeliner",
                    "Eyeshadow",]],
                    ["name" => "Health Care",
                    "children" => ["Wellness & Relaxation",
                    "Sexual Wellness",
                    "Medical Supplies & Equipment",]],
                    ["name" => "Top Brands",
                    "children" => ["Braun",
                    "L\'oreal",
                    "Durex ",
                    "Maybelline",
                    "Veet",
                    "The Body Shop",
                    "Nivea",
                    "P&G",
                    "Johnson\'s",
                    "GSK",]]
                ] ],

                [ 'name' => 'Baby Products' , 'children' =>  [
                    ["name" => "Diapering",
                "children" => ["Diapers",
                "Baby Wipes",
                "Diaper bags",]],
                ["name" => "Baby Feeding",
                "children" => ["Bottle Feeding",
                "Breast feeding",
                "Baby Food",]],
                ["name" => "Bath & Skin Care",
                "children" => ["Baby Creams & Lotions",
                "Baby Shampoo",
                "Baby Soaps",
                "Baby Conditioners",]],
                ["name" => "Baby Safety",
                "children" => ["Rails & Rail Guards",
                "Kitchen Safety",
                "Monitors",]],
                ["name" => "Strollers & Accessories",
                "children" => ["Strollers Accessories",
                "Strollers",]],
                ["name" => "Gear",
                "children" => ["Swings, Jumpers & Bouncers",
                "Backpacks & Carriers",]],
                ["name" => "Nursery",
                "children" => ["Beds, Cribs & Bedding",
                "Nursery Decor",]],
                ["name" => "Baby & Toddler Toys",
                "children" => ["Toy Gift Sets",
                "Blocks",]],
                ["name" => "Toys & Games",
                "children" => ["Dolls & Accessories",
                "Learning & Education",
                "Action Figures & Statues",
                "Arts & Crafts",
                "Dress Up & Pretend Play",
                "Puzzles",
                "Toy Remote Control & Play Vehicles",]]
                ] ],

                [ 'name' => 'Phones & Tablets' , 'children' => [
                        ["name" => "Mobile Phones",
                    "children" => ["Smartphones",
                    "Cell Phones",]],
                    ["name" => "Tablets",
                    "children" => ["iPad Tablets",
                    "Tablet Accessories",
                    "Bags & Cases",]],
                    ["name" => "Mobile Accessories",
                    "children" => ["Phone Cases",
                    "Screen Protectors",
                    "Bluetooth Headsets",
                    "Corded Headsets",
                    "Cables",
                    "Portable Power Banks",
                    "Smart Watches",
                    "Memory Cards",
                    "Chargers",
                    "Car Accessories",
                    "Mounts & Stands",
                    "Selfie Sticks & Tripods",]],
                    ["name" => "Top Brands",
                    "children" => ["Realme",
                    "Samsung",
                    "Huawei",
                    "Xiaomi",
                    "Lenovo",
                    "Tecno",
                    "Infinix",]]
                    ]
                ],
                [  'name' => 'Home & Kitchen' , 'children' => [
                    ["name" => "Home & Kitchen",
                "children" => ["Bedding",
                "Bath",
                " Storage & Organization",
                "Kitchen & Dining",
                "Furniture",
                "Home Decor",
                "Lighting",]],
                ["name" => "Tools & Home Improvement",
                "children" => ["Building Supplies",
                "Electrical",
                "Hardware",
                "Light Bulbs",
                "Power & Hand Tools",
                "Painting Supplies & Wall Treatments",]],
                ["name" => "Office Products",
                "children" => ["Office Electronics",
                "Office Furniture & Lighting",]],
                ["name" => "Small Appliances",
                "children" => ["Blenders",
                "Mixers",
                "Ovens & Toasters",
                "Microwave Ovens",
                "Food Processors",
                "Deep Fryers",
                "Juicers",
                "Coffee, Tea & Espresso Appliances",]],
                ["name" => "Heating, Cooling & Air Quality",
                "children" => ["Air Conditioners",
                "Household Fans",
                "Space Heaters",]],
                ["name" => "Appliances",
                "children" => ["Dishwashers",
                "Freezers",
                "Refrigerators",
                "Washers & Dryers",]],
                ["name" => "Cooking Appliances",
                "children" => ["Cookers",
                "Cook Top",]]
                    ]
                ],
                [ 'name' => 'Electronics' , 'children' => ["name" => "Television & Video",
                        "children" => ["LED & LCD TVs",
                        "Receiver",
                        "Streaming Media Players",]],
                        ["name" => "Cameras",
                        "children" => ["Digital Cameras",
                        "Wearable & Action cameras",]],
                        ["name" => "Home Audio",
                        "children" => ["Home Theater Systems",
                        "Speakers",
                        "Portable Speakers & Docks",]],
                        ["name" => "Headphones",
                        "children" => ["Over-Ear Headphones",
                        "Earbud Headphones",
                        "On-Ear Headphones",]
                        ]
                ],

                [
                    'name' => 'computing' , 'children' => [["name" => "Laptops",
                    "children" => ["2 in 1 Laptops",
                    "Gaming Laptops",
                    "Traditional Laptops",
                    "Macbooks",]],
                    ["name" => "Data Storage",
                    "children" => ["USB Flash Drives",
                    "External Hard Drives",]],
                    ["name" => "Computers & Accessories",
                    "children" => ["Laptop Accessories",
                    "Desktops",
                    "Monitors",
                    "Printers",
                    "Scanners",]],
                    ["name" => "Computer Components",
                    "children" => ["Internal Hard Drives",
                    "Graphics Cards",
                    "Fans & Cooling",]],
                    ["name" => "Computer Accessories",
                    "children" => ["Audio & Video Accessories",
                    "Computer Cable Adapters",
                    "Keyboards, Mice & Accessories",
                    "Printer Ink & Toner",
                    "USP Gadgets",]],
                    ["name" => "Networking Products",
                    "children" => ["Routers",
                    "Wireless Access Points",]],
                    ["name" => "Top Brands",
                    "children" => ["HP",
                    "Lenovo",
                    "Dell",
                    "Apple",]]]
            ],
            [ 'name' => 'Sporting Goods' , 'children' =>  ["name" => "Cardio Training",
                "children" => ["Treadmills",
                "Exercise Bike",
                "Elliptical Trainers",]],
                ["name" => "Strength Training Equipment",
                "children" => ["Dumbbells",
                "Bars",
                "Core & Abdominal Trainers",]],
                ["name" => "Accessories",
                "children" => ["Exercise Bands",
                "Jump Ropes",
                "Exercise Mats",]],
                ["name" => "Sports & Fitness",
                "children" => ["Accessories",
                "Swimming",
                "Team Sports",]],
                ["name" => "Outdoor & Adventure",
                "children" => ["Cycling",
                "Running",]]
            ],
            [
                'name' => 'Gaming'  , 'children' => [["name" => "PlayStation 5",
                "children" => ["Consoles",
                "Games",
                "Controllers",
                "Cards",
                "Accessories",]],
                ["name" => "PlayStation 4",
                "children" => ["PS4",
                "PS4 Games",
                "PS4 Controllers",
                "Cards",
                "Accessories",
                "PS4 Cases",]],
                ["name" => "Xbox",
                "children" => ["Games",
                "Controllers",
                "Accessories",
                "Nintendo Switch",]],
                ["name" => "PC Gaming",
                "children" => ["Gaming Laptops",
                "Headsets",
                "Keyboards",
                "Mouse",
                "Gaming Chairs",
                "Monitors",]
                ],
                ]
            ],
            [
                'name' => 'Automobile' , 'children' => ["name" => "Car Care",
                "children" => ["Cleaning Kits",
                "Exterior Care",
                " Interior Care",
                "Finishing",
                "Glass Care",]],
                ["name" => "Car Electronics & Accessories",
                "children" => ["Car Electronics",
                "Car Electronics Accessories",]],
                ["name" => "Lights & Lighting Accessories",
                "children" => ["Light Covers",
                "Bulbs",
                "Accent & Off Road Lighting",]],
                ["name" => "Oils & Fluids",
                "children" => ["Brake Fluids",
                "Flushes",
                "Greases & Lubricants",
                "Oils",]],
                ["name" => "Exterior Accessories",
                "children" => ["Car Covers",
                "Mirrors",
                "Bumper Stickers, Decals & Magnets",]],
                ["name" => "Interior Accessories",
                "children" => ["Air Fresheners",
                "Consoles & Organizers",
                "Covers",
                "Cup Holders",
                "Mirrors",
                "Key Chains",
                "Floor Mats & Cargo Liners",
                "Sun Protection",
                "Seat Covers & Accessories",]],
            ],
            ['name' => 'Other Categories' , 'children' => ["name" => "Garden & Outdoors",
            "children" => ["Outdoor Decor",
            "Outdoor Furniture & Accessories",
            "Grills & Outdoor Cooking",
            "Gardening & Lawn Care",
            "Watering Equipment",
            "Farm & Ranch",]],
            ["name" => "Books, Movies and Music",
            "children" => ["Art & Humanities",
            "Bestselling Books",
            "Biography & Autobiography Books",
            "Business & Finance Books",
            "Education & Learning",
            "Entertainment Books",
            "Family & Lifestyle Books",
            "Fiction Books",
            "Journals & Planners",
            "Magazines",
            "Motivational & Self-Help Books",
            "Religion Books",
            "Science & Technology Books",]],
            ["name" => "Hand Crafted ",
            "children" => ["Women Accessories",
            "Baby Products",
            "Bags",
            "Bedding",
            "Home Décor",
            "Jewelry & Accessories",]],
            ["name" => "Industrial & Scientific",
            "children" => []],
            ["name" => "Pet Supplies",
            "children" => ["Dogs",
            "Cats",
            "Birds",]],
        ],
];


    ProductCategory::truncate();
    //parent categories
    foreach($new_categories as $category)
    {
        $parent = ProductCategory::create(['name' => $category['name']]);
        $this->createChildren($parent->id, $category);
    }




    }


    public function createChildren($parent_id , $category)
    {
        //child and sub child categories
        if(isset($category['children']) &&  @$category['children'] != null)
        {
            foreach($category['children'] as $child)
            {
                $new_child = ProductCategory::create(['name' => $child[0] , 'parent_id' => $parent_id]);
                if(isset($child['children']) && @$child['children'] != null)
                {
                    $this->createChildren($new_child->id , $child);
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


