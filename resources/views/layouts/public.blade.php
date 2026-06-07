<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $title ?? school('seo.meta_title') ?? 'بوابة القبول الإلكترونية') - {{ school('name') }}</title>
    <meta name="description" content="@yield('meta_description', school('seo.meta_description'))">
    <meta name="keywords" content="@yield('meta_keywords', school('seo.meta_keywords'))">
    <link rel="canonical" href="{{ url()->current() }}">

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ school('name') }}">
    <meta property="og:title" content="@yield('title', $title ?? school('seo.meta_title') ?? 'بوابة القبول الإلكترونية') - {{ school('name') }}">
    <meta property="og:description" content="@yield('meta_description', school('seo.meta_description'))">
    <meta property="og:image" content="{{ school('images.hero') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">

    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link href="https://fonts.bunny.net/css?family=cairo:700,800,900|tajawal:400,500,700,800,900&display=swap" rel="stylesheet">

    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/public.js'])
    @livewireStyles
</head>
<body class="bg-surface font-sans text-slate-800 antialiased">
    <div class="bg-brand-dark text-white">
        <div class="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-2 text-xs sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-blue-100">
                <span>{{ config('school.phone') }}</span>
                <span>{{ config('school.email') }}</span>
                <span>{{ config('school.address') }}</span>
            </div>
            <a href="{{ route('application.status') }}" class="font-bold text-white/90 transition hover:text-accent focus:outline-none focus:ring-2 focus:ring-accent/50 rounded-lg px-1">
                متابعة طلب القبول
            </a>
        </div>
    </div>

    <nav id="main-nav" class="sticky top-0 z-50 border-b border-white/10 bg-white/90 backdrop-blur-xl" aria-label="التنقل الرئيسي">
        <div class="mx-auto max-w-7xl px-4">
            <div class="flex h-20 items-center justify-between">
                <a href="/" class="flex items-center gap-3 focus:outline-none focus:ring-2 focus:ring-brand/30 rounded-xl">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand text-white shadow-lg shadow-brand/20">
                        <svg class="h-7 w-7" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path d="M10.394 2.08a1 1 0 0 0-.788 0l-7 3a1 1 0 0 0 0 1.84L5 7.946V12a1 1 0 0 0 .553.894l4 2a1 1 0 0 0 .894 0l4-2A1 1 0 0 0 15 12V7.946l2.394-1.026a1 1 0 0 0 0-1.84l-7-3Z"/>
                        </svg>
                    </span>
                    <span>
                        <span class="block font-display text-lg font-black text-brand">{{ config('school.name') }}</span>
                        <span class="block text-xs font-medium text-muted">{{ config('school.tagline') }}</span>
                    </span>
                </a>

                <div class="hidden items-center gap-7 text-sm md:flex">
                    <a href="/" @class(['nav-link', 'is-active' => request()->is('/')]) aria-current="{{ request()->is('/') ? 'page' : 'false' }}">الرئيسية</a>
                    <a href="/#programs" class="nav-link">المراحل</a>
                    <a href="/#gallery" class="nav-link">المعرض</a>
                    <a href="/#admission" class="nav-link">القبول</a>
                    <a href="/#faq" class="nav-link">الأسئلة</a>
                    <a href="{{ route('apply') }}" @class(['nav-link', 'is-active' => request()->routeIs('apply')]) aria-current="{{ request()->routeIs('apply') ? 'page' : 'false' }}">تقديم طلب</a>
                    <a href="/#contact" class="nav-link">تواصل معنا</a>
                    <a href="/admin" class="btn-brand px-5 py-3 text-sm">دخول الإدارة</a>
                </div>

                <button id="mobile-menu-toggle" type="button" class="rounded-xl border border-slate-200 p-2 text-brand focus:outline-none focus:ring-2 focus:ring-brand/30 md:hidden" aria-label="فتح القائمة" aria-expanded="false" aria-controls="mobile-menu">
                    <svg data-icon="open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg data-icon="close" class="hidden h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div id="mobile-menu" class="hidden border-t border-slate-100 pb-4 pt-2 md:hidden">
                <div class="grid gap-1 text-sm font-bold text-slate-700">
                    <a href="/" class="rounded-xl px-3 py-3 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-brand/20">الرئيسية</a>
                    <a href="/#programs" class="rounded-xl px-3 py-3 hover:bg-slate-100">المراحل</a>
                    <a href="/#gallery" class="rounded-xl px-3 py-3 hover:bg-slate-100">المعرض</a>
                    <a href="/#admission" class="rounded-xl px-3 py-3 hover:bg-slate-100">القبول</a>
                    <a href="/#faq" class="rounded-xl px-3 py-3 hover:bg-slate-100">الأسئلة</a>
                    <a href="{{ route('apply') }}" class="rounded-xl px-3 py-3 hover:bg-slate-100">تقديم طلب</a>
                    <a href="{{ route('application.status') }}" class="rounded-xl px-3 py-3 hover:bg-slate-100">متابعة الطلب</a>
                    <a href="/admin" class="btn-brand mt-2 px-3 py-3 text-center text-sm">دخول الإدارة</a>
                </div>
            </div>
        </div>
    </nav>

    <main>
        {!! $slot ?? '' !!}@yield('content')
    </main>

    <footer class="bg-brand-dark text-white">
        <div class="mx-auto grid max-w-7xl gap-10 px-4 py-12 md:grid-cols-4">
            <div class="md:col-span-2">
                <div class="mb-4 flex items-center gap-3">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-accent text-brand">
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="M10.394 2.08a1 1 0 0 0-.788 0l-7 3a1 1 0 0 0 0 1.84l7 3a1 1 0 0 0 .788 0l7-3a1 1 0 0 0 0-1.84l-7-3Z"/></svg>
                    </span>
                    <div>
                        <h2 class="font-display text-xl font-black text-accent">{{ config('school.name') }}</h2>
                        <p class="text-sm text-blue-100">{{ config('school.tagline') }}</p>
                    </div>
                </div>
                <p class="max-w-xl text-sm leading-7 text-blue-100">
                    نساعد الطلاب على بناء شخصية متوازنة تجمع بين التفوق الأكاديمي، المهارات العملية، والقيم الإنسانية.
                </p>
            </div>
            <div>
                <h3 class="mb-4 font-display font-black text-accent">روابط سريعة</h3>
                <ul class="space-y-3 text-sm text-blue-100">
                    <li><a href="{{ route('apply') }}" class="transition hover:text-white focus:outline-none focus:ring-2 focus:ring-accent/40 rounded">تقديم طلب قبول</a></li>
                    <li><a href="{{ route('application.status') }}" class="transition hover:text-white">متابعة الطلب</a></li>
                    <li><a href="/#programs" class="transition hover:text-white">المراحل الدراسية</a></li>
                    <li><a href="/admin" class="transition hover:text-white">بوابة الإدارة</a></li>
                </ul>
            </div>
            <div>
                <h3 class="mb-4 font-display font-black text-accent">تواصل معنا</h3>
                <ul class="space-y-3 text-sm text-blue-100">
                    <li>{{ config('school.phone') }}</li>
                    <li>{{ config('school.email') }}</li>
                    <li>{{ config('school.address') }}</li>
                </ul>
            </div>
        </div>
        <div class="border-t border-white/10 py-5 text-center text-xs text-blue-100">
            © {{ now()->year }} {{ config('school.name') }}. جميع الحقوق محفوظة.
        </div>
    </footer>

    <a href="https://wa.me/{{ config('school.whatsapp') }}" target="_blank" rel="noopener"
       aria-label="تواصل عبر واتساب"
       class="wa-float fixed bottom-5 end-5 z-50 flex h-14 w-14 items-center justify-center rounded-full bg-emerald-500 text-white shadow-2xl transition hover:bg-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-300 focus:ring-offset-2">
        <svg class="h-7 w-7" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M20.52 3.48A11.8 11.8 0 0 0 12.08 0C5.5 0 .15 5.35.15 11.93c0 2.1.55 4.16 1.6 5.97L.06 24l6.25-1.64a11.9 11.9 0 0 0 5.77 1.47h.01c6.58 0 11.93-5.35 11.93-11.93 0-3.19-1.24-6.19-3.5-8.42ZM12.09 21.8h-.01a9.9 9.9 0 0 1-5.04-1.38l-.36-.21-3.7.97.99-3.61-.24-.37a9.86 9.86 0 0 1-1.51-5.27c0-5.46 4.44-9.9 9.9-9.9a9.84 9.84 0 0 1 7 2.9 9.84 9.84 0 0 1 2.9 7c0 5.45-4.44 9.89-9.93 9.89Zm5.43-7.4c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.64.07-.3-.15-1.26-.46-2.4-1.48-.89-.79-1.48-1.76-1.66-2.06-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.03-.52-.07-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.79.37-.27.3-1.04 1.02-1.04 2.48 0 1.46 1.07 2.88 1.22 3.08.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.7.63.71.23 1.36.19 1.87.12.57-.09 1.76-.72 2.01-1.41.25-.7.25-1.3.17-1.42-.07-.12-.27-.2-.57-.35Z"/></svg>
    </a>

    @livewireScripts
</body>
</html>
