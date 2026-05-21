<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    <url>

        <loc>{{ url('/') }}</loc>

        <priority>1.0</priority>

    </url>

    @foreach($cities as $city)

        @foreach($services as $service)

            <url>

                <loc>
                    {{ url($city . '/' . $service) }}
                </loc>

                <priority>0.8</priority>

            </url>

        @endforeach

    @endforeach

</urlset>