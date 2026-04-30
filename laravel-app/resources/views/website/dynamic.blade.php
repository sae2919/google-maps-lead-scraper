<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $lead->name ?? $data['name'] ?? 'Business' }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @php
        $primary      = $data['colors']['primary'] ?? '#3b82f6';
        $accent       = $data['colors']['accent']  ?? '#1e3a8a';
        $bg           = $data['colors']['bg']       ?? '#ffffff';
        $aboutData    = $data['about']         ?? [];
        $statsData    = $data['stats']         ?? [];
        $servicesAI   = $data['services']      ?? [];
        $reviewsAI    = $data['reviews']       ?? [];
        $hoursAI      = $data['opening_hours'] ?? [];
        $imgs         = array_values(array_filter($data['images'] ?? []));
        $heroImg      = $imgs[0] ?? '';
        $isLight      = in_array($bg, ['#ffffff','#f8fafc','#fffbeb','#f0fdf4','#f0fdf9','#fdf6f9','#f8f7f4','#f0fdfa','#faf5ff','#f8faff']);
        $sectionAlt   = $isLight ? '#f8fafc' : 'rgba(255,255,255,0.04)';
    @endphp

    <style>
        :root {
            --primary : {{ $primary }};
            --accent  : {{ $accent }};
            --bg      : {{ $bg }};
            --radius  : 16px;
            --shadow  : 0 4px 24px rgba(0,0,0,0.08);
            --shadow-h: 0 20px 60px rgba(0,0,0,0.14);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body { background: var(--bg); font-family: 'Plus Jakarta Sans', sans-serif; color: #0f172a; line-height: 1.6; padding-bottom: 80px; }

        /* ── NAVBAR ── */
        nav { position: sticky; top: 0; z-index: 100; background: rgba(255,255,255,0.9); backdrop-filter: blur(16px); border-bottom: 1px solid rgba(0,0,0,0.06); padding: 0 32px; height: 68px; display: flex; align-items: center; justify-content: space-between; }
        .nav-logo { font-family: 'Syne', sans-serif; font-size: 20px; font-weight: 800; color: var(--primary); text-decoration: none; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 300px; }
        .nav-links { display: flex; gap: 28px; }
        .nav-links a { text-decoration: none; color: #64748b; font-weight: 600; font-size: 14px; transition: color 0.2s; }
        .nav-links a:hover { color: var(--primary); }
        .nav-cta { background: var(--primary); color: #fff; padding: 10px 22px; border-radius: 50px; text-decoration: none; font-weight: 700; font-size: 13px; transition: all 0.2s ease; white-space: nowrap; }
        .nav-cta:hover { background: var(--accent); transform: translateY(-1px); }
        @media (max-width: 768px) { .nav-links { display: none; } }

        /* ── HERO ── */
        .hero { position: relative; min-height: 92vh; display: flex; align-items: flex-end; overflow: hidden; }
        .hero-bg { position: absolute; inset: 0; background-size: cover; background-position: center; background-color: var(--accent); transform: scale(1.04); transition: transform 10s ease; }
        .hero:hover .hero-bg { transform: scale(1); }
        .hero-overlay { position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.88) 0%, rgba(0,0,0,0.45) 40%, rgba(0,0,0,0.1) 100%); }
        .hero-content { position: relative; z-index: 1; max-width: 1180px; margin: 0 auto; padding: 0 32px 80px; width: 100%; }
        .hero-badge { display: inline-flex; align-items: center; gap: 6px; background: rgba(255,255,255,0.12); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.2); border-radius: 100px; padding: 6px 16px; font-size: 11px; font-weight: 700; color: rgba(255,255,255,0.9); letter-spacing: 0.08em; text-transform: uppercase; margin-bottom: 24px; }
        .hero h1 { font-family: 'Syne', sans-serif; font-size: clamp(2.8rem, 7vw, 5.5rem); font-weight: 800; color: #fff; line-height: 1.05; letter-spacing: -0.04em; margin-bottom: 20px; }
        .hero-tagline { font-size: clamp(1rem, 1.8vw, 1.2rem); color: rgba(255,255,255,0.72); max-width: 540px; margin-bottom: 36px; font-weight: 300; line-height: 1.65; }
        .hero-actions { display: flex; gap: 14px; flex-wrap: wrap; }
        .hero-btn-primary { background: var(--primary); color: #fff; padding: 15px 32px; border-radius: 12px; text-decoration: none; font-weight: 700; font-size: 15px; transition: all 0.25s ease; }
        .hero-btn-primary:hover { transform: translateY(-2px); box-shadow: 0 12px 32px rgba(0,0,0,0.3); }
        .hero-btn-ghost { background: rgba(255,255,255,0.1); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.25); color: #fff; padding: 15px 32px; border-radius: 12px; text-decoration: none; font-weight: 600; font-size: 15px; transition: all 0.2s ease; }
        .hero-btn-ghost:hover { background: rgba(255,255,255,0.2); }

        /* ── SECTIONS ── */
        .section { padding: 100px 32px; }
        .section-alt { background: {{ $sectionAlt }}; }
        .container { max-width: 1180px; margin: 0 auto; }
        .section-label { font-size: 11px; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; color: var(--primary); margin-bottom: 12px; display: block; }
        .section-title { font-family: 'Syne', sans-serif; font-size: clamp(1.8rem, 3.5vw, 2.8rem); font-weight: 800; color: var(--accent); line-height: 1.1; letter-spacing: -0.03em; margin-bottom: 18px; }
        .section-title.centered { text-align: center; }
        .section-body { color: #64748b; font-size: 1rem; line-height: 1.75; max-width: 600px; font-weight: 400; }
        .section-body.centered { text-align: center; margin: 0 auto 52px; }

        /* ── ABOUT ── */
        .about-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: center; }
        @media (max-width: 768px) { .about-grid { grid-template-columns: 1fr; gap: 40px; } }
        .about-img-wrap { position: relative; }
        .about-img-wrap img { width: 100%; height: 460px; object-fit: cover; border-radius: 24px; display: block; }
        .about-img-badge { position: absolute; bottom: -18px; right: -18px; background: var(--primary); color: #fff; padding: 18px 22px; border-radius: 16px; text-align: center; box-shadow: var(--shadow-h); }
        .about-img-badge strong { display: block; font-family: 'Syne', sans-serif; font-size: 26px; font-weight: 800; }
        .about-img-badge span { font-size: 11px; opacity: 0.85; }
        .about-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-top: 36px; }
        .stat-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 18px 14px; text-align: center; }
        .stat-card strong { display: block; font-family: 'Syne', sans-serif; font-size: 22px; font-weight: 800; color: var(--primary); }
        .stat-card span { font-size: 11px; color: #94a3b8; font-weight: 500; }

        /* ── SERVICES ── */
        .services-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 22px; margin-top: 52px; }
        .service-card { background: #fff; border: 1px solid #e2e8f0; border-radius: var(--radius); padding: 32px 28px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); position: relative; overflow: hidden; }
        .service-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, var(--primary), var(--accent)); transform: scaleX(0); transform-origin: left; transition: transform 0.3s ease; }
        .service-card:hover { transform: translateY(-8px); box-shadow: var(--shadow-h); border-color: transparent; }
        .service-card:hover::before { transform: scaleX(1); }
        .service-icon { font-size: 34px; margin-bottom: 18px; display: block; }
        .service-title { font-family: 'Syne', sans-serif; font-size: 17px; font-weight: 800; color: var(--accent); margin-bottom: 10px; }
        .service-desc { color: #64748b; font-size: 13.5px; line-height: 1.65; }

        /* ── GALLERY ── */
        .gallery-grid { display: grid; grid-template-columns: 2fr 1fr; grid-template-rows: 260px 260px; gap: 14px; margin-top: 52px; }
        .gallery-item { border-radius: var(--radius); overflow: hidden; }
        .gallery-item:first-child { grid-row: 1 / 3; }
        .gallery-item img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.5s ease; }
        .gallery-item:hover img { transform: scale(1.06); }
        @media (max-width: 640px) { .gallery-grid { grid-template-columns: 1fr; grid-template-rows: auto; } .gallery-item:first-child { grid-row: auto; } .gallery-item { height: 220px; } }

        /* ── REVIEWS ── */
        .reviews-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 22px; margin-top: 52px; }
        .review-card { background: #fff; border: 1px solid #e2e8f0; border-radius: var(--radius); padding: 30px; position: relative; transition: all 0.3s ease; }
        .review-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-h); border-color: var(--primary); }
        .review-quote { font-size: 52px; color: var(--primary); opacity: 0.12; line-height: 1; font-family: Georgia, serif; position: absolute; top: 14px; left: 22px; }
        .review-stars { color: #fbbf24; font-size: 15px; margin-bottom: 12px; letter-spacing: 2px; }
        .review-text { font-size: 14px; color: #374151; font-style: italic; line-height: 1.65; margin-bottom: 20px; }
        .review-author { display: flex; align-items: center; gap: 12px; }
        .review-avatar { width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), var(--accent)); display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; color: #fff; flex-shrink: 0; }
        .review-name { font-weight: 700; font-size: 13px; color: var(--accent); }
        .review-role { font-size: 11px; color: #94a3b8; }

        /* ── INFO ── */
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 28px; margin-top: 52px; }
        @media (max-width: 640px) { .info-grid { grid-template-columns: 1fr; } }
        .info-card { background: #fff; border: 1px solid #e2e8f0; border-radius: var(--radius); padding: 30px; }
        .info-card-title { font-size: 11px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: var(--primary); margin-bottom: 16px; }
        .info-row { display: flex; gap: 12px; align-items: flex-start; padding: 10px 0; border-bottom: 1px solid #f1f5f9; }
        .info-row:last-child { border-bottom: none; }
        .info-icon { font-size: 17px; flex-shrink: 0; margin-top: 2px; }
        .info-text { font-size: 14px; color: #374151; line-height: 1.5; }
        .info-text strong { display: block; font-size: 10px; color: #94a3b8; margin-bottom: 2px; letter-spacing: 0.05em; text-transform: uppercase; }
        .hours-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #f1f5f9; font-size: 13.5px; }
        .hours-row:last-child { border-bottom: none; }
        .hours-row .day { color: #64748b; font-weight: 500; }
        .hours-row .time { font-weight: 700; color: #0f172a; }
        .hours-row .closed { color: #ef4444; font-weight: 700; }
        .hours-row .open-24 { color: #16a34a; font-weight: 700; }

        /* ── CONTACT ── */
        .contact-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 48px; align-items: start; margin-top: 52px; }
        @media (max-width: 768px) { .contact-grid { grid-template-columns: 1fr; } }
        .contact-map-wrap { border-radius: var(--radius); overflow: hidden; height: 340px; background: #e2e8f0; }
        .contact-map-wrap iframe { width: 100%; height: 100%; border: none; }
        .contact-action-btns { display: flex; flex-direction: column; gap: 14px; }
        .contact-btn { display: flex; align-items: center; gap: 14px; padding: 18px 22px; border-radius: 14px; text-decoration: none; font-weight: 700; font-size: 14px; transition: all 0.2s ease; }
        .contact-btn:hover { transform: translateX(5px); }
        .contact-btn-icon { width: 42px; height: 42px; border-radius: 11px; display: flex; align-items: center; justify-content: center; font-size: 19px; flex-shrink: 0; }
        .btn-arrow { margin-left: auto; font-size: 17px; transition: transform 0.2s; }
        .contact-btn:hover .btn-arrow { transform: translateX(4px); }
        .btn-call { background: #eff6ff; color: var(--accent); }
        .btn-wa   { background: #f0fdf4; color: #15803d; }
        .btn-dir  { background: #f8fafc; color: #0f172a; }
        .btn-call .contact-btn-icon { background: #dbeafe; }
        .btn-wa   .contact-btn-icon { background: #dcfce7; }
        .btn-dir  .contact-btn-icon { background: #e2e8f0; }
        .contact-sub { font-size: 11px; font-weight: 500; opacity: 0.6; margin-bottom: 2px; }

        /* ── FOOTER ── */
        footer { background: #0f172a; color: #f8fafc; padding: 64px 32px 32px; }
        .footer-grid { max-width: 1180px; margin: 0 auto; display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 48px; padding-bottom: 40px; border-bottom: 1px solid rgba(255,255,255,0.07); margin-bottom: 28px; }
        @media (max-width: 768px) { .footer-grid { grid-template-columns: 1fr; gap: 28px; } }
        .footer-brand { font-family: 'Syne', sans-serif; font-size: 20px; font-weight: 800; color: #fff; margin-bottom: 10px; }
        .footer-tagline { color: #64748b; font-size: 13px; line-height: 1.65; }
        .footer-heading { font-size: 10px; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; color: #64748b; margin-bottom: 16px; }
        .footer-links { display: flex; flex-direction: column; gap: 10px; }
        .footer-links a { color: #94a3b8; text-decoration: none; font-size: 13px; transition: color 0.2s; }
        .footer-links a:hover { color: #fff; }
        .footer-bottom { max-width: 1180px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; font-size: 12px; color: #475569; }

        /* ── STICKY BAR ── */
        .sticky-bar { position: fixed; bottom: 0; left: 0; right: 0; z-index: 9999; display: flex; background: rgba(255,255,255,0.96); backdrop-filter: blur(16px); border-top: 1px solid rgba(0,0,0,0.07); box-shadow: 0 -8px 32px rgba(0,0,0,0.08); }
        .sticky-btn { flex: 1; padding: 14px 8px; text-align: center; text-decoration: none; font-size: 11px; font-weight: 700; display: flex; flex-direction: column; align-items: center; gap: 3px; transition: background 0.2s; }
        .sticky-btn:hover { background: #f8fafc; }
        .sticky-btn .s-icon { font-size: 19px; }
        .sticky-btn-call { color: var(--primary); border-right: 1px solid #e2e8f0; }
        .sticky-btn-wa   { color: #16a34a; border-right: 1px solid #e2e8f0; }
        .sticky-btn-map  { color: #374151; }

        /* ── ANIMATION ── */
        .fade-up { opacity: 0; transform: translateY(28px); transition: opacity 0.6s ease, transform 0.6s ease; }
        .fade-up.visible { opacity: 1; transform: translateY(0); }
    </style>
</head>
<body>

{{-- NAVBAR --}}
<nav>
    <a href="#hero" class="nav-logo">{{ $lead->name ?? $data['name'] ?? 'Business' }}</a>
    <div class="nav-links">
        @foreach($data['navbar'] ?? ['Home','About','Services','Gallery','Contact'] as $nav)
            <a href="#{{ Str::slug($nav) }}">{{ $nav }}</a>
        @endforeach
    </div>
    @if(!empty($lead->phone ?? null))
        <a href="tel:{{ $lead->phone }}" class="nav-cta">📞 Call Now</a>
    @endif
</nav>

{{-- SECTIONS --}}
@foreach($data['sections'] as $index => $section)

    {{-- ══ HERO ══ --}}
    @if($section['type'] === 'hero')
    <header class="hero" id="hero">
        @if($heroImg)
            <div class="hero-bg" style="background-image:url('{{ $heroImg }}')"></div>
        @else
            <div class="hero-bg" style="background:linear-gradient(135deg,{{ $primary }},{{ $accent }})"></div>
        @endif
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <div class="hero-badge fade-up">
                ✦ &nbsp;{{ ucwords(str_replace('_',' ',$data['category'] ?? 'business')) }}
                @if(!empty($lead->main_area ?? null)) &nbsp;·&nbsp; {{ $lead->main_area }} @endif
            </div>
            <h1 class="fade-up" style="transition-delay:0.1s">{{ $lead->name ?? $section['title'] ?? 'Welcome' }}</h1>
            <p class="hero-tagline fade-up" style="transition-delay:0.2s">
                {{ $data['tagline'] ?? $section['body'] ?? 'Quality service you can trust.' }}
            </p>
            <div class="hero-actions fade-up" style="transition-delay:0.3s">
                @if(!empty($lead->phone ?? null))
                    <a href="tel:{{ $lead->phone }}" class="hero-btn-primary">📞 Book Now</a>
                @endif
                <a href="#about" class="hero-btn-ghost">Learn More →</a>
            </div>
        </div>
    </header>

    {{-- ══ ABOUT ══ --}}
    @elseif($section['type'] === 'about')
    <section class="section" id="about">
        <div class="container">
            <div class="about-grid">
                <div class="about-img-wrap fade-up">
                    @if(isset($imgs[1]))
                        <img src="{{ $imgs[1] }}" alt="{{ $lead->name ?? 'About' }}">
                    @elseif(isset($imgs[0]))
                        <img src="{{ $imgs[0] }}" alt="{{ $lead->name ?? 'About' }}">
                    @endif
                    @if(!empty($statsData[1]))
                    <div class="about-img-badge">
                        <strong>{{ $statsData[1]['value'] ?? '5★' }}</strong>
                        <span>{{ $statsData[1]['label'] ?? 'Rated' }}</span>
                    </div>
                    @endif
                </div>
                <div class="fade-up" style="transition-delay:0.15s">
                    <span class="section-label">Who We Are</span>
                    <h2 class="section-title">{{ $section['title'] ?? 'About Us' }}</h2>
                    @if(!empty($aboutData['paragraph1']))
                        <p class="section-body" style="margin-bottom:18px;">{{ $aboutData['paragraph1'] }}</p>
                        <p class="section-body">{{ $aboutData['paragraph2'] ?? '' }}</p>
                    @else
                        <p class="section-body">{{ $section['body'] ?? "Welcome to " . ($lead->name ?? 'our business') . "." }}</p>
                    @endif
                    @if(!empty($statsData))
                    <div class="about-stats">
                        @foreach($statsData as $st)
                        <div class="stat-card">
                            <strong>{{ $st['value'] ?? '' }}</strong>
                            <span>{{ $st['label'] ?? '' }}</span>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- ══ SERVICES ══ --}}
    @elseif($section['type'] === 'services')
    <section class="section section-alt" id="services">
        <div class="container">
            <div class="fade-up" style="text-align:center;">
                <span class="section-label">What We Offer</span>
                <h2 class="section-title centered">{{ $section['title'] ?? 'Our Services' }}</h2>
                <p class="section-body centered">Everything you need — delivered with expertise and care.</p>
            </div>
            @php $serviceItems = !empty($servicesAI) ? $servicesAI : ($section['items'] ?? []); @endphp
            <div class="services-grid">
                @foreach($serviceItems as $i => $item)
                    <div class="service-card fade-up" style="transition-delay:{{ $i * 0.07 }}s">
                        <span class="service-icon">{{ $item['icon'] ?? '⭐' }}</span>
                        <div class="service-title">{{ $item['title'] ?? $item['name'] ?? 'Service' }}</div>
                        <p class="service-desc">{{ $item['desc'] ?? $item['description'] ?? '' }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ══ GALLERY ══ --}}
    @elseif($section['type'] === 'gallery')
    <section class="section" id="gallery">
        <div class="container">
            <div class="fade-up" style="text-align:center;">
                <span class="section-label">Visual Tour</span>
                <h2 class="section-title centered">{{ $section['title'] ?? 'Our Gallery' }}</h2>
            </div>
            @if(count($imgs) >= 2)
            <div class="gallery-grid fade-up">
                @foreach(array_slice($imgs, 0, 3) as $img)
                    <div class="gallery-item"><img src="{{ $img }}" alt="Gallery"></div>
                @endforeach
            </div>
            @endif
        </div>
    </section>

    {{-- ══ REVIEWS ══ --}}
    @elseif($section['type'] === 'reviews')
    <section class="section section-alt" id="reviews">
        <div class="container">
            <div class="fade-up" style="text-align:center;">
                <span class="section-label">Testimonials</span>
                <h2 class="section-title centered">{{ $section['title'] ?? 'What Customers Say' }}</h2>
            </div>
            <div class="reviews-grid">
                @foreach($reviewsAI as $i => $r)
                    <div class="review-card fade-up" style="transition-delay:{{ $i * 0.1 }}s">
                        <div class="review-quote">"</div>
                        <div class="review-stars">
                            @for($s = 0; $s < ($r['rating'] ?? 5); $s++) ★ @endfor
                        </div>
                        <p class="review-text">"{{ $r['text'] ?? '' }}"</p>
                        <div class="review-author">
                            <div class="review-avatar">{{ strtoupper(substr($r['name'] ?? 'C', 0, 1)) }}</div>
                            <div>
                                <div class="review-name">{{ $r['name'] ?? 'Customer' }}</div>
                                <div class="review-role">{{ $r['role'] ?? 'Verified Customer' }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ══ INFO ══ --}}
    @elseif($section['type'] === 'info')
    <section class="section" id="info">
        <div class="container">
            <div class="fade-up" style="text-align:center;">
                <span class="section-label">Find Us</span>
                <h2 class="section-title centered">{{ $section['title'] ?? 'Business Info' }}</h2>
            </div>
            <div class="info-grid fade-up">
                {{-- Contact Details --}}
                <div class="info-card">
                    <div class="info-card-title">Contact Details</div>
                    @if(!empty($lead->address ?? null))
                    <div class="info-row">
                        <span class="info-icon">📍</span>
                        <div class="info-text"><strong>Address</strong>{{ $lead->address }}</div>
                    </div>
                    @endif
                    @if(!empty($lead->phone ?? null))
                    <div class="info-row">
                        <span class="info-icon">📞</span>
                        <div class="info-text">
                            <strong>Phone</strong>
                            <a href="tel:{{ $lead->phone }}" style="color:var(--primary);text-decoration:none;">{{ $lead->phone }}</a>
                        </div>
                    </div>
                    @endif
                    @if(!empty($data['category']))
                    <div class="info-row">
                        <span class="info-icon">🏷️</span>
                        <div class="info-text"><strong>Category</strong>{{ ucwords(str_replace('_',' ',$data['category'])) }}</div>
                    </div>
                    @endif
                </div>

                {{-- AI-Generated Opening Hours --}}
                <div class="info-card">
                    <div class="info-card-title">Opening Hours</div>
                    @if(!empty($hoursAI))
                        @foreach($hoursAI as $h)
                        <div class="hours-row">
                            <span class="day">{{ $h['day'] ?? '' }}</span>
                            @php $t = $h['time'] ?? null; @endphp
                            @if(is_null($t) || $t === '' || strtolower($t) === 'null' || strtolower($t) === 'closed')
                                <span class="closed">Closed</span>
                            @elseif(str_contains(strtolower($t), '24') || strtolower($t) === 'open 24 hours')
                                <span class="open-24">Open 24 Hours</span>
                            @else
                                <span class="time">{{ $t }}</span>
                            @endif
                        </div>
                        @endforeach
                    @else
                        {{-- Fallback only if AI returned nothing --}}
                        @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $day)
                        <div class="hours-row">
                            <span class="day">{{ $day }}</span>
                            <span class="time">9:00 AM – 9:00 PM</span>
                        </div>
                        @endforeach
                        <div class="hours-row">
                            <span class="day">Sunday</span>
                            <span class="closed">Closed</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- ══ CONTACT ══ --}}
    @elseif($section['type'] === 'contact')
    <section class="section section-alt" id="contact">
        <div class="container">
            <div class="fade-up" style="text-align:center;">
                <span class="section-label">Get In Touch</span>
                <h2 class="section-title centered">{{ $section['title'] ?? 'Contact Us' }}</h2>
                <p class="section-body centered">
                    {{ $section['body'] ?? "We'd love to hear from you. Reach out and we'll respond promptly." }}
                </p>
            </div>
            <div class="contact-grid fade-up">
                <div class="contact-map-wrap">
                    @if(!empty($lead->address ?? null))
                    <iframe src="https://maps.google.com/maps?q={{ urlencode($lead->address) }}&output=embed" loading="lazy" allowfullscreen></iframe>
                    @endif
                </div>
                <div class="contact-action-btns">
                    @if(!empty($lead->phone ?? null))
                    <a href="tel:{{ $lead->phone }}" class="contact-btn btn-call">
                        <div class="contact-btn-icon">📞</div>
                        <div><div class="contact-sub">Call directly</div>{{ $lead->phone }}</div>
                        <span class="btn-arrow">→</span>
                    </a>
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/','', $lead->phone) }}" target="_blank" class="contact-btn btn-wa">
                        <div class="contact-btn-icon">💬</div>
                        <div><div class="contact-sub">Chat on WhatsApp</div>Message us now</div>
                        <span class="btn-arrow">→</span>
                    </a>
                    @endif
                    @if(!empty($lead->address ?? null))
                    <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($lead->address) }}" target="_blank" class="contact-btn btn-dir">
                        <div class="contact-btn-icon">📍</div>
                        <div><div class="contact-sub">Get directions</div>{{ Str::limit($lead->address, 38) }}</div>
                        <span class="btn-arrow">→</span>
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- ══ FALLBACK ══ --}}
    @else
        @php $viewName = 'sections.' . $section['type']; @endphp
        @if(view()->exists($viewName))
        <section class="section {{ $index % 2 !== 0 ? 'section-alt' : '' }}" id="{{ Str::slug($section['type']) }}">
            <div class="container">
                @include($viewName, ['content'=>$section,'images'=>$imgs,'lead'=>$lead,'data'=>$data])
            </div>
        </section>
        @endif
    @endif

@endforeach

{{-- FOOTER --}}
<footer>
    <div class="footer-grid">
        <div>
            <div class="footer-brand">{{ $lead->name ?? 'Business' }}</div>
            <p class="footer-tagline">{{ $data['tagline'] ?? 'Serving customers with excellence and care.' }}</p>
        </div>
        <div>
            <div class="footer-heading">Quick Links</div>
            <div class="footer-links">
                @foreach($data['navbar'] ?? ['Home','About','Services','Gallery','Contact'] as $nav)
                    <a href="#{{ Str::slug($nav) }}">{{ $nav }}</a>
                @endforeach
            </div>
        </div>
        <div>
            <div class="footer-heading">Business Hours</div>
            <div style="font-size:13px;color:#94a3b8;line-height:1.9;">
                @if(!empty($hoursAI))
                    @foreach(array_slice($hoursAI, 0, 3) as $h)
                        @php $t = $h['time'] ?? null; @endphp
                        <div>{{ $h['day'] }}: {{ is_null($t) || strtolower((string)$t) === 'null' || strtolower((string)$t) === 'closed' ? 'Closed' : $t }}</div>
                    @endforeach
                    <div style="color:#475569;font-size:12px;">...and more</div>
                @else
                    Mon – Sat: 9:00 AM – 9:00 PM<br>Sunday: Closed
                @endif
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <span>© {{ date('Y') }} {{ $lead->name ?? 'Business' }}. All rights reserved.</span>
        <span>Powered by AI ✦</span>
    </div>
</footer>

{{-- STICKY BAR --}}
@php $hasPhone = !empty($lead->phone ?? null); $hasAddr = !empty($lead->address ?? null); @endphp
@if($hasPhone || $hasAddr)
<div class="sticky-bar">
    @if($hasPhone)
    <a href="tel:{{ $lead->phone }}" class="sticky-btn sticky-btn-call">
        <span class="s-icon">📞</span>Call
    </a>
    <a href="https://wa.me/{{ preg_replace('/[^0-9]/','', $lead->phone) }}" target="_blank" class="sticky-btn sticky-btn-wa">
        <span class="s-icon">💬</span>WhatsApp
    </a>
    @endif
    @if($hasAddr)
    <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($lead->address) }}" target="_blank" class="sticky-btn sticky-btn-map">
        <span class="s-icon">📍</span>Map
    </a>
    @endif
</div>
@endif

<script>
    const obs = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (e.isIntersecting) { e.target.classList.add('visible'); obs.unobserve(e.target); }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.fade-up').forEach(el => obs.observe(el));
    setTimeout(() => document.querySelectorAll('.hero .fade-up').forEach(el => el.classList.add('visible')), 100);
</script>
</body>
</html>