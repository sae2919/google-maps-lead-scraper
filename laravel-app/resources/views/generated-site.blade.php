<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        {{ $lead->name }}
    </title>

    <meta name="description"
          content="{{ $lead->name }} professional business website">

    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-white text-gray-900">

    @php

        $meta = json_decode(
            $lead->ai_metadata,
            true
        );

        $design = $meta['design'] ?? [];

        $images = $meta['images']
            ?? [];

        $colors = $design['colors']
            ?? [];

        $sections = $design['sections']
            ?? [];

    @endphp

    {{-- NAVBAR --}}

    <nav class="shadow-md p-5 flex justify-between">

        <h1 class="text-2xl font-bold">

            {{ $lead->name }}

        </h1>

        <div class="flex gap-6">

            @foreach($design['navbar'] ?? [] as $item)

                <a href="#"
                   class="hover:text-blue-600">

                    {{ $item }}

                </a>

            @endforeach

        </div>

    </nav>

    {{-- HERO --}}

    @foreach($sections as $index => $section)

        @if($section['type'] === 'hero')

            <section class="py-24 px-6 text-center">

                <h2 class="text-5xl font-bold mb-6">

                    {{ $section['title'] ?? $lead->name }}

                </h2>

                <p class="text-xl text-gray-600 mb-10">

                    {{ $section['body'] ?? '' }}

                </p>

                @if(isset($images[0]))

                    <img src="{{ $images[0] }}"
                         class="rounded-3xl shadow-xl mx-auto w-full max-w-5xl">

                @endif

            </section>

        @endif

    @endforeach

    {{-- SERVICES --}}

    @foreach($sections as $section)

        @if($section['type'] === 'services')

            <section class="py-20 px-10 bg-gray-50">

                <h2 class="text-4xl font-bold mb-12 text-center">

                    Our Services

                </h2>

                <div class="grid md:grid-cols-3 gap-8">

                    @foreach($section['items'] ?? [] as $item)

                        <div class="bg-white rounded-3xl shadow-lg p-8">

                            <h3 class="text-2xl font-bold mb-4">

                                {{ $item['title'] ?? '' }}

                            </h3>

                            <p class="text-gray-600">

                                {{ $item['desc'] ?? '' }}

                            </p>

                        </div>

                    @endforeach

                </div>

            </section>

        @endif

    @endforeach

    {{-- GALLERY --}}

    @if(count($images))

        <section class="py-20 px-10">

            <h2 class="text-4xl font-bold mb-12 text-center">

                Gallery

            </h2>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">

                @foreach($images as $img)

                    <img src="{{ $img }}"
                         class="rounded-2xl shadow-lg h-72 w-full object-cover">

                @endforeach

            </div>

        </section>

    @endif

    {{-- CONTACT --}}

    <section class="py-24 px-6 bg-gray-900 text-white text-center">

        <h2 class="text-4xl font-bold mb-6">

            Contact {{ $lead->name }}

        </h2>

        <p class="mb-4">

            {{ $lead->phone }}

        </p>

        <p class="mb-4">

            {{ $lead->email }}

        </p>

        <p>

            {{ $lead->address }}

        </p>

    </section>

</body>

</html>