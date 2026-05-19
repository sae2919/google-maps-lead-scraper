<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @php
        $aiData = json_decode($lead->ai_metadata, true);

        $sections = $aiData['content']['sections'] ?? [];

        $images = $aiData['gallery'] ?? $aiData['images'] ?? [];

        // 🔥 STRONG IMAGE FALLBACK
        if (empty($images) || count($images) < 3) {
            $images = [
                "https://images.unsplash.com/photo-1492724441997-5dc865305da7?q=80&w=1200",
                "https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?q=80&w=1200",
                "https://images.unsplash.com/photo-1554995207-c18c203602cb?q=80&w=1200",
                "https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?q=80&w=1200"
            ];
        }

        $images = array_slice($images, 0, 4);
    @endphp

    <title>{{ $lead->name }} | Smart Website</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Inter:wght@400;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #000; color: #fff; }
        .heading-font { font-family: 'Archivo Black', sans-serif; text-transform: uppercase; letter-spacing: -0.05em; }
        .accent-border { border-left: 4px solid #3b82f6; }
        .glass-card { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); }
        html { scroll-behavior: smooth; }
    </style>
</head>

<body id="top">

<nav class="fixed w-full z-50 bg-black/90 border-b border-white/10">
    <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
        <span class="text-2xl heading-font text-blue-500 italic">{{ $lead->name }}</span>

        <a href="tel:{{ $lead->phone }}" class="bg-blue-600 text-white text-xs font-black uppercase px-8 py-3 rounded-full">
            Call Now
        </a>
    </div>
</nav>


{{-- 🔥 FALLBACK HERO IF AI FAILS --}}
@if(empty($sections))
<header class="pt-28 pb-16 px-6 text-center">
    <h1 class="text-5xl heading-font mb-4">
        {{ $lead->name }}
    </h1>
    <p class="text-gray-400 max-w-xl mx-auto">
        Premium services in {{ $lead->main_area ?? 'your area' }}
    </p>
</header>

<section class="px-6 pb-16 max-w-7xl mx-auto">
    <div class="grid md:grid-cols-4 gap-4">
        @foreach($images as $img)
            <img src="{{ $img }}" class="rounded-xl h-60 w-full object-cover">
        @endforeach
    </div>
</section>
@endif


{{-- 🔥 DYNAMIC SECTIONS --}}
@foreach($sections as $section)

    {{-- HERO --}}
    @if($section['type'] == 'hero')
    <header id="home" class="pt-28 pb-12 px-6 max-w-7xl mx-auto scroll-mt-24">
        <div class="relative h-[500px] rounded-3xl overflow-hidden">
            <img src="{{ $images[0] }}" class="w-full h-full object-cover">

            <div class="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent"></div>

            <div class="absolute bottom-10 left-10 right-10">
                <h1 class="text-5xl md:text-6xl heading-font italic mb-4">
                    {{ $section['title'] ?? $lead->name }}
                </h1>

                <p class="text-gray-300 max-w-xl">
                    {{ $section['subtitle'] ?? 'Best services in ' . $lead->main_area }}
                </p>
            </div>
        </div>
    </header>
    @endif


    {{-- ABOUT --}}
    @if($section['type'] == 'about')
    <section id="about" class="py-16 px-6 text-center scroll-mt-24">
        <p class="max-w-2xl mx-auto text-gray-300">
            {{ $section['content'] }}
        </p>
    </section>
    @endif


    {{-- SERVICES --}}
    @if($section['type'] == 'services')
    <section id="services" class="py-24 px-6 max-w-7xl mx-auto scroll-mt-24">
        <h2 class="text-4xl heading-font mb-16 text-center">Services</h2>

        <div class="grid md:grid-cols-3 gap-10">
            @foreach($section['items'] as $item)
                <div class="accent-border pl-6">
                    <h3 class="font-bold text-lg">{{ $item }}</h3>
                </div>
            @endforeach
        </div>
    </section>
    @endif


    {{-- SPECIAL --}}
    @if($section['type'] == 'special')
    <section class="py-24 px-6 bg-zinc-900 text-center">
        <h2 class="text-3xl heading-font mb-10">
            {{ $section['name'] }}
        </h2>

        <div class="grid md:grid-cols-3 gap-6">
            @foreach($section['items'] as $item)
                <div class="glass-card p-6 rounded-xl">
                    {{ $item }}
                </div>
            @endforeach
        </div>
    </section>
    @endif


    {{-- GALLERY --}}
    @if($section['type'] == 'gallery')
    <section id="gallery" class="py-16 px-6 max-w-7xl mx-auto scroll-mt-24">
        <h2 class="text-3xl heading-font mb-10 text-center">Gallery</h2>

        <div class="grid md:grid-cols-4 gap-4">
            @foreach($images as $img)
                <img src="{{ $img }}" class="rounded-xl h-56 w-full object-cover">
            @endforeach
        </div>
    </section>
    @endif


    {{-- CTA --}}
    @if($section['type'] == 'cta')
    <section class="py-16 text-center bg-blue-600">
        <h2 class="text-2xl font-bold">
            {{ $section['text'] }}
        </h2>

        <a href="tel:{{ $lead->phone }}" class="mt-6 inline-block bg-white text-black px-6 py-3 rounded-xl font-bold">
            Call Now
        </a>
    </section>
    @endif

