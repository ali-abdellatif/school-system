<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('school.name') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link href="https://fonts.bunny.net/css?family=cairo:700,800,900|tajawal:400,500,700,800,900&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-surface font-sans text-slate-800 antialiased">
    <div class="flex min-h-screen flex-col items-center justify-center px-4 py-10 sm:px-6">
        <a href="/" class="mb-8 flex items-center gap-3 focus:outline-none focus:ring-2 focus:ring-brand/30 rounded-xl">
            <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-brand text-white shadow-lg shadow-brand/20">
                <svg class="h-8 w-8" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path d="M10.394 2.08a1 1 0 0 0-.788 0l-7 3a1 1 0 0 0 0 1.84L5 7.946V12a1 1 0 0 0 .553.894l4 2a1 1 0 0 0 .894 0l4-2A1 1 0 0 0 15 12V7.946l2.394-1.026a1 1 0 0 0 0-1.84l-7-3Z"/>
                </svg>
            </span>
            <span>
                <span class="block font-display text-xl font-black text-brand">{{ config('school.name') }}</span>
                <span class="block text-xs font-medium text-muted">{{ config('school.tagline') }}</span>
            </span>
        </a>

        <div class="w-full max-w-md overflow-hidden rounded-[2rem] bg-white p-8 shadow-2xl">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
