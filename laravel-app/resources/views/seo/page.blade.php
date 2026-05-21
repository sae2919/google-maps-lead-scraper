<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        {{ $content['hero']['title'] ?? 'AI Website' }}
    </title>

    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-gray-50 text-gray-900">

    {{-- NAVBAR --}}
    <nav class="bg-white shadow-md sticky top-0 z-50">

        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">

            <div class="text-2xl font-bold text-indigo-600">

                {{ $content['navbar']['logo'] ?? 'Business' }}

            </div>

            <div class="hidden md:flex items-center gap-8 text-sm font-medium">

                @foreach(($content['navbar']['menu'] ?? []) as $menu)

                    <a
                        href="#"
                        class="hover:text-indigo-600 transition"
                    >
                        {{ $menu }}
                    </a>

                @endforeach

            </div>

        </div>

    </nav>

    {{-- HERO SECTION --}}
    <section class="relative overflow-hidden bg-gradient-to-r from-indigo-600 via-purple-600 to-cyan-500 text-white">

        <div class="max-w-7xl mx-auto px-6 py-24 grid md:grid-cols-2 gap-12 items-center">

            <div>

                <h1 class="text-5xl font-extrabold leading-tight mb-6">

                    {{ $content['hero']['title'] ?? 'Premium Business Website' }}

                </h1>

                <p class="text-lg opacity-90 mb-8">

                    {{ $content['hero']['subtitle'] ?? 'Modern AI generated website experience.' }}

                </p>

                <button class="bg-white text-indigo-700 px-8 py-4 rounded-xl font-semibold shadow-lg hover:scale-105 transition">

                    {{ $content['hero']['button_text'] ?? 'Get Started' }}

                </button>

            </div>

            <div>

                <img
                    src="{{ $content['images'][0] ?? 'https://images.unsplash.com/photo-1497366754035-f200968a6e72?q=80&w=1200' }}"
                    class="rounded-3xl shadow-2xl w-full"
                >

            </div>

        </div>

    </section>

    {{-- STATS --}}
    <section class="py-20">

        <div class="max-w-7xl mx-auto px-6">

            <div class="grid md:grid-cols-3 gap-8">

                @foreach(($content['stats'] ?? []) as $stat)

                    <div class="bg-white rounded-3xl p-10 shadow-lg text-center hover:shadow-2xl transition">

                        <h2 class="text-5xl font-extrabold text-indigo-600 mb-4">

                            {{ $stat['number'] }}

                        </h2>

                        <p class="text-gray-600 text-lg">

                            {{ $stat['label'] }}

                        </p>

                    </div>

                @endforeach

            </div>

        </div>

    </section>

    {{-- SERVICES --}}
    <section class="py-20 bg-white">

        <div class="max-w-7xl mx-auto px-6">

            <div class="text-center mb-16">

                <h2 class="text-4xl font-bold mb-4">

                    Our Services
                </h2>

                <p class="text-gray-500 text-lg">

                    Premium solutions crafted for excellence.
                </p>

            </div>

            <div class="grid md:grid-cols-3 gap-10">

                @foreach(($content['services'] ?? []) as $service)

                    <div class="bg-gray-50 p-10 rounded-3xl shadow hover:shadow-2xl transition">

                        <div class="w-16 h-16 bg-indigo-100 rounded-2xl flex items-center justify-center mb-6">

                            <span class="text-3xl">✨</span>

                        </div>

                        <h3 class="text-2xl font-bold mb-4">

                            {{ $service['title'] }}

                        </h3>

                        <p class="text-gray-600 leading-8">

                            {{ $service['description'] }}

                        </p>

                    </div>

                @endforeach

            </div>

        </div>

    </section>

    {{-- ABOUT --}}
    <section class="py-24">

        <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-2 gap-16 items-center">

            <div>

                <img
                    src="{{ $content['images'][1] ?? 'https://images.unsplash.com/photo-1524758631624-e2822e304c36?q=80&w=1200' }}"
                    class="rounded-3xl shadow-xl"
                >

            </div>

            <div>

                <h2 class="text-5xl font-bold mb-8">

                    {{ $content['about']['title'] ?? 'About Us' }}

                </h2>

                <p class="text-gray-600 text-lg leading-9">

                    {{ $content['about']['description'] ?? 'Professional services with modern experience.' }}

                </p>

            </div>

        </div>

    </section>

    {{-- TESTIMONIALS --}}
    <section class="py-24 bg-gradient-to-r from-indigo-50 to-cyan-50">

        <div class="max-w-7xl mx-auto px-6">

            <div class="text-center mb-16">

                <h2 class="text-4xl font-bold mb-4">

                    Client Testimonials
                </h2>

                <p class="text-gray-600">

                    What our customers say.
                </p>

            </div>

            <div class="grid md:grid-cols-2 gap-10">

                @foreach(($content['testimonials'] ?? []) as $testimonial)

                    <div class="bg-white p-10 rounded-3xl shadow-lg">

                        <p class="text-lg text-gray-600 italic mb-6">

                            "{{ $testimonial['review'] }}"
                        </p>

                        <h4 class="font-bold text-xl">

                            {{ $testimonial['name'] }}
                        </h4>

                    </div>

                @endforeach

            </div>

        </div>

    </section>

    {{-- CONTACT --}}
    <section class="py-24">

        <div class="max-w-5xl mx-auto px-6 text-center">

            <h2 class="text-5xl font-bold mb-8">

                Contact Us
            </h2>

            <p class="text-xl text-gray-600 mb-4">

                📞 {{ $content['contact']['phone'] ?? '+91 9876543210' }}
            </p>

            <p class="text-xl text-gray-600">

                📍 {{ $content['contact']['address'] ?? 'Business Location' }}
            </p>

        </div>

    </section>

    {{-- FOOTER --}}
    <footer class="bg-gray-900 text-white py-10">

        <div class="max-w-7xl mx-auto px-6 text-center">

            <p class="text-gray-400">

                {{ $content['footer']['copyright']
                    ?? '© AI Generated Website' }}

            </p>

        </div>

    </footer>

</body>

</html>