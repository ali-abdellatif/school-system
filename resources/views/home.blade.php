@extends('layouts.public')

@section('title', 'الرئيسية')
@section('meta_description', 'تعرف على ' . school('name') . '، المراحل الدراسية، خطوات القبول، وخدمات المدرسة الرقمية.')

@section('content')
    <section class="home-hero">
        <img src="{{ school('images.hero') }}" alt="طلاب داخل بيئة مدرسية حديثة" class="absolute inset-0 -z-20 h-full w-full object-cover">
        <div class="absolute inset-0 -z-10 bg-gradient-to-l from-brand-dark/95 via-brand/80 to-brand-light/60"></div>
        <div class="absolute inset-0 -z-10 hero-pattern opacity-20"></div>
        <div class="absolute -start-32 top-20 h-72 w-72 rounded-full bg-accent/10 blur-3xl"></div>
        <div class="absolute -end-20 bottom-10 h-56 w-56 rounded-full bg-brand-light/20 blur-2xl"></div>

        <div class="mx-auto grid min-h-[calc(100vh-112px)] max-w-7xl items-center gap-10 px-4 py-16 lg:grid-cols-[1.08fr_.92fr]">
            <div data-reveal>
                @if (school('admission_open'))
                    <span class="mb-5 inline-flex rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm font-bold text-white/90 backdrop-blur">
                        التقديم للعام الدراسي الجديد مفتوح الآن
                    </span>
                @endif
                <h1 class="max-w-3xl font-display text-4xl font-black leading-tight md:text-6xl">
                    تعليم عصري يصنع الثقة، الفضول، والتميّز.
                </h1>
                <p class="mt-6 max-w-2xl text-lg font-normal leading-8 text-blue-50">
                    في {{ school('name') }} يجد الطالب بيئة آمنة ومحفزة، ومعلمين قريبين منه، وتجربة رقمية تسهّل التواصل بين المدرسة والأسرة.
                </p>
                <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                    <x-button variant="primary" href="{{ route('apply') }}" size="lg">قدّم طلب قبول</x-button>
                    <x-button variant="outline" href="{{ route('application.status') }}" size="lg">تابع حالة الطلب</x-button>
                </div>
            </div>

            <div data-reveal data-reveal-delay="150" class="rounded-[2rem] border border-white/15 bg-white/10 p-4 shadow-2xl backdrop-blur">
                <div class="grid grid-cols-2 gap-3">
                    @foreach (school('stats', []) as $stat)
                        <x-stat-card
                            :count="$stat['count'] ?? null"
                            :suffix="$stat['suffix'] ?? ''"
                            :value="$stat['value'] ?? null"
                            :label="$stat['label']"
                        />
                    @endforeach
                </div>
                <div class="mt-3 rounded-3xl border border-white/20 bg-white/10 p-6 text-white">
                    <div class="text-sm font-black">بوابة القبول الإلكترونية</div>
                    <p class="mt-2 text-sm font-normal leading-7">
                        نموذج واضح، مراجعة منظمة، ومتابعة حالة الطلب من نفس الموقع.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-20">
        <div class="grid gap-10 lg:grid-cols-[.9fr_1.1fr] lg:items-center">
            <div data-reveal>
                <x-section-heading eyebrow="عن المدرسة" title="بيئة تعليمية ترى الطالب كاملا">
                    نؤمن أن المدرسة ليست مكانا للدروس فقط، بل مساحة لاكتشاف قدرات الطالب وبناء شخصيته. لذلك نجمع بين جودة التعليم، الأنشطة، المتابعة النفسية، والتواصل المستمر مع الأسرة.
                </x-section-heading>
                <div class="mt-8 grid gap-4 sm:grid-cols-2">
                    <x-feature-tile icon="01" title="متابعة قريبة">تقارير منتظمة وتواصل واضح مع ولي الأمر.</x-feature-tile>
                    <x-feature-tile icon="02" title="تعلم نشط">أنشطة ومشروعات تساعد الطالب على الفهم والتطبيق.</x-feature-tile>
                </div>
                <div class="mt-8 flex flex-wrap gap-3">
                    @foreach (school('accreditations', []) as $badge)
                        <span class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-brand">
                            {{ is_array($badge) ? ($badge['badge'] ?? reset($badge)) : $badge }}
                        </span>
                    @endforeach
                </div>
            </div>
            <div data-reveal data-reveal-delay="150" class="grid grid-cols-2 gap-4">
                <img src="{{ school('images.classroom') }}" alt="فصل دراسي حديث" class="h-72 w-full rounded-3xl object-cover shadow-xl">
                <img src="{{ school('images.lab') }}" alt="مختبر مدرسي" class="mt-10 h-72 w-full rounded-3xl object-cover shadow-xl">
            </div>
        </div>
    </section>

    <section id="programs" class="bg-white py-20 scroll-mt-24">
        <div class="mx-auto max-w-7xl px-4">
            <div data-reveal>
                <x-section-heading eyebrow="المراحل الدراسية" title="رحلة تعليمية متدرجة وواضحة" :centered="true" />
            </div>
            <div class="mt-12 grid gap-6 md:grid-cols-3">
                @php
                    $programs = [
                        ['title' => 'المراحلة الابتدائية', 'desc' => 'تأسيس قوي في اللغة، الرياضيات، العلوم، والمهارات الاجتماعية.', 'img' => school('images.library')],
                        ['title' => 'المرحلة الإعدادية', 'desc' => 'تنمية التفكير النقدي، العادات الدراسية، والعمل الجماعي.', 'img' => school('images.classroom')],
                        ['title' => 'المرحلة الثانوية', 'desc' => 'إعداد أكاديمي وشخصي للجامعة وسوق العمل.', 'img' => school('images.lab')],
                    ];
                @endphp
                @foreach ($programs as $i => $program)
                    <article data-reveal data-reveal-delay="{{ $i * 100 }}" class="program-card">
                        <img src="{{ $program['img'] }}" alt="{{ $program['title'] }}" class="h-52 w-full object-cover">
                        <div class="p-7">
                            <h3 class="font-display text-xl font-black text-brand">{{ $program['title'] }}</h3>
                            <p class="mt-3 text-sm font-normal leading-7 text-muted">{{ $program['desc'] }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section id="gallery" class="bg-surface py-20 scroll-mt-24">
        <div class="mx-auto max-w-7xl px-4">
            <div data-reveal class="mb-12 text-center">
                <x-section-heading eyebrow="معرض الصور" title="لحظات من حياة المدرسة" :centered="true" />
            </div>
            <x-gallery-grid :items="school('gallery', [])" />
        </div>
    </section>

    <section id="admission" class="mx-auto max-w-7xl px-4 py-20 scroll-mt-24">
        <div class="grid gap-10 lg:grid-cols-[.95fr_1.05fr] lg:items-center">
            <div data-reveal>
                <x-section-heading eyebrow="القبول الإلكتروني" title="التقديم أصبح أسهل وأكثر وضوحا">
                    صممنا رحلة القبول لتكون مريحة لولي الأمر: بيانات الطالب، بيانات ولي الأمر، ثم مراجعة نهائية قبل إرسال الطلب.
                </x-section-heading>
                <x-button variant="brand" href="{{ route('apply') }}" class="mt-8">ابدأ التقديم الآن</x-button>
            </div>
            <div data-reveal data-reveal-delay="150">
                @php
                    $steps = [
                        ['n' => '1', 'title' => 'إدخال بيانات الطالب', 'desc' => 'الاسم، تاريخ الميلاد، الصف المطلوب، والمدرسة السابقة.'],
                        ['n' => '2', 'title' => 'بيانات ولي الأمر', 'desc' => 'وسائل التواصل وصلة القرابة والعنوان.'],
                        ['n' => '3', 'title' => 'المراجعة والمتابعة', 'desc' => 'راجع الطلب، أرسله، ثم تابع حالته برقم الطلب.'],
                    ];
                @endphp
                @foreach ($steps as $step)
                    <div class="flex gap-5">
                        <div class="flex flex-col items-center">
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-brand text-xl font-black text-white shadow-lg shadow-brand/25 ring-4 ring-brand/10">{{ $step['n'] }}</span>
                            @unless ($loop->last)
                                <span class="timeline-connector my-2"></span>
                            @endunless
                        </div>
                        <div class="pt-2.5 {{ $loop->last ? '' : 'pb-8' }}">
                            <h3 class="font-black text-brand">{{ $step['title'] }}</h3>
                            <p class="mt-1 text-sm font-normal leading-7 text-muted">{{ $step['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section id="faq" class="bg-white py-20 scroll-mt-24">
        <div class="mx-auto max-w-4xl px-4">
            <div data-reveal class="mb-12 text-center">
                <x-section-heading eyebrow="الأسئلة الشائعة" title="كل ما تحتاج معرفته عن القبول" :centered="true" />
            </div>
            <x-faq-accordion :items="school('faq', [])" data-reveal />
        </div>
    </section>

    <section class="bg-brand py-20 text-white">
        <div class="mx-auto max-w-7xl px-4">
            <div data-reveal>
                <x-section-heading eyebrow="خدمات المدرسة" title="كل ما تحتاجه الأسرة في مكان واحد" :centered="true" :dark="true" />
            </div>
            <div class="mt-12 grid gap-6 md:grid-cols-4">
                @php
                    $services = [
                        ['title' => 'قبول إلكتروني', 'desc' => 'تقديم ومتابعة الطلبات بسهولة.'],
                        ['title' => 'بوابة ولي الأمر', 'desc' => 'متابعة الحضور والتقارير.'],
                        ['title' => 'أنشطة مدرسية', 'desc' => 'رياضة وفنون ومشروعات.'],
                        ['title' => 'تواصل سريع', 'desc' => 'قنوات واضحة مع الإدارة.'],
                    ];
                @endphp
                @foreach ($services as $service)
                    <div data-reveal class="service-card">
                        <h3 class="font-display text-lg font-black text-white">{{ $service['title'] }}</h3>
                        <p class="mt-3 text-sm font-normal leading-7 text-blue-50">{{ $service['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-20">
        <div class="grid gap-8 lg:grid-cols-3">
            <div data-reveal>
                <x-section-heading eyebrow="آراء أولياء الأمور" title="ثقة تُبنى بالتجربة اليومية" />
            </div>
            @foreach (school('testimonials', []) as $i => $testimonial)
                <div data-reveal data-reveal-delay="{{ $i * 120 }}" class="testimonial-card">
                    <x-star-rating :rating="$testimonial['rating'] ?? 5" class="mb-4" />
                    <p class="font-normal leading-8 text-muted">"{{ $testimonial['quote'] }}"</p>
                    <div class="mt-6 font-black text-brand">{{ $testimonial['name'] }}</div>
                    <div class="text-sm text-muted">{{ $testimonial['role'] }}</div>
                </div>
            @endforeach
        </div>
    </section>

    <section id="contact" class="mx-auto max-w-7xl px-4 pb-20 scroll-mt-24">
        <div class="content-card overflow-hidden">
            <div class="grid lg:grid-cols-2">
                <div class="p-8 md:p-12">
                    <x-section-heading eyebrow="تواصل معنا" title="لديك سؤال عن القبول؟">
                        فريق شؤون الطلاب جاهز لمساعدتك في خطوات التقديم أو متابعة حالة الطلب.
                    </x-section-heading>
                    <div class="mt-8 grid gap-4 text-sm font-medium text-slate-700">
                        <span>الهاتف: {{ school('phone') }}</span>
                        <span>البريد الإلكتروني: {{ school('email') }}</span>
                        <span>العنوان: {{ school('address') }}</span>
                    </div>
                    <x-button variant="brand" href="{{ route('apply') }}" class="mt-8">قدّم طلبك الآن</x-button>
                </div>
                <div class="relative min-h-80">
                    <iframe
                        src="{{ school('map_embed_url') }}"
                        title="موقع {{ school('name') }} على الخريطة"
                        class="absolute inset-0 h-full w-full border-0"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        allowfullscreen>
                    </iframe>
                </div>
            </div>
        </div>
    </section>
@endsection
