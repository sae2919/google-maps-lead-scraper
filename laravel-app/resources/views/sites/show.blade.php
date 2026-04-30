<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $lead->name }} | {{ ucfirst($category ?? 'Service') }}</title>
    
    <link href="https://fonts.googleapis.com/css2?family={{ str_replace(' ', '+', $aiContent['theme']['font'] ?? 'Poppins') }}:wght@300;400;600;700;800&display=swap" rel="stylesheet">

<style>
:root {
    --primary: {{ $aiContent['theme']['color'] ?? '#2563eb' }};
    --primary-dark: #1e3a8a;
    --primary-soft: rgba(37, 99, 235, 0.08);
    --dark: #0f172a;
    --muted: #64748b;
    --light: #f8fafc;
    --white: #ffffff;
    --border: #e2e8f0;
    --shadow-lg: 0 30px 60px rgba(0,0,0,0.1);
    --radius: 32px;
}

/* RESET & BASE */
* { margin:0; padding:0; box-sizing:border-box; scroll-behavior: smooth; cursor: none; }

body {
    font-family: '{{ $aiContent['theme']['font'] ?? 'Poppins' }}', sans-serif;
    background: var(--white);
    color: var(--dark);
    line-height: 1.7;
    overflow-x: hidden;
}

#cursor {
    width: 20px; height: 20px; background: var(--primary); border-radius: 50%;
    position: fixed; pointer-events: none; z-index: 9999; opacity: 0.3;
    transition: transform 0.1s ease; transform: translate(-50%, -50%);
}

.container { max-width: 1240px; margin: auto; padding: 0 30px; }
section { padding: 100px 0; position: relative; width: 100%; display: block; overflow: hidden; }

nav {
    position: fixed; top: 0; width: 100%; z-index: 2000;
    background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(25px);
    border-bottom: 1px solid var(--border);
}

.nav-content { display: flex; justify-content: space-between; align-items: center; padding: 18px 0; }
.logo { font-weight: 800; font-size: 22px; color: var(--primary); text-decoration: none; }
.nav-links { display: flex; gap: 30px; }
.nav-links a { text-decoration: none; color: var(--dark); font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 1px; }

/* HERO */
.hero { min-height: 100vh; display: flex; align-items: center; background: radial-gradient(circle at 80% 20%, var(--primary-soft), transparent); }
.hero-grid { display: grid; grid-template-columns: 1.1fr 0.9fr; gap: 80px; align-items: center; }
.hero h1 { font-size: 64px; line-height: 1.1; font-weight: 800; letter-spacing: -2px; margin-bottom: 25px; }

.btn {
    display: inline-flex; align-items: center; padding: 18px 40px; border-radius: 15px;
    background: var(--primary); color: white; text-decoration: none; font-weight: 700;
    box-shadow: 0 15px 30px var(--primary-soft); transition: 0.3s ease;
}

.hero-image-box img { width: 100%; height: 500px; object-fit: cover; border-radius: var(--radius); box-shadow: var(--shadow-lg); }

/* SERVICES */
.services-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; }
.service-card { padding: 40px; border-radius: 24px; border: 1px solid var(--border); background: var(--white); }

/* GALLERY FIX - NO OVERLAP */
.gallery-layout { 
    display: grid; 
    grid-template-columns: 1.5fr 1fr; 
    gap: 20px; 
    margin-bottom: 40px;
}
.gal-main { width: 100%; height: 500px; object-fit: cover; border-radius: 20px; }
.gal-sub { display: grid; grid-template-rows: 1fr 1fr; gap: 20px; height: 500px; }
.gal-img-small { width: 100%; height: 100%; object-fit: cover; border-radius: 20px; }

