<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · Monitoring Ternak</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}" sizes="any">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-canvas font-sans text-ink antialiased">
    {{-- Overlay menu seluler --}}
    <button type="button" data-sidebar-overlay hidden aria-label="Tutup menu" class="fixed inset-0 z-[29] block w-full border-0 bg-[#051423]/55 p-0 md:hidden"></button>

    @include('partials.sidebar')

    <main class="min-h-screen md:ml-[220px] lg:ml-[250px]">
        @include('partials.topbar')

        <div class="px-3.5 pt-4 md:px-7 md:pt-[25px]">
            @yield('content')

            @include('partials.footer')
        </div>
    </main>

    @stack('scripts')
</body>
</html>