@endforeach


{{-- CONTACT --}}
<section id="contact" class="py-24 px-6 bg-zinc-900 border-t border-white/5 text-center scroll-mt-24">
    <h2 class="text-4xl heading-font mb-8">Contact</h2>

    <p class="text-lg text-gray-300">{{ $lead->address }}</p>
    <p class="text-lg text-gray-300 mb-6">{{ $lead->phone }}</p>

    <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($lead->address) }}"
       target="_blank"
       class="bg-white text-black px-6 py-3 rounded-xl font-bold">
        Get Directions
    </a>

    <div class="mt-10 max-w-xl mx-auto">
        <img src="{{ $images[2] ?? $images[0] }}" class="rounded-2xl shadow-xl w-full h-64 object-cover">
    </div>
</section>

<footer class="bg-[#020B2D] text-white pt-20 pb-32 px-6 relative">

    <div class="max-w-7xl mx-auto grid md:grid-cols-3 gap-16">

        {{-- LEFT --}}
        <div>
            <h2 class="text-4xl heading-font leading-tight mb-6">
                {{ $lead->name }}
            </h2>

            <p class="text-gray-400 text-lg">
                Your secure and comfortable destination in
                {{ $lead->main_area ?? 'your area' }}.
            </p>
        </div>

        {{-- QUICK LINKS --}}
        <div>
            <h3 class="text-sm uppercase tracking-[0.2em] text-gray-400 mb-6">
                Quick Links
            </h3>

            <ul class="space-y-4 text-lg">

                <li>
                    <a href="#top"
   class="hover:text-blue-400 transition duration-300">
    Home
</a>
                </li>

                <li>
                    <a href="#about"
                       class="hover:text-blue-400 transition duration-300">
                        About
                    </a>
                </li>

                <li>
                    <a href="#services"
                       class="hover:text-blue-400 transition duration-300">
                        Services
                    </a>
                </li>

                <li>
                    <a href="#gallery"
                       class="hover:text-blue-400 transition duration-300">
                        Gallery
                    </a>
                </li>

                <li>
                    <a href="#contact"
                       class="hover:text-blue-400 transition duration-300">
                        Contact
                    </a>
                </li>

            </ul>
        </div>

        {{-- BUSINESS HOURS --}}
        <div>
            <h3 class="text-sm uppercase tracking-[0.2em] text-gray-400 mb-6">
                Business Hours
            </h3>

            <div class="space-y-3 text-gray-300 text-lg">
                <p>Monday: 24/7 Resident Access</p>
                <p>Tuesday: 24/7 Resident Access</p>
                <p>Wednesday: 24/7 Resident Access</p>
                <p class="text-gray-500">...and more</p>
            </div>
        </div>

    </div>

    {{-- BOTTOM --}}
    <div class="max-w-7xl mx-auto mt-20 pt-8 border-t border-white/10 flex flex-col md:flex-row justify-between items-center text-gray-500 text-sm">

        <p>
            © {{ date('Y') }} {{ $lead->name }}.
            All rights reserved.
        </p>

        <p class="mt-4 md:mt-0">
            Powered by AI ✦
        </p>

    </div>

</footer>

</body>
</html>