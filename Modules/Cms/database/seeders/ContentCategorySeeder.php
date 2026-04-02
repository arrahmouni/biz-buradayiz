<?php

namespace Modules\Cms\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Cms\Models\ContentCategory;
use Modules\Cms\Models\ContentCategoryTranslation;

class ContentCategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'translations'  => [
                    'en'        => ['title' => 'Electronics', 'description' => 'Devices and gadgets'],
                    'ar'        => ['title' => 'إلكترونيات' , 'description' => 'الأجهزة والملحقات']
                ],
                'subcategories' => [
                    [
                        'translations'  => [
                            'en'        => ['title' => 'Mobile Phones', 'description' => 'Smartphones and accessories'],
                            'ar'        => ['title' => 'هواتف محمولة', 'description' => 'الهواتف الذكية والملحقات']
                        ],
                        'subcategories' => [
                            [
                                'translations'  => [
                                    'en'        => ['title' => 'Android Phones', 'description' => 'Phones with Android OS'],
                                    'ar'        => ['title' => 'هواتف أندرويد', 'description' => 'هواتف بنظام أندرويد']
                                ]
                            ],
                            [
                                'translations'  => [
                                    'en'        => ['title' => 'iOS Phones', 'description' => 'Phones with iOS'],
                                    'ar'        => ['title' => 'هواتف آيفون', 'description' => 'هواتف بنظام آي أو إس']
                                ]
                            ]
                        ]
                    ],
                    [
                        'translations'          => [
                            'en'                => ['title' => 'Laptops', 'description' => 'Portable computers'],
                            'ar'                => ['title' => 'أجهزة لابتوب', 'description' => 'أجهزة الكمبيوتر المحمولة']
                        ],
                        'subcategories'         => [
                            [
                                'translations'  => [
                                    'en'        => ['title' => 'Gaming Laptops', 'description' => 'High-performance laptops for gaming'],
                                    'ar'        => ['title' => 'لابتوبات الألعاب', 'description' => 'لابتوبات عالية الأداء للألعاب']
                                ]
                            ],
                            [
                                'translations'  => [
                                    'en'        => ['title' => 'Ultrabooks', 'description' => 'Lightweight and powerful laptops'],
                                    'ar'        => ['title' => 'لابتوبات فائقة النحافة', 'description' => 'لابتوبات خفيفة وقوية']
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                'translations'                  => [
                    'en'                        => ['title' => 'Clothing', 'description' => 'Apparel and fashion'],
                    'ar'                        => ['title' => 'ملابس', 'description' => 'الأزياء والملابس']
                ],
                'subcategories'                 => [
                    [
                        'translations'          => [
                            'en'                => ['title' => "Men's Wear", 'description' => 'Clothing for men'],
                            'ar'                => ['title' => 'ملابس رجالية', 'description' => 'ملابس للرجال']
                        ],
                        'subcategories'         => [
                            [
                                'translations'  => [
                                    'en'        => ['title' => 'Formal Wear', 'description' => 'Suits and formal attire'],
                                    'ar'        => ['title' => 'ملابس رسمية', 'description' => 'بدلات وملابس رسمية']
                                ]
                            ],
                            [
                                'translations'  => [
                                    'en'        => ['title' => 'Casual Wear', 'description' => 'Everyday casual clothing'],
                                    'ar'        => ['title' => 'ملابس كاجوال', 'description' => 'ملابس يومية كاجوال']
                                ]
                            ]
                        ]
                    ],
                    [
                        'translations'          => [
                            'en'                => ['title' => "Women's Wear", 'description' => 'Clothing for women'],
                            'ar'                => ['title' => 'ملابس نسائية', 'description' => 'ملابس للنساء']
                        ],
                        'subcategories'         => [
                            [
                                'translations'  => [
                                    'en'        => ['title' => 'Dresses', 'description' => 'Elegant and casual dresses'],
                                    'ar'        => ['title' => 'فساتين', 'description' => 'فساتين أنيقة وكاجوال']
                                ]
                            ],
                            [
                                'translations'  => [
                                    'en'        => ['title' => 'Sportswear', 'description' => 'Clothing for sports activities'],
                                    'ar'        => ['title' => 'ملابس رياضية', 'description' => 'ملابس للأنشطة الرياضية']
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                'translations'                  => [
                    'en'                        => ['title' => 'Home Appliances', 'description' => 'Appliances for home use'],
                    'ar'                        => ['title' => 'أجهزة منزلية', 'description' => 'أجهزة للاستخدام المنزلي']
                ],
                'subcategories'                 => [
                    [
                        'translations'          => [
                            'en'                => ['title' => 'Kitchen Appliances', 'description' => 'Appliances for cooking and kitchen use'],
                            'ar'                => ['title' => 'أجهزة المطبخ', 'description' => 'أجهزة للطبخ والاستخدام في المطبخ']
                        ],
                        'subcategories'         => [
                            [
                                'translations'  => [
                                    'en'        => ['title' => 'Microwaves', 'description' => 'Microwave ovens'],
                                    'ar'        => ['title' => 'أفران ميكروويف', 'description' => 'أفران الميكروويف']
                                ]
                            ],
                            [
                                'translations'  => [
                                    'en'        => ['title' => 'Blenders', 'description' => 'Blenders and mixers'],
                                    'ar'        => ['title' => 'خلاطات', 'description' => 'الخلاطات والمزج']
                                ]
                            ]
                        ]
                    ],
                    [
                        'translations'          => [
                            'en'                => ['title' => 'Cleaning Appliances', 'description' => 'Appliances for cleaning tasks'],
                            'ar'                => ['title' => 'أجهزة التنظيف', 'description' => 'أجهزة لأعمال التنظيف']
                        ],
                        'subcategories'         => [
                            [
                                'translations'  => [
                                    'en'        => ['title' => 'Vacuum Cleaners', 'description' => 'Electric vacuum cleaners'],
                                    'ar'        => ['title' => 'مكانس كهربائية', 'description' => 'مكانس كهربائية']
                                ]
                            ],
                            [
                                'translations'  => [
                                    'en'        => ['title' => 'Steam Cleaners', 'description' => 'Steam cleaning devices'],
                                    'ar'        => ['title' => 'أجهزة التنظيف بالبخار', 'description' => 'أجهزة التنظيف باستخدام البخار']
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        foreach ($categories as $category) {
            $parent = ContentCategory::create([
                'parent_id' => null,
                'can_be_deleted' => true,
            ]);

            foreach ($category['translations'] as $locale => $translation) {
                ContentCategoryTranslation::create([
                    'content_category_id' => $parent->id,
                    'locale' => $locale,
                    'title' => $translation['title'],
                ]);
            }

            if (!empty($category['subcategories'])) {
                foreach ($category['subcategories'] as $subcategory) {
                    $this->createSubcategory($subcategory, $parent->id);
                }
            }
        }
    }

    private function createSubcategory(array $subcategory, int $parentId)
    {
        $child = ContentCategory::create([
            'parent_id' => $parentId,
            'can_be_deleted' => true,
        ]);

        foreach ($subcategory['translations'] as $locale => $translation) {
            ContentCategoryTranslation::create([
                'content_category_id' => $child->id,
                'locale' => $locale,
                'title' => $translation['title'],
            ]);
        }

        if (!empty($subcategory['subcategories'])) {
            foreach ($subcategory['subcategories'] as $childSubcategory) {
                $this->createSubcategory($childSubcategory, $child->id);
            }
        }
    }
}