/* FOOTER FIX */
footer { 
    background: #0f172a; 
    color: #f8fafc; 
    padding: 80px 0 40px; 
    position: relative; 
    z-index: 10; 
    margin-top: 50px; 
}
.footer-grid { display: grid; grid-template-columns: 1.5fr 1fr 1fr; gap: 60px; margin-bottom: 50px; }
.footer-title { font-size: 14px; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; color: var(--primary); margin-bottom: 20px; }
.address-text { color: #94a3b8; font-size: 16px; line-height: 1.8; }

[data-reveal] { opacity: 0; transform: translateY(30px); transition: 0.8s ease-out; }
[data-reveal].active { opacity: 1; transform: translateY(0); }

@media (max-width: 1024px) {
    .hero-grid, .gallery-layout, .footer-grid { grid-template-columns: 1fr; height: auto; text-align: center; }
    .gal-sub, .gal-main { height: auto; min-height: 300px; }
    .hero h1 { font-size: 40px; }
    #cursor { display: none; }
}
</style>
</head>
@php
$category = strtolower(trim($category ?? 'salon'));

// normalize categories
if(str_contains($category, 'beauty') || str_contains($category, 'salon') || str_contains($category, 'hair')) {
    $category = 'salon';
}
if(str_contains($category, 'restaurant') || str_contains($category, 'food') || str_contains($category, 'cafe')) {
    $category = 'restaurant';
}
if(str_contains($category, 'gym') || str_contains($category, 'fitness')) {
    $category = 'gym';
}
@endphp
@php
    $cat = strtolower($category ?? 'salon');
    
    // AUTOMATED BEAUTY INDUSTRY DETECTION
    
    $config = match(true) {
        str_contains($cat, 'salon') || str_contains($cat, 'beauty') || str_contains($cat, 'hair') || str_contains($cat, 'academy') => [
            'nav_gallery' => 'Lookbook',
            'nav_about' => 'The Studio',
            'hero_tag' => 'Salon & Beauty Academy',
            'cta' => 'Book Appointment',
            'icon' => '✂️'
        ],
        str_contains($cat, 'restaurant') || str_contains($cat, 'food') || str_contains($cat, 'cafe') => [
            'nav_gallery' => 'Our Menu',
            'nav_about' => 'Our Story',
            'hero_tag' => 'Restaurant',
            'cta' => 'Order Now',
            'icon' => '🍽️'
        ],
        default => [
            'nav_gallery' => 'Portfolio',
            'nav_about' => 'About Us',
            'hero_tag' => ucfirst($cat),
            'cta' => 'Get Started',
            'icon' => '💎'
        ],
    };

    $images = array_filter($images ?? []);
    $imgHero = $images[0] ?? 'https://via.placeholder.com/800x600';
    $imgAbout = $images[1] ?? ($images[0] ?? '');
    $imgGal1 = $images[2] ?? ($images[0] ?? '');
    $imgGal2 = $images[3] ?? ($images[1] ?? '');
    $imgGal3 = $images[4] ?? ($images[2] ?? '');
@endphp

<main>
   @php
$navItems = $navItems ?? ($design['navbar'] ?? ['Home','About','Services','Gallery','Contact']);
@endphp

<nav>
    <div class="container nav-content">
        <a href="#hero" class="logo">{{ $lead->name }}</a>

        <div class="nav-links">
            @foreach($navItems as $nav)
                @php
                    // 🔥 clean anchor (very important)
                    $id = strtolower(str_replace(' ', '-', $nav));
                @endphp

                <a href="#{{ $id }}">{{ $nav }}</a>
            @endforeach
        </div>
    </div>
</nav>
    



@foreach($design['sections'] ?? [] as $section)

    {{-- HERO --}}
    @if($section['type'] === 'hero')
    <section class="hero" id="hero">
        <div class="container hero-grid">
            <div data-reveal>
                <span style="color:var(--primary); font-weight:700; text-transform:uppercase;">
                    {{ $config['hero_tag'] }}
                </span>

                <h1>{{ $section['title'] ?? $lead->name }}</h1>

                <p style="color:var(--muted);">
                    {{ $section['body'] ?? ($aiContent['tagline'] ?? '') }}
                </p>

                <a href="#contact" class="btn">{{ $config['cta'] }}</a>
            </div>

            <div class="hero-image-box" data-reveal>
                <img src="{{ $imgHero }}">
            </div>
        </div>
    </section>
    @endif


    {{-- ABOUT --}}
@if($section['type'] === 'about')
<section id="about" class="container">
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:80px;">
        <img src="{{ $imgAbout }}" style="width:100%; border-radius:var(--radius);">

        <div>
            <h2>
                {{ $section['title'] 
                   ?? ($config['nav_about'] 
                   ?? 'About '.$lead->name) }}
            </h2>

            <p>
                {{ $aiContent['about'] 
                   ?? $section['body'] 
                   ?? "Welcome to {$lead->name}, your trusted {$category}." }}
            </p>
        </div>
    </div>
</section>
@endif


    {{-- SERVICES --}}
    @if($section['type'] === 'services')

    @php
    $items = $section['items'] ?? [];

    // 🔥 Validate AI content
    if(!empty($items)) {
        $json = strtolower(json_encode($items));

        $isWrong = match($category) {
            'salon' => str_contains($json, 'food') || str_contains($json, 'dish'),
            'restaurant' => str_contains($json, 'hair') || str_contains($json, 'makeup'),
            'gym' => str_contains($json, 'food'),
            default => false
        };

        if($isWrong) {
            $items = [];
        }
    }

    // 🔥 Fallback
    if(empty($items)) {
        $items = match($category) {
            'salon' => [
                ['title'=>'Hair Styling','desc'=>'Professional cuts','icon'=>'✂️'],
                ['title'=>'Facial','desc'=>'Skin treatments','icon'=>'💆‍♀️'],
                ['title'=>'Makeup','desc'=>'Bridal & party','icon'=>'💄'],
            ],
            'gym' => [
                ['title'=>'Training','desc'=>'Personal coaching','icon'=>'💪'],
                ['title'=>'Weight Loss','desc'=>'Fat burn','icon'=>'🔥'],
                ['title'=>'Yoga','desc'=>'Flexibility','icon'=>'🧘'],
            ],
            'restaurant' => [
                ['title'=>'Dine In','desc'=>'Comfort dining','icon'=>'🍽️'],
                ['title'=>'Takeaway','desc'=>'Quick pickup','icon'=>'📦'],
                ['title'=>'Delivery','desc'=>'Fast delivery','icon'=>'🚀'],
            ],
            default => [
                ['title'=>'Service 1','desc'=>'Quality service','icon'=>'✨'],
                ['title'=>'Service 2','desc'=>'Customer support','icon'=>'💬'],
                ['title'=>'Service 3','desc'=>'Affordable','icon'=>'💰'],
            ]
        };
    }
    @endphp

    <section id="services" style="background:var(--light);">
        <div class="container">
            <h2 style="text-align:center;">{{ $section['title'] ?? 'Our Services' }}</h2>

            <div class="services-grid">
                @foreach($items as $item)
                <div class="service-card">
                    <div>{{ $item['icon'] ?? '✨' }}</div>
                    <h3>{{ $item['title'] }}</h3>
                    <p>{{ $item['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    @endif


    {{-- GALLERY --}}
    @if($section['type'] === 'gallery')
    <section id="gallery" class="container">

        <h2 style="text-align:center;">
            {{ match($category) {
                'restaurant' => 'Food Highlights',
                'salon' => 'Lookbook',
                'gym' => 'Transformation Gallery',
                'store' => 'Product Highlights',
                default => 'Highlights'
            } }}
        </h2>

        <div class="gallery-layout">
            <img src="{{ $imgGal1 }}" class="gal-main">
            <div class="gal-sub">
                <img src="{{ $imgGal2 }}" class="gal-img-small">
                <img src="{{ $imgGal3 }}" class="gal-img-small">
            </div>
        </div>

    </section>
    @endif


    {{-- CONTACT --}}
    @if($section['type'] === 'contact')
    <section id="contact" class="container">
        <iframe width="100%" height="400"
            src="https://maps.google.com/maps?q={{ urlencode($lead->address) }}&output=embed">
        </iframe>
    </section>
    @endif

@endforeach

</main>

<footer>
    <div class="container footer-grid">
        <div>
            <h2 style="font-size:26px; margin-bottom:20px; color:var(--white);">{{ $lead->name }}</h2>
            <p style="color:#94a3b8; font-size:15px; max-width:400px;">Committed to professional excellence and client satisfaction. Experience the difference with our expert team.</p>
        </div>
        <div>
            <p class="footer-title">Navigation</p>
            <div style="display:flex; flex-direction:column; gap:12px;">
                <a href="#about" style="color:#94a3b8; text-decoration:none;">{{ $config['nav_about'] }}</a>
                <a href="#services" style="color:#94a3b8; text-decoration:none;">Services</a>
                <a href="#gallery" style="color:#94a3b8; text-decoration:none;">{{ $config['nav_gallery'] }}</a>
            </div>
        </div>
        <div>
            <p class="footer-title">Location & Contact</p>
            <p class="address-text">📍 {{ $lead->address }}</p>
            <p class="address-text" style="margin-top:15px;">📞 {{ $lead->phone }}</p>
        </div>
    </div>
    <div class="container" style="text-align:center; padding-top:40px; border-top:1px solid rgba(255,255,255,0.1); color:#64748b; font-size:13px;">
        © {{ date('Y') }} {{ $lead->name }}. All Rights Reserved.
    </div>
</footer>

<script>
    const cursor = document.getElementById('cursor');
    document.addEventListener('mousemove', e => {
        cursor.style.left = e.clientX + 'px';
        cursor.style.top = e.clientY + 'px';
    });

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => { if (entry.isIntersecting) entry.target.classList.add('active'); });
    }, { threshold: 0.1 });
    document.querySelectorAll('[data-reveal]').forEach(el => observer.observe(el));
</script>

</body>
</html>