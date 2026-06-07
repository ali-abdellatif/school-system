<?php

return [

    'name' => env('SCHOOL_NAME', 'مدرسة النور'),

    'tagline' => env('SCHOOL_TAGLINE', 'تعليم ملهم لمستقبل أفضل'),

    'phone' => env('SCHOOL_PHONE', '01012345678'),

    'whatsapp' => env('SCHOOL_WHATSAPP', '201012345678'),

    'email' => env('SCHOOL_EMAIL', 'info@alnoor-school.edu.eg'),

    'address' => env('SCHOOL_ADDRESS', 'القاهرة، جمهورية مصر العربية'),

    'map_embed_url' => env(
        'SCHOOL_MAP_EMBED_URL',
        'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3453.7!2d31.2357!3d30.0444!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMzDCsDAyJzM5LjgiTiAzMcKwMTQnMDguNSJF!5e0!3m2!1sar!2seg!4v1700000000000'
    ),

    'admission_open' => env('SCHOOL_ADMISSION_OPEN', true),

    // تحسين محركات البحث (إعدادات أساسية)
    'seo' => [
        'meta_title' => env('SCHOOL_META_TITLE', 'مدرسة النور | بوابة القبول الإلكترونية'),
        'meta_description' => env('SCHOOL_META_DESCRIPTION', 'مدرسة النور للتعليم المتميز — قدّم طلب الالتحاق وتابع حالته إلكترونياً بخطوات سهلة وواضحة.'),
        'meta_keywords' => env('SCHOOL_META_KEYWORDS', 'مدرسة النور, قبول إلكتروني, تقديم مدارس, مدرسة خاصة, التحاق, القاهرة'),
    ],

    'images' => [
        'hero' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=1600&auto=format&fit=crop&q=80',
        'classroom' => 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?w=800&auto=format&fit=crop&q=80',
        'lab' => 'https://images.unsplash.com/photo-1532094349884-543419669f78?w=800&auto=format&fit=crop&q=80',
        'library' => 'https://images.unsplash.com/photo-1524995997942-a1c2e315a42f?w=800&auto=format&fit=crop&q=80',
    ],

    'stats' => [
        ['count' => 1200, 'suffix' => '+', 'label' => 'طالب وطالبة'],
        ['count' => 80, 'suffix' => '+', 'label' => 'معلم متخصص'],
        ['count' => 25, 'suffix' => '+', 'label' => 'سنة خبرة'],
        ['count' => 98, 'suffix' => '%', 'label' => 'نسبة نجاح'],
    ],

    'accreditations' => [
        'معتمدة من وزارة التربية والتعليم',
        'عضو الجمعية المصرية للمدارس الخاصة',
        'شريك Cambridge English',
    ],

    'testimonials' => [
        [
            'name' => 'أحمد المصري',
            'role' => 'ولي أمر — الصف الخامس',
            'quote' => 'التواصل مع المدرسة واضح، وابني أصبح أكثر ثقة واهتماما بالتعلم.',
            'rating' => 5,
        ],
        [
            'name' => 'فاطمة عبدالله',
            'role' => 'ولي أمر — الصف الثاني الإعدادي',
            'quote' => 'رحلة التقديم كانت سهلة، والمتابعة بعد القبول منظمة جدا.',
            'rating' => 5,
        ],
    ],

    'gallery' => [
        [
            'title' => 'أنشطة رياضية',
            'category' => 'رياضة',
            'image' => 'https://images.unsplash.com/photo-1622279457486-62dcc4a431d6?w=600&auto=format&fit=crop&q=80',
        ],
        [
            'title' => 'معمل العلوم',
            'category' => 'علوم',
            'image' => 'https://images.unsplash.com/photo-1532094349884-543419669f78?w=600&auto=format&fit=crop&q=80',
        ],
        [
            'title' => 'ورشة فنية',
            'category' => 'فنون',
            'image' => 'https://images.unsplash.com/photo-1513364776144-60967b0f800f?w=600&auto=format&fit=crop&q=80',
        ],
        [
            'title' => 'حفل تكريم',
            'category' => 'فعاليات',
            'image' => 'https://images.unsplash.com/photo-1523580494863-6f3031224c88?w=600&auto=format&fit=crop&q=80',
        ],
        [
            'title' => 'مكتبة المدرسة',
            'category' => 'تعليم',
            'image' => 'https://images.unsplash.com/photo-1524995997942-a1c2e315a42f?w=600&auto=format&fit=crop&q=80',
        ],
        [
            'title' => 'رحلة مدرسية',
            'category' => 'أنشطة',
            'image' => 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=600&auto=format&fit=crop&q=80',
        ],
    ],

    'faq' => [
        [
            'question' => 'ما هي المستندات المطلوبة للتقديم؟',
            'answer' => 'شهادة الميلاد، صور شخصية حديثة، آخر شهادة دراسية، وصورة بطاقة ولي الأمر. قد تُطلب مستندات إضافية حسب الصف المطلوب.',
        ],
        [
            'question' => 'متى يبدأ التقديم للعام الدراسي الجديد؟',
            'answer' => 'يفتح التقديم عادة في بداية شهر مارس ويستمر حتى اكتمال المقاعد. تابع الموقع أو تواصل مع شؤون الطلاب لمعرفة المواعيد الدقيقة.',
        ],
        [
            'question' => 'كيف أتابع حالة طلب القبول؟',
            'answer' => 'بعد إرسال الطلب ستحصل على رقم طلب. استخدمه مع رقم هاتف ولي الأمر في صفحة "متابعة الطلب" على الموقع.',
        ],
        [
            'question' => 'هل يوجد مقابلة شخصية للطالب؟',
            'answer' => 'نعم، قد تُحدد مقابلة أو اختبار تحديد مستوى حسب الصف المطلوب. سيتم التواصل معكم لتحديد الموعد.',
        ],
        [
            'question' => 'ما هي طرق الدفع المتاحة؟',
            'answer' => 'تتوفر عدة خيارات للدفع تشمل التقسيط. يمكنكم الاستفسار من قسم الحسابات بعد قبول الطالب.',
        ],
    ],

];
