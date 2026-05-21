<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $config['content']['brand'] ?? 'Business' }}</title>
    <meta name="description" content="{{ $config['content']['tagline'] ?? '' }}">

    @php
        $fontHeading = $config['theme']['font_heading'] ?? 'Cormorant Garamond';
        $fontBody    = $config['theme']['font_body']    ?? 'DM Sans';
        $t           = $config['theme'];
        $c           = $config['content'];
        $layout      = $config['layout'];
        $heroType    = $layout['hero_type'] ?? 'fullscreen';
        $navStyle    = $layout['nav_style'] ?? 'transparent';
        $sections    = $layout['sections']  ?? ['hero','stats','services','about','testimonials','cta','contact'];

        $brand = strtolower($c['brand'] ?? '');

        // Hero + About images by business type
        $heroImg  = 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1920&q=85';
        $aboutImg = 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=900&q=85';

        if (str_contains($brand,'restaurant')||str_contains($brand,'dine')||str_contains($brand,'biryani')||str_contains($brand,'kitchen')) {
            $heroImg='https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=1920&q=85';
            $aboutImg='https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=900&q=85';
        } elseif (str_contains($brand,'cafe')||str_contains($brand,'coffee')||str_contains($brand,'bistro')) {
            $heroImg='https://images.unsplash.com/photo-1554118811-1e0d58224f24?w=1920&q=85';
            $aboutImg='https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=900&q=85';
        } elseif (str_contains($brand,'spa')||str_contains($brand,'salon')||str_contains($brand,'beauty')||str_contains($brand,'wellness')) {
            $heroImg='https://images.unsplash.com/photo-1540555700478-4be289fbecef?w=1920&q=85';
            $aboutImg='https://images.unsplash.com/photo-1519823551278-64ac92734fb1?w=900&q=85';
        } elseif (str_contains($brand,'clinic')||str_contains($brand,'hospital')||str_contains($brand,'medical')||str_contains($brand,'health')) {
            $heroImg='https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?w=1920&q=85';
            $aboutImg='https://images.unsplash.com/photo-1579684385127-1ef15d508118?w=900&q=85';
        } elseif (str_contains($brand,'tech')||str_contains($brand,'software')||str_contains($brand,'digital')||str_contains($brand,'solutions')) {
            $heroImg='https://images.unsplash.com/photo-1497366216548-37526070297c?w=1920&q=85';
            $aboutImg='https://images.unsplash.com/photo-1552664730-d307ca884978?w=900&q=85';
        }

        // Service card images mapped by keyword
        $serviceImages = [
            'room'          => 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=600&q=80',
            'suite'         => 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=600&q=80',
            'accommodation' => 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=600&q=80',
            'dine'          => 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=600&q=80',
            'dining'        => 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=600&q=80',
            'restaurant'    => 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=600&q=80',
            'food'          => 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=600&q=80',
            'cuisine'       => 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=600&q=80',
            'breakfast'     => 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=600&q=80',
            'concierge'     => 'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=600&q=80',
            'service'       => 'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=600&q=80',
            'staff'         => 'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=600&q=80',
            'event'         => 'https://images.unsplash.com/photo-1519167758481-83f550bb49b3?w=600&q=80',
            'banquet'       => 'https://images.unsplash.com/photo-1519167758481-83f550bb49b3?w=600&q=80',
            'conference'    => 'https://images.unsplash.com/photo-1519167758481-83f550bb49b3?w=600&q=80',
            'meeting'       => 'https://images.unsplash.com/photo-1519167758481-83f550bb49b3?w=600&q=80',
            'pool'          => 'https://images.unsplash.com/photo-1571902943202-507ec2618e8f?w=600&q=80',
            'spa'           => 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?w=600&q=80',
            'fitness'       => 'https://images.unsplash.com/photo-1571902943202-507ec2618e8f?w=600&q=80',
            'gym'           => 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=600&q=80',
            'transport'     => 'https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?w=600&q=80',
            'parking'       => 'https://images.unsplash.com/photo-1486325212027-8081e485255e?w=600&q=80',
            'wifi'          => 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=600&q=80',
            'business'      => 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=600&q=80',
            'bar'           => 'https://images.unsplash.com/photo-1514362545857-3bc16c4c7d1b?w=600&q=80',
            'lounge'        => 'https://images.unsplash.com/photo-1514362545857-3bc16c4c7d1b?w=600&q=80',
            'wedding'       => 'https://images.unsplash.com/photo-1519167758481-83f550bb49b3?w=600&q=80',
        ];
        $defaultServiceImg = 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=600&q=80';

        $mapAddress   = urlencode($c['contact']['address'] ?? $c['brand'] ?? 'Hyderabad India');
        $mapsEmbedUrl = 'https://maps.google.com/maps?q='.$mapAddress.'&output=embed&iwloc=near&z=15';

        // Always transparent navbar for fullscreen hero
        $navTransparent = in_array($heroType, ['fullscreen', 'minimal']) || in_array($navStyle, ['transparent','glass']);
    @endphp

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family={{ urlencode($fontHeading) }}:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,600&family={{ urlencode($fontBody) }}:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary:       {{ $t['primary']       ?? '#1A2A44' }};
            --secondary:     {{ $t['secondary']     ?? '#C8A94A' }};
            --accent:        {{ $t['accent']        ?? '#B8860B' }};
            --text-dark:     {{ $t['text_dark']     ?? '#1A2A44' }};
            --text-light:    #ffffff;
            --bg:            {{ $t['bg']            ?? '#FDFDFB' }};
            --surface:       {{ $t['surface']       ?? '#F5F3EE' }};
            --hero-gradient: {{ $t['hero_gradient'] ?? 'linear-gradient(135deg,#1A2A44,#C8A94A)' }};
            --radius:        12px;
            --shadow:        0 4px 20px rgba(0,0,0,0.09);
            --shadow-md:     0 8px 40px rgba(0,0,0,0.13);
            --shadow-lg:     0 20px 60px rgba(0,0,0,0.18);
            --font-h:        '{{ $fontHeading }}', Georgia, serif;
            --font-b:        '{{ $fontBody }}', system-ui, sans-serif;
        }

        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        html{scroll-behavior:smooth}
        body{font-family:var(--font-b);color:var(--text-dark);background:var(--bg);line-height:1.75;-webkit-font-smoothing:antialiased}
        h1,h2,h3,h4{font-family:var(--font-h);line-height:1.15}
        a{text-decoration:none;color:inherit}
        img{max-width:100%;display:block}

        .container{max-width:1180px;margin:0 auto;padding:0 40px}
        .section{padding:96px 0}

        /* Buttons */
        .btn{display:inline-flex;align-items:center;gap:8px;padding:15px 36px;border-radius:var(--radius);font-family:var(--font-b);font-size:.9rem;font-weight:600;letter-spacing:.05em;cursor:pointer;border:2px solid transparent;transition:all .28s ease}
        .btn-primary{background:var(--accent);color:#fff}
        .btn-primary:hover{filter:brightness(1.1);transform:translateY(-2px);box-shadow:0 10px 28px rgba(0,0,0,.2)}
        .btn-white{background:#fff;color:var(--text-dark)}
        .btn-white:hover{background:var(--surface);transform:translateY(-2px);box-shadow:var(--shadow)}
        .btn-outline-white{background:transparent;color:#fff;border-color:rgba(255,255,255,.55)}
        .btn-outline-white:hover{background:rgba(255,255,255,.12)}
        .btn-dark{background:var(--primary);color:#fff}
        .btn-dark:hover{transform:translateY(-2px);box-shadow:var(--shadow-md)}
        .btn-group{display:flex;gap:14px;flex-wrap:wrap}

        /* Tag */
        .tag{display:inline-flex;align-items:center;gap:6px;padding:7px 18px;border-radius:999px;font-size:.72rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase}
        .tag-light{background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.3);backdrop-filter:blur(8px)}
        .tag-accent{background:color-mix(in srgb,var(--secondary) 14%,transparent);color:color-mix(in srgb,var(--accent) 80%,#000);border:1px solid color-mix(in srgb,var(--secondary) 28%,transparent)}

        /* Section header */
        .s-head{text-align:center;margin-bottom:60px}
        .s-head h2{font-size:clamp(2rem,3.5vw,2.9rem);font-weight:700;margin:14px 0 12px;letter-spacing:-.02em;color:var(--text-dark)}
        .s-head p{color:#6b7280;font-size:1.02rem;max-width:500px;margin:0 auto}

        /* ═══ NAVBAR ═══ */
        .navbar{
            position:fixed;top:0;left:0;right:0;z-index:500;
            height:74px;display:flex;align-items:center;justify-content:space-between;
            padding:0 48px;transition:background .35s ease,box-shadow .35s ease;
            background:{{ $navTransparent ? 'transparent' : 'var(--bg)' }};
            {{ !$navTransparent ? 'border-bottom:1px solid rgba(0,0,0,.07);box-shadow:var(--shadow)' : '' }};
        }
        .navbar.scrolled{background:var(--primary)!important;box-shadow:0 4px 24px rgba(0,0,0,.25)}
        .nav-brand{font-family:var(--font-h);font-size:1.3rem;font-weight:700;color:{{ $navTransparent ? '#fff' : 'var(--primary)' }};letter-spacing:-.02em;transition:color .35s ease}
        .navbar.scrolled .nav-brand{color:#fff}
        .navbar.scrolled .btn-dark{background:var(--secondary);color:#fff}

        /* ═══ HERO: FULLSCREEN ═══ */
        @if($heroType === 'fullscreen')
        .hero{min-height:100vh;position:relative;display:flex;align-items:flex-end;padding-bottom:96px;overflow:hidden}
        .hero-bg{position:absolute;inset:0;background:url('{{ $heroImg }}') center/cover no-repeat;transform:scale(1.05);transition:transform 9s ease}
        .hero:hover .hero-bg{transform:scale(1)}
        .hero-ov{position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.82) 0%,rgba(0,0,0,.32) 55%,rgba(0,0,0,.1) 100%)}
        .hero-inner{position:relative;z-index:2;padding:0 80px;max-width:860px;color:#fff}

        @elseif($heroType === 'split')
        .hero{min-height:92vh;display:grid;grid-template-columns:1fr 1fr;overflow:hidden;margin-top:74px}
        .hero-inner{background:var(--hero-gradient);display:flex;flex-direction:column;justify-content:center;padding:80px 64px;color:#fff}
        .hero-img{background:url('{{ $heroImg }}') center/cover no-repeat;min-height:500px}

        @elseif($heroType === 'centered')
        .hero{min-height:88vh;position:relative;display:flex;align-items:center;justify-content:center;text-align:center;overflow:hidden;margin-top:74px}
        .hero-bg{position:absolute;inset:0;background:var(--hero-gradient)}
        .hero-ov{position:absolute;inset:0;background:radial-gradient(ellipse 70% 60% at 50% 50%,rgba(255,255,255,.05),transparent)}
        .hero-inner{position:relative;z-index:2;padding:60px 28px;max-width:720px;color:#fff}
        .hero-inner .btn-group{justify-content:center}

        @else
        .hero{min-height:80vh;background:var(--surface);display:grid;grid-template-columns:1.1fr 1fr;align-items:center;gap:60px;padding:140px 0 80px}
        .hero-inner{padding-left:64px}
        .hero-img{height:500px;border-radius:calc(var(--radius)*2);background:url('{{ $heroImg }}') center/cover;box-shadow:var(--shadow-lg);margin-right:40px}
        @endif

        .hero-inner h1{font-size:clamp(2.6rem,5vw,4rem);font-weight:800;letter-spacing:-.03em;margin:16px 0 18px;line-height:1.08}
        .hero-sub{font-size:1.1rem;opacity:.88;margin-bottom:40px;line-height:1.75;max-width:540px}
        .hero-stat-pill{display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.14);backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,.25);color:#fff;padding:8px 20px;border-radius:999px;font-size:.8rem;font-weight:700;margin-bottom:18px;letter-spacing:.05em}

        /* ═══ STATS BAR ═══ */
        .stats-bar{background:var(--primary);padding:52px 0}
        .stats-grid{display:grid;grid-template-columns:repeat(4,1fr)}
        .stat-item{text-align:center;padding:16px 20px;border-right:1px solid rgba(255,255,255,.1);color:#fff}
        .stat-item:last-child{border-right:none}
        .stat-icon-wrap{width:44px;height:44px;border-radius:10px;background:rgba(255,255,255,.1);display:flex;align-items:center;justify-content:center;margin:0 auto 14px}
        .stat-icon-wrap svg{width:22px;height:22px;fill:var(--secondary)}
        .stat-val{font-family:var(--font-h);font-size:2.1rem;font-weight:800;color:var(--secondary);line-height:1;margin-bottom:6px}
        .stat-lbl{font-size:.72rem;opacity:.6;letter-spacing:.1em;text-transform:uppercase;font-weight:600}

        /* ═══ SERVICES (with images) ═══ */
        .services{background:var(--bg)}
        .svc-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:28px}
        .svc-card{background:var(--bg);border:1px solid rgba(0,0,0,.07);border-radius:calc(var(--radius)*1.5);overflow:hidden;transition:all .32s ease;box-shadow:var(--shadow)}
        .svc-card:hover{transform:translateY(-8px);box-shadow:var(--shadow-lg)}
        .svc-img{height:200px;background-size:cover;background-position:center;background-color:var(--surface);position:relative;overflow:hidden}
        .svc-img::after{content:'';position:absolute;inset:0;background:linear-gradient(to bottom,transparent 40%,rgba(0,0,0,.3) 100%)}
        .svc-body{padding:24px 28px}
        .svc-title{font-family:var(--font-h);font-size:1.1rem;font-weight:700;margin-bottom:10px;color:var(--text-dark)}
        .svc-desc{font-size:.9rem;color:#6b7280;line-height:1.75}

        /* ═══ ABOUT ═══ */
        .about{background:var(--surface)}
        .about-grid{display:grid;grid-template-columns:1fr 1fr;gap:80px;align-items:center}
        .about-img-wrap{position:relative}
        .about-img{height:520px;border-radius:calc(var(--radius)*2);background:url('{{ $aboutImg }}') center/cover;box-shadow:var(--shadow-lg)}
        .about-badge{position:absolute;bottom:-24px;right:-24px;background:var(--primary);color:#fff;padding:22px 28px;border-radius:var(--radius);box-shadow:var(--shadow-md);text-align:center}
        .about-badge .big{font-family:var(--font-h);font-size:2rem;font-weight:800;color:var(--secondary);display:block}
        .about-badge .sm{font-size:.7rem;text-transform:uppercase;letter-spacing:.1em;opacity:.7}
        .about-content h2{font-size:clamp(1.8rem,2.8vw,2.5rem);font-weight:700;margin:12px 0 16px;letter-spacing:-.02em}
        .about-story{color:#4b5563;font-size:1rem;margin-bottom:16px;line-height:1.85}
        .about-mission{border-left:4px solid var(--secondary);padding:14px 20px;margin:24px 0;background:color-mix(in srgb,var(--secondary) 7%,transparent);border-radius:0 var(--radius) var(--radius) 0;font-size:1rem;font-weight:500;color:var(--text-dark);line-height:1.65;font-style:italic}
        .about-hl{list-style:none;display:flex;flex-direction:column;gap:14px;margin-top:28px}
        .about-hl li{display:flex;align-items:center;gap:12px;font-size:.95rem;font-weight:600}
        .about-hl .dot{width:8px;height:8px;border-radius:50%;background:var(--secondary);flex-shrink:0}

        /* ═══ TESTIMONIALS ═══ */
        .testimonials{background:var(--bg)}
        .t-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:28px}
        .t-card{background:var(--surface);border:1px solid rgba(0,0,0,.06);border-radius:calc(var(--radius)*1.5);padding:36px 32px;position:relative;transition:box-shadow .28s ease}
        .t-card:hover{box-shadow:var(--shadow-md)}
        .t-quote{position:absolute;top:18px;right:24px;font-size:5rem;line-height:1;font-family:Georgia,serif;color:var(--secondary);opacity:.15}
        .t-stars{color:#f59e0b;font-size:.85rem;letter-spacing:3px;margin-bottom:16px}
        .t-text{font-size:.97rem;color:#374151;line-height:1.8;font-style:italic;margin-bottom:24px}
        .t-author{display:flex;align-items:center;gap:14px}
        .t-avatar{width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--secondary));display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:1.1rem;font-family:var(--font-h);flex-shrink:0}
        .t-name{font-weight:700;font-size:.95rem}
        .t-role{font-size:.8rem;color:#9ca3af;margin-top:2px}

        /* ═══ CTA ═══ */
        .cta-sec{position:relative;overflow:hidden;background:var(--hero-gradient);text-align:center;padding:100px 24px;color:#fff}
        .cta-sec::before{content:'';position:absolute;inset:0;background:rgba(0,0,0,.25)}
        .cta-sec *{position:relative;z-index:1}
        .cta-sec h2{font-size:clamp(2rem,4vw,3.1rem);font-weight:800;margin-bottom:16px;letter-spacing:-.02em}
        .cta-sec p{font-size:1.08rem;opacity:.85;margin-bottom:40px;max-width:500px;margin-left:auto;margin-right:auto}

        /* ═══ CONTACT ═══ */
        .contact{background:var(--surface)}
        .contact-top{display:grid;grid-template-columns:1fr 1.3fr;gap:60px;align-items:start;margin-bottom:56px}
        .contact-info h2{font-size:clamp(1.8rem,2.8vw,2.4rem);font-weight:700;margin:12px 0 12px;letter-spacing:-.02em}
        .contact-info>p{color:#6b7280;margin-bottom:36px}
        .c-items{display:flex;flex-direction:column;gap:22px}
        .c-item{display:flex;align-items:flex-start;gap:18px}
        .c-icon{width:50px;height:50px;flex-shrink:0;border-radius:var(--radius);background:color-mix(in srgb,var(--primary) 8%,transparent);border:1px solid color-mix(in srgb,var(--primary) 14%,transparent);display:flex;align-items:center;justify-content:center;font-size:1.2rem}
        .c-lbl{font-size:.7rem;color:#9ca3af;text-transform:uppercase;letter-spacing:.1em;font-weight:700}
        .c-val{font-weight:600;color:var(--text-dark);font-size:.97rem;margin-top:3px;line-height:1.5}
        .c-form{background:var(--bg);border-radius:calc(var(--radius)*2);padding:44px;box-shadow:var(--shadow-md);border:1px solid rgba(0,0,0,.06)}
        .c-form h3{font-size:1.25rem;font-weight:700;margin-bottom:28px;font-family:var(--font-h)}
        .f-row{display:grid;grid-template-columns:1fr 1fr;gap:16px}
        .f-grp{margin-bottom:18px}
        .f-grp label{display:block;font-size:.8rem;font-weight:700;margin-bottom:8px;color:#374151;letter-spacing:.03em}
        .f-grp input,.f-grp textarea{width:100%;padding:13px 16px;border:1.5px solid rgba(0,0,0,.1);border-radius:var(--radius);font-family:var(--font-b);font-size:.92rem;color:var(--text-dark);background:var(--surface);outline:none;transition:border-color .2s,box-shadow .2s}
        .f-grp input:focus,.f-grp textarea:focus{border-color:var(--secondary);box-shadow:0 0 0 3px color-mix(in srgb,var(--secondary) 15%,transparent)}
        .f-grp textarea{resize:vertical;min-height:120px}

        /* Map */
        .map-wrap{border-radius:calc(var(--radius)*2);overflow:hidden;box-shadow:var(--shadow-lg);border:5px solid var(--bg);height:400px}
        .map-wrap iframe{width:100%;height:100%;border:none;display:block}

        /* Footer */
        .footer{background:var(--primary);color:#fff;padding:40px 0;text-align:center}
        .footer-brand{font-family:var(--font-h);font-size:1.4rem;font-weight:700;color:var(--secondary);margin-bottom:8px}
        .footer-copy{font-size:.8rem;opacity:.4}

        /* Responsive */
        @media(max-width:960px){
            .hero{grid-template-columns:1fr!important}
            .hero-img{display:none}
            .hero-inner{padding:60px 28px!important}
            .about-grid,.contact-top{grid-template-columns:1fr}
            .about-img{height:280px}
            .about-badge{display:none}
            .stats-grid{grid-template-columns:repeat(2,1fr)}
            .f-row{grid-template-columns:1fr}
            .navbar{padding:0 24px}
        }
        @media(max-width:600px){
            .section{padding:64px 0}
            .container{padding:0 20px}
            .btn-group{flex-direction:column}
            .stats-grid{grid-template-columns:1fr 1fr}
            .map-wrap{height:260px}
            .hero-inner h1{font-size:2.4rem}
        }

        /* Animations */
        .fade-up{opacity:0;transform:translateY(30px);transition:opacity .65s ease,transform .65s ease}
        .fade-up.visible{opacity:1;transform:none}
    </style>
</head>
<body>

{{-- NAVBAR --}}
<nav class="navbar" id="navbar">
    <span class="nav-brand">{{ $c['brand'] ?? 'Business' }}</span>
    <a href="#contact" class="btn {{ $navTransparent ? 'btn-outline-white' : 'btn-dark' }}" id="navCta">
        {{ $c['hero']['cta_primary'] ?? 'Contact Us' }}
    </a>
</nav>

@foreach($sections as $section)

{{-- ══ HERO ══ --}}
@if($section === 'hero' && isset($c['hero']))
@php $h = $c['hero']; @endphp
<section class="hero" id="home">
    @if(in_array($heroType, ['fullscreen','centered']))
    <div class="hero-bg"></div>
    <div class="hero-ov"></div>
    @endif

    <div class="{{ $heroType === 'split' ? 'hero-inner' : 'hero-inner container' }}">
        <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:18px">
            @if(!empty($h['badge']))
            <span class="tag tag-light">✦ {{ $h['badge'] }}</span>
            @endif
            @if(!empty($h['highlight_stat']))
            <span class="tag tag-light">⭐ {{ $h['highlight_stat'] }}</span>
            @endif
        </div>
        <h1>{{ $h['headline'] ?? $c['brand'] }}</h1>
        <p class="hero-sub">{{ $h['subheadline'] ?? '' }}</p>
        <div class="btn-group">
            @if(!empty($h['cta_primary']))
            <a href="#contact" class="btn btn-primary">{{ $h['cta_primary'] }} →</a>
            @endif
            @if(!empty($h['cta_secondary']))
            <a href="#about" class="btn btn-outline-white">{{ $h['cta_secondary'] }}</a>
            @endif
        </div>
    </div>

    @if($heroType === 'split' || $heroType === 'minimal')
    <div class="hero-img"></div>
    @endif
</section>
@endif

{{-- ══ STATS ══ --}}
@if($section === 'stats' && !empty($c['stats']))
<div class="stats-bar">
    <div class="container">
        <div class="stats-grid">
            @foreach($c['stats'] as $i => $stat)
            @php
                $icons = [
                    '<svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 21 12 17.77 5.82 21 7 14.14l-5-4.87 6.91-1.01L12 2z"/></svg>',
                    '<svg viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>',
                    '<svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>',
                    '<svg viewBox="0 0 24 24"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zm4.24 16L12 15.45 7.77 18l1.12-4.81-3.73-3.23 4.92-.42L12 5l1.92 4.53 4.92.42-3.73 3.23L16.23 18z"/></svg>',
                ];
            @endphp
            <div class="stat-item fade-up" style="transition-delay:{{ $i*80 }}ms">
                <div class="stat-icon-wrap">{!! $icons[$i % 4] !!}</div>
                <div class="stat-val">{{ $stat['value'] ?? '' }}</div>
                <div class="stat-lbl">{{ $stat['label'] ?? '' }}</div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- ══ SERVICES (with images) ══ --}}
@if($section === 'services' && !empty($c['services']['items']))
@php $sv = $c['services']; @endphp
<section class="section services" id="services">
    <div class="container">
        <div class="s-head fade-up">
            <span class="tag tag-accent">✦ What We Offer</span>
            <h2>{{ $sv['heading'] ?? 'Our Services' }}</h2>
            <p>{{ $sv['subheading'] ?? '' }}</p>
        </div>
        <div class="svc-grid">
            @foreach($sv['items'] as $i => $item)
            @php
                $svcImg = $item['image_url'] ?? null; // Pexels image from AI config

                if (!$svcImg) {
                    // Fallback: keyword match from title/description
                    $svcTitle = strtolower($item['title'] ?? '');
                    $svcDesc  = strtolower($item['description'] ?? '');
                    $svcText  = $svcTitle . ' ' . $svcDesc;
                    $svcImg   = $defaultServiceImg;
                    foreach ($serviceImages as $keyword => $imgUrl) {
                        if (str_contains($svcText, $keyword)) {
                            $svcImg = $imgUrl;
                            break;
                        }
                    }
                }
            @endphp
            <div class="svc-card fade-up" style="transition-delay:{{ $i*90 }}ms">
                <div class="svc-img" style="background-image:url('{{ $svcImg }}')"></div>
                <div class="svc-body">
                    <div class="svc-title">{{ $item['title'] ?? '' }}</div>
                    <div class="svc-desc">{{ $item['description'] ?? '' }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ══ ABOUT ══ --}}
@if($section === 'about' && isset($c['about']))
@php $ab = $c['about']; @endphp
<section class="section about" id="about">
    <div class="container">
        <div class="about-grid">
            <div class="about-img-wrap fade-up">
                <div class="about-img"></div>
                @if(!empty($c['stats'][0]))
                <div class="about-badge">
                    <span class="big">{{ $c['stats'][0]['value'] ?? '10+' }}</span>
                    <span class="sm">{{ $c['stats'][0]['label'] ?? 'Years' }}</span>
                </div>
                @endif
            </div>
            <div class="about-content fade-up">
                <span class="tag tag-accent">✦ Our Story</span>
                <h2>{{ $ab['heading'] ?? 'About Us' }}</h2>
                <p class="about-story">{{ $ab['story'] ?? '' }}</p>
                @if(!empty($ab['mission']))
                <div class="about-mission">{{ $ab['mission'] }}</div>
                @endif
                @if(!empty($ab['highlight_items']))
                <ul class="about-hl">
                    @foreach($ab['highlight_items'] as $item)
                    <li>
                        <span class="dot"></span>
                        <span>{{ $item['text'] ?? '' }}</span>
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>
        </div>
    </div>
</section>
@endif

{{-- ══ TESTIMONIALS ══ --}}
@if($section === 'testimonials' && !empty($c['testimonials']['items']))
@php $tm = $c['testimonials']; @endphp
<section class="section testimonials" id="testimonials">
    <div class="container">
        <div class="s-head fade-up">
            <span class="tag tag-accent">✦ Guest Reviews</span>
            <h2>{{ $tm['heading'] ?? 'What Our Customers Say' }}</h2>
        </div>
        <div class="t-grid">
            @foreach($tm['items'] as $i => $t)
            <div class="t-card fade-up" style="transition-delay:{{ $i*100 }}ms">
                <div class="t-quote">"</div>
                <div class="t-stars">@for($s=0;$s<($t['rating']??5);$s++) ★ @endfor</div>
                <p class="t-text">"{{ $t['text'] ?? '' }}"</p>
                <div class="t-author">
                    <div class="t-avatar">{{ $t['avatar_letter'] ?? strtoupper(substr($t['name']??'A',0,1)) }}</div>
                    <div>
                        <div class="t-name">{{ $t['name'] ?? '' }}</div>
                        <div class="t-role">{{ $t['role'] ?? '' }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ══ CTA ══ --}}
@if($section === 'cta' && isset($c['cta']))
@php $cta = $c['cta']; @endphp
<section class="cta-sec">
    <h2>{{ $cta['heading'] ?? 'Ready to Get Started?' }}</h2>
    <p>{{ $cta['subheading'] ?? '' }}</p>
    <a href="#contact" class="btn btn-white" style="font-size:.95rem;padding:16px 44px">
        {{ $cta['button'] ?? 'Contact Us' }} →
    </a>
</section>
@endif

{{-- ══ CONTACT + MAP ══ --}}
@if($section === 'contact' && isset($c['contact']))
@php $ct = $c['contact']; @endphp
<section class="section contact" id="contact">
    <div class="container">
        <div class="contact-top">
            <div class="contact-info fade-up">
                <span class="tag tag-accent">✦ Contact</span>
                <h2>{{ $ct['heading'] ?? 'Get In Touch' }}</h2>
                <p>{{ $ct['subheading'] ?? '' }}</p>
                <div class="c-items">
                    @if(!empty($ct['phone']))
                    <div class="c-item">
                        <div class="c-icon">📞</div>
                        <div><div class="c-lbl">Phone</div><div class="c-val">{{ $ct['phone'] }}</div></div>
                    </div>
                    @endif
                    @if(!empty($ct['address']))
                    <div class="c-item">
                        <div class="c-icon">📍</div>
                        <div><div class="c-lbl">Address</div><div class="c-val">{{ $ct['address'] }}</div></div>
                    </div>
                    @endif
                    @if(!empty($ct['email']))
                    <div class="c-item">
                        <div class="c-icon">✉️</div>
                        <div><div class="c-lbl">Email</div><div class="c-val">{{ $ct['email'] }}</div></div>
                    </div>
                    @endif
                    @if(!empty($ct['hours']))
                    <div class="c-item">
                        <div class="c-icon">🕐</div>
                        <div><div class="c-lbl">Hours</div><div class="c-val">{{ $ct['hours'] }}</div></div>
                    </div>
                    @endif
                </div>
            </div>
            <div class="c-form fade-up">
                <h3>Send Us a Message</h3>
                <div class="f-row">
                    <div class="f-grp"><label>Your Name</label><input type="text" placeholder="John Doe"></div>
                    <div class="f-grp"><label>Email Address</label><input type="email" placeholder="john@example.com"></div>
                </div>
                <div class="f-grp"><label>Phone Number</label><input type="tel" placeholder="+91 00000 00000"></div>
                <div class="f-grp"><label>Message</label><textarea placeholder="How can we help you?"></textarea></div>
                <button class="btn btn-dark" style="width:100%;justify-content:center;font-size:.92rem;padding:15px">Send Message →</button>
            </div>
        </div>

        @if(!empty($ct['address']))
        <div class="map-wrap fade-up">
            <iframe src="{{ $mapsEmbedUrl }}" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Location Map"></iframe>
        </div>
        @endif
    </div>
</section>
@endif

@endforeach

<footer class="footer">
    <div class="footer-brand">{{ $c['brand'] ?? 'Business' }}</div>
    <div class="footer-copy">© {{ date('Y') }} {{ $c['brand'] ?? 'Business' }}. All rights reserved.</div>
</footer>

<script>
const nav = document.getElementById('navbar');
window.addEventListener('scroll', () => nav.classList.toggle('scrolled', window.scrollY > 60), {passive:true});

const io = new IntersectionObserver(entries => {
    entries.forEach(e => { if(e.isIntersecting){e.target.classList.add('visible');io.unobserve(e.target)} });
}, {threshold:.1});
document.querySelectorAll('.fade-up').forEach(el => io.observe(el));
</script>
</body>
</html>