<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Website Builder</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        /* ── RESET & TOKENS ── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --ink:     #08090d;
            --surface: #0f1117;
            --panel:   #161820;
            --card:    #1c1e28;
            --border:  rgba(255,255,255,0.07);
            --border-h:rgba(255,255,255,0.18);
            --gold:    #e8c97a;
            --gold-d:  #b89a3e;
            --text:    #eef0f6;
            --muted:   #6b7280;
            --dim:     #374151;
            --r:       12px;
        }

        html { scroll-behavior: smooth; }

        body {
            background: var(--ink);
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ── ANIMATED BACKGROUND ── */
        .bg-orbs {
            position: fixed; inset: 0; pointer-events: none; z-index: 0; overflow: hidden;
        }
        .orb {
            position: absolute; border-radius: 50%; filter: blur(100px); opacity: 0.12;
            animation: drift 18s ease-in-out infinite alternate;
        }
        .orb-1 { width: 600px; height: 600px; background: #6366f1; top: -200px; left: -150px; animation-delay: 0s; }
        .orb-2 { width: 500px; height: 500px; background: #e8c97a; bottom: -200px; right: -100px; animation-delay: -6s; }
        .orb-3 { width: 400px; height: 400px; background: #ec4899; top: 40%; left: 50%; animation-delay: -12s; }

        @keyframes drift {
            from { transform: translate(0, 0) scale(1); }
            to   { transform: translate(40px, 30px) scale(1.08); }
        }

        /* ── LAYOUT ── */
        .shell {
            position: relative; z-index: 1;
            display: grid;
            grid-template-columns: 380px 1fr;
            min-height: 100vh;
        }

        @media (max-width: 900px) {
            .shell { grid-template-columns: 1fr; }
            .sidebar { display: none; }
        }

        /* ── SIDEBAR ── */
        .sidebar {
            background: linear-gradient(160deg, #0f1117 0%, #13141d 100%);
            border-right: 1px solid var(--border);
            padding: 52px 44px;
            display: flex; flex-direction: column; gap: 48px;
            position: sticky; top: 0; height: 100vh; overflow: hidden;
        }

        .sidebar-brand { display: flex; align-items: center; gap: 12px; }
        .brand-icon {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, var(--gold), #f5a623);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
        }
        .brand-name {
            font-family: 'Syne', sans-serif;
            font-size: 18px; font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--text);
        }

        .sidebar-headline {
            font-family: 'Syne', sans-serif;
            font-size: 2rem; font-weight: 800;
            line-height: 1.15;
            letter-spacing: -0.04em;
        }

        .sidebar-headline em {
            font-style: normal;
            background: linear-gradient(90deg, var(--gold), #f5a623);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .sidebar-desc {
            color: var(--muted);
            font-size: 14px; line-height: 1.7;
            font-weight: 300;
        }

        .feature-list { display: flex; flex-direction: column; gap: 16px; }

        .feature {
            display: flex; align-items: flex-start; gap: 14px;
            padding: 16px;
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--border);
            border-radius: var(--r);
            transition: border-color 0.2s;
        }

        .feature:hover { border-color: var(--border-h); }

        .feature-icon {
            width: 36px; height: 36px; flex-shrink: 0;
            background: rgba(232,201,122,0.1);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px;
        }

        .feature-text strong {
            display: block; font-size: 13px; font-weight: 600;
            color: var(--text); margin-bottom: 2px;
        }

        .feature-text span { font-size: 12px; color: var(--muted); }

        .stat-row {
            display: flex; gap: 24px;
            padding-top: 8px;
            border-top: 1px solid var(--border);
        }

        .stat strong {
            display: block;
            font-family: 'Syne', sans-serif;
            font-size: 22px; font-weight: 800;
            color: var(--gold);
        }

        .stat span { font-size: 11px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.07em; }

        /* ── MAIN CONTENT ── */
        .main {
            padding: 52px 56px;
            display: flex; flex-direction: column;
        }

        @media (max-width: 640px) { .main { padding: 32px 20px; } }

        /* ── PROGRESS ── */
        .progress-bar {
            display: flex; align-items: center; gap: 0;
            margin-bottom: 52px;
        }

        .step-node {
            display: flex; flex-direction: column; align-items: center; gap: 6px;
            cursor: pointer;
        }

        .step-dot {
            width: 32px; height: 32px; border-radius: 50%;
            border: 2px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 700;
            color: var(--muted);
            transition: all 0.3s ease;
        }

        .step-node.active .step-dot {
            background: var(--gold);
            border-color: var(--gold);
            color: #000;
        }

        .step-node.done .step-dot {
            background: rgba(232,201,122,0.15);
            border-color: var(--gold-d);
            color: var(--gold);
        }

        .step-label {
            font-size: 11px; font-weight: 500;
            color: var(--muted);
            white-space: nowrap;
            letter-spacing: 0.04em;
        }

        .step-node.active .step-label { color: var(--text); }

        .step-connector {
            flex: 1; height: 1px;
            background: var(--border);
            margin: 0 8px;
            margin-bottom: 22px;
            transition: background 0.3s;
        }

        .step-connector.done { background: var(--gold-d); }

        /* ── STEP PANELS ── */
        .step-panel {
            display: none;
            animation: fadeSlide 0.35s ease;
        }

        .step-panel.active { display: block; }

        @keyframes fadeSlide {
            from { opacity: 0; transform: translateX(20px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        .step-heading {
            font-family: 'Syne', sans-serif;
            font-size: 1.8rem; font-weight: 800;
            letter-spacing: -0.04em;
            margin-bottom: 6px;
        }

        .step-sub {
            color: var(--muted); font-size: 14px;
            margin-bottom: 36px; font-weight: 300;
        }

        /* ── CATEGORY GRID ── */
        .cat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
            gap: 10px;
            margin-bottom: 36px;
        }

        .cat-card {
            background: var(--card);
            border: 1.5px solid var(--border);
            border-radius: var(--r);
            padding: 20px 14px 16px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            user-select: none;
            position: relative; overflow: hidden;
        }

        .cat-card::before {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(232,201,122,0.08), transparent);
            opacity: 0; transition: opacity 0.2s;
        }

        .cat-card:hover { border-color: rgba(232,201,122,0.4); transform: translateY(-3px); }
        .cat-card:hover::before { opacity: 1; }

        .cat-card.selected {
            border-color: var(--gold);
            background: rgba(232,201,122,0.08);
            box-shadow: 0 0 0 1px var(--gold-d), 0 8px 32px rgba(232,201,122,0.15);
        }

        .cat-card.selected::before { opacity: 1; }

        .cat-icon { font-size: 28px; display: block; margin-bottom: 10px; }

        .cat-name {
            font-size: 11.5px; font-weight: 600;
            color: var(--muted); line-height: 1.3;
            transition: color 0.2s;
        }

        .cat-card:hover .cat-name,
        .cat-card.selected .cat-name { color: var(--text); }

        .cat-check {
            position: absolute; top: 8px; right: 8px;
            width: 16px; height: 16px;
            background: var(--gold);
            border-radius: 50%;
            display: none; align-items: center; justify-content: center;
            font-size: 9px; color: #000;
        }

        .cat-card.selected .cat-check { display: flex; }

        /* ── CUSTOM INPUT ── */
        .custom-row { margin-bottom: 36px; display: none; }
        .custom-row.show { display: block; }

        /* ── FORM FIELDS ── */
        .field-grid {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 16px; margin-bottom: 36px;
        }

        @media (max-width: 580px) { .field-grid { grid-template-columns: 1fr; } }

        .field { display: flex; flex-direction: column; gap: 7px; }
        .field.span-2 { grid-column: 1 / -1; }

        .field label {
            font-size: 11px; font-weight: 600;
            letter-spacing: 0.08em; text-transform: uppercase;
            color: var(--muted);
        }

        .field input {
            background: var(--card);
            border: 1.5px solid var(--border);
            border-radius: 10px;
            padding: 13px 16px;
            font-size: 14px; font-weight: 400;
            font-family: 'DM Sans', sans-serif;
            color: var(--text);
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            width: 100%;
        }

        .field input:focus {
            border-color: var(--gold-d);
            box-shadow: 0 0 0 3px rgba(232,201,122,0.1);
        }

        .field input::placeholder { color: var(--dim); }

        /* ── STYLE CARDS ── */
        .style-grid {
            display: grid; grid-template-columns: repeat(3, 1fr);
            gap: 12px; margin-bottom: 36px;
        }

        @media (max-width: 580px) { .style-grid { grid-template-columns: repeat(2, 1fr); } }

        .style-card {
            border: 1.5px solid var(--border);
            border-radius: var(--r);
            padding: 20px 16px;
            cursor: pointer;
            transition: all 0.2s ease;
            user-select: none;
        }

        .style-card:hover { border-color: var(--border-h); transform: translateY(-2px); }

        .style-card.selected {
            border-color: var(--gold);
            background: rgba(232,201,122,0.06);
            box-shadow: 0 0 0 1px var(--gold-d);
        }

        .style-swatch {
            display: flex; gap: 4px; margin-bottom: 10px;
        }

        .swatch { width: 16px; height: 16px; border-radius: 50%; }

        .style-name { font-size: 13px; font-weight: 600; margin-bottom: 3px; }
        .style-desc { font-size: 11px; color: var(--muted); }

        /* ── PREVIEW CARD ── */
        .preview-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--r);
            padding: 20px 24px;
            margin-bottom: 32px;
            display: flex; align-items: center; gap: 16px;
        }

        .preview-icon { font-size: 32px; }

        .preview-name {
            font-family: 'Syne', sans-serif;
            font-size: 18px; font-weight: 800;
        }

        .preview-meta { font-size: 12px; color: var(--muted); margin-top: 3px; }

        /* ── BUTTONS ── */
        .btn-row {
            display: flex; gap: 12px; align-items: center;
        }

        .btn-back {
            padding: 13px 24px;
            background: transparent;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            color: var(--muted);
            font-family: 'DM Sans', sans-serif;
            font-size: 14px; font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-back:hover { border-color: var(--border-h); color: var(--text); }

        .btn-next {
            flex: 1;
            padding: 15px 28px;
            background: linear-gradient(135deg, #e8c97a, #d4a53b);
            border: none; border-radius: 10px;
            color: #0a0800;
            font-family: 'DM Sans', sans-serif;
            font-size: 15px; font-weight: 700;
            cursor: pointer;
            transition: all 0.25s ease;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            position: relative; overflow: hidden;
        }

        .btn-next::after {
            content: '';
            position: absolute; inset: 0;
            background: rgba(255,255,255,0.15);
            opacity: 0; transition: opacity 0.2s;
        }

        .btn-next:hover::after { opacity: 1; }
        .btn-next:hover { transform: translateY(-2px); box-shadow: 0 12px 32px rgba(232,201,122,0.3); }
        .btn-next:active { transform: translateY(0); }

        /* ── GENERATE BTN ── */
        .btn-generate {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #e8c97a, #d4a53b);
            border: none; border-radius: 12px;
            color: #0a0800;
            font-family: 'DM Sans', sans-serif;
            font-size: 16px; font-weight: 700;
            cursor: pointer;
            transition: all 0.25s ease;
            display: flex; align-items: center; justify-content: center; gap: 10px;
            position: relative; overflow: hidden;
        }

        .btn-generate:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 40px rgba(232,201,122,0.35);
        }

        /* ── LOADING ── */
        .btn-generate.loading span { display: none; }

        .btn-generate.loading::before {
            content: 'Generating your website...';
            font-size: 15px; font-weight: 700;
        }

        .btn-generate.loading::after {
            content: '';
            width: 18px; height: 18px;
            border: 2px solid rgba(0,0,0,0.3);
            border-top-color: #000;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        /* ── ERRORS ── */
        .error-box {
            background: rgba(239,68,68,0.08);
            border: 1px solid rgba(239,68,68,0.25);
            border-radius: 10px;
            padding: 14px 18px;
            margin-bottom: 24px;
            font-size: 13px;
            color: #fca5a5;
        }

        /* ── HINT LINE ── */
        .hint {
            text-align: center;
            margin-top: 18px;
            font-size: 12px;
            color: var(--dim);
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }

        .hint::before, .hint::after {
            content: ''; flex: 1; height: 1px;
            background: var(--border);
        }
    </style>
</head>
<body>

<div class="bg-orbs">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
</div>

<div class="shell">

    {{-- ── SIDEBAR ── --}}
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon">✦</div>
            <span class="brand-name">SiteForge AI</span>
        </div>

        <div>
            <h1 class="sidebar-headline">
                Build a <em>stunning</em><br>website in seconds.
            </h1>
            <p class="sidebar-desc" style="margin-top:14px;">
                Answer 3 quick questions. Our AI designs and writes
                a complete, professional website — specific to your business.
            </p>
        </div>

        <div class="feature-list">
            <div class="feature">
                <div class="feature-icon">🎨</div>
                <div class="feature-text">
                    <strong>Category-aware design</strong>
                    <span>Restaurant, hospital, gym — each gets its own visual identity</span>
                </div>
            </div>
            <div class="feature">
                <div class="feature-icon">📸</div>
                <div class="feature-text">
                    <strong>Real images, auto-fetched</strong>
                    <span>Pexels pulls professional photos for your business type</span>
                </div>
            </div>
            <div class="feature">
                <div class="feature-icon">🔗</div>
                <div class="feature-text">
                    <strong>Shareable URL instantly</strong>
                    <span>Get a permanent link — share with customers right away</span>
                </div>
            </div>
        </div>

        <div class="stat-row">
            <div class="stat">
                <strong>12+</strong>
                <span>Business types</span>
            </div>
            <div class="stat">
                <strong>~8s</strong>
                <span>Generation time</span>
            </div>
            <div class="stat">
                <strong>100%</strong>
                <span>AI-written copy</span>
            </div>
        </div>
    </aside>

    {{-- ── MAIN ── --}}
    <main class="main">

        {{-- Progress --}}
        <div class="progress-bar">
            <div class="step-node active" id="node-1">
                <div class="step-dot">1</div>
                <span class="step-label">Category</span>
            </div>
            <div class="step-connector" id="conn-1"></div>
            <div class="step-node" id="node-2">
                <div class="step-dot">2</div>
                <span class="step-label">Details</span>
            </div>
            <div class="step-connector" id="conn-2"></div>
            <div class="step-node" id="node-3">
                <div class="step-dot">3</div>
                <span class="step-label">Style</span>
            </div>
        </div>

        @if($errors->any())
            <div class="error-box">
                @foreach($errors->all() as $e) ⚠ {{ $e }}<br> @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('generate.submit') }}" id="genForm">
            @csrf
            <input type="hidden" name="category"     id="categoryInput" value="{{ old('category') }}">
            <input type="hidden" name="color_scheme" id="colorInput"    value="auto">

            {{-- ════════════════════════════
                 STEP 1 — CATEGORY
            ════════════════════════════ --}}
            <div class="step-panel active" id="step-1">
                <h2 class="step-heading">What kind of business?</h2>
                <p class="step-sub">Pick the type that best describes your business.</p>

                @php
                $cats = [
                    ['key'=>'restaurant',  'icon'=>'🍽️', 'name'=>'Restaurant'],
                    ['key'=>'hospital',    'icon'=>'🏥', 'name'=>'Hospital'],
                    ['key'=>'gym',         'icon'=>'💪', 'name'=>'Gym'],
                    ['key'=>'hotel',       'icon'=>'🏨', 'name'=>'Hotel'],
                    ['key'=>'salon',       'icon'=>'✂️', 'name'=>'Salon'],
                    ['key'=>'pet_store',   'icon'=>'🐾', 'name'=>'Pet Store'],
                    ['key'=>'school',      'icon'=>'📚', 'name'=>'School'],
                    ['key'=>'pharmacy',    'icon'=>'💊', 'name'=>'Pharmacy'],
                    ['key'=>'retail',      'icon'=>'🛍️', 'name'=>'Retail'],
                    ['key'=>'law_firm',    'icon'=>'⚖️', 'name'=>'Law Firm'],
                    ['key'=>'real_estate', 'icon'=>'🏢', 'name'=>'Real Estate'],
                    ['key'=>'custom',      'icon'=>'✏️', 'name'=>'Custom'],
                ];
                @endphp

                <div class="cat-grid">
                    @foreach($cats as $c)
                        <div class="cat-card {{ old('category') === $c['key'] ? 'selected' : '' }}"
                             data-key="{{ $c['key'] }}"
                             onclick="selectCat(this)">
                            <div class="cat-check">✓</div>
                            <span class="cat-icon">{{ $c['icon'] }}</span>
                            <div class="cat-name">{{ $c['name'] }}</div>
                        </div>
                    @endforeach
                </div>

                <div class="custom-row" id="customRow">
                    <div class="field">
                        <label>Describe your business type</label>
                        <input type="text" id="customText"
                               placeholder="e.g. Photography Studio, Bakery, Co-working Space"
                               oninput="document.getElementById('categoryInput').value = this.value">
                    </div>
                </div>

                <div class="btn-row">
                    <button type="button" class="btn-next" onclick="goStep(2)">
                        <span>Continue</span> →
                    </button>
                </div>
            </div>

            {{-- ════════════════════════════
                 STEP 2 — DETAILS
            ════════════════════════════ --}}
            <div class="step-panel" id="step-2">
                <h2 class="step-heading">Tell us about your business.</h2>
                <p class="step-sub">This information appears on your website.</p>

                <div class="field-grid">
                    <div class="field span-2">
                        <label>Business Name *</label>
                        <input type="text" name="business_name" id="nameInput"
                               placeholder="e.g. Green Leaf Restaurant"
                               value="{{ old('business_name') }}"
                               oninput="updatePreview()"
                               required>
                    </div>
                    <div class="field">
                        <label>City / Location</label>
                        <input type="text" name="city"
                               placeholder="e.g. Hyderabad"
                               value="{{ old('city') }}">
                    </div>
                    <div class="field">
                        <label>Phone Number</label>
                        <input type="tel" name="phone"
                               placeholder="e.g. 9000000000"
                               value="{{ old('phone') }}">
                    </div>
                    <div class="field span-2">
                        <label>Full Address (optional)</label>
                        <input type="text" name="address"
                               placeholder="e.g. 12, MG Road, Banjara Hills, Hyderabad"
                               value="{{ old('address') }}">
                    </div>
                </div>

                <div class="btn-row">
                    <button type="button" class="btn-back" onclick="goStep(1)">← Back</button>
                    <button type="button" class="btn-next" onclick="goStep(3)">
                        <span>Continue</span> →
                    </button>
                </div>
            </div>

            {{-- ════════════════════════════
                 STEP 3 — STYLE + GENERATE
            ════════════════════════════ --}}
            <div class="step-panel" id="step-3">
                <h2 class="step-heading">Choose a visual style.</h2>
                <p class="step-sub">Sets the color palette and mood of your website.</p>

                <div class="style-grid">
                    @php
                    $styles = [
                        ['key'=>'auto',   'name'=>'Auto',       'desc'=>'AI picks the best',   'colors'=>['#6366f1','#8b5cf6','#a78bfa']],
                        ['key'=>'dark',   'name'=>'Dark Bold',  'desc'=>'Black & vivid accent', 'colors'=>['#111','#ef4444','#fbbf24']],
                        ['key'=>'light',  'name'=>'Clean Light','desc'=>'White & professional', 'colors'=>['#fff','#3b82f6','#1e40af']],
                        ['key'=>'warm',   'name'=>'Warm Earthy','desc'=>'Browns & oranges',     'colors'=>['#fef3c7','#f97316','#b45309']],
                        ['key'=>'green',  'name'=>'Fresh Green','desc'=>'Nature & health',      'colors'=>['#f0fdf4','#22c55e','#166534']],
                        ['key'=>'luxury', 'name'=>'Luxury Gold','desc'=>'Black & gold premium', 'colors'=>['#0a0a0a','#e8c97a','#b89a3e']],
                    ];
                    @endphp

                    @foreach($styles as $s)
                        <div class="style-card {{ $loop->first ? 'selected' : '' }}"
                             data-color="{{ $s['key'] }}"
                             onclick="selectStyle(this)">
                            <div class="style-swatch">
                                @foreach($s['colors'] as $c)
                                    <div class="swatch" style="background:{{ $c }}"></div>
                                @endforeach
                            </div>
                            <div class="style-name">{{ $s['name'] }}</div>
                            <div class="style-desc">{{ $s['desc'] }}</div>
                        </div>
                    @endforeach
                </div>

                {{-- Preview --}}
                <div class="preview-card" id="previewCard">
                    <div class="preview-icon" id="previewIcon">🏢</div>
                    <div>
                        <div class="preview-name" id="previewName">Your Business</div>
                        <div class="preview-meta" id="previewMeta">Category · AI-generated website</div>
                    </div>
                </div>

                <div class="btn-row" style="flex-direction:column; gap:12px;">
                    <button type="button" class="btn-back" style="width:100%; text-align:center;"
                            onclick="goStep(2)">← Back</button>
                    <button type="submit" class="btn-generate" id="genBtn">
                        <span>✦ Generate My Website</span>
                    </button>
                </div>

                <p class="hint">⚡ Takes 8–15 seconds &nbsp;·&nbsp; Powered by Gemini AI &nbsp;·&nbsp; Permanent shareable link</p>
            </div>

        </form>
    </main>
</div>

<script>
    const catIcons = {
        restaurant:'🍽️', hospital:'🏥', gym:'💪', hotel:'🏨', salon:'✂️',
        pet_store:'🐾', school:'📚', pharmacy:'💊', retail:'🛍️',
        law_firm:'⚖️', real_estate:'🏢', custom:'✏️'
    };

    let currentStep = 1;
    let selectedCat = '{{ old("category") }}' || '';
    let selectedCatName = '';

    function goStep(n) {
        // Validate before advancing
        if (n > currentStep) {
            if (currentStep === 1 && !selectedCat) {
                alert('Please select a business category first.');
                return;
            }
            if (currentStep === 2) {
                const name = document.querySelector('input[name="business_name"]').value.trim();
                if (!name) {
                    alert('Please enter your business name.');
                    return;
                }
            }
        }

        // Hide current
        document.getElementById('step-' + currentStep).classList.remove('active');
        document.getElementById('node-' + currentStep).classList.remove('active');

        if (n > currentStep) {
            document.getElementById('node-' + currentStep).classList.add('done');
            if (currentStep < 3) {
                document.getElementById('conn-' + currentStep).classList.add('done');
            }
        }

        currentStep = n;

        // Show new
        document.getElementById('step-' + currentStep).classList.add('active');
        document.getElementById('node-' + currentStep).classList.add('active');
        document.getElementById('node-' + currentStep).classList.remove('done');

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function selectCat(el) {
        document.querySelectorAll('.cat-card').forEach(c => c.classList.remove('selected'));
        el.classList.add('selected');

        const key = el.dataset.key;
        selectedCat = key;
        selectedCatName = el.querySelector('.cat-name').textContent;

        if (key === 'custom') {
            document.getElementById('customRow').classList.add('show');
            document.getElementById('categoryInput').value = '';
            document.getElementById('customText').focus();
        } else {
            document.getElementById('customRow').classList.remove('show');
            document.getElementById('categoryInput').value = key;
        }

        updatePreview();
    }

    function updatePreview() {
        const name = document.getElementById('nameInput')?.value || 'Your Business';
        const icon = catIcons[selectedCat] || '🏢';
        const catLabel = selectedCatName || (selectedCat.replace(/_/g, ' '));

        document.getElementById('previewIcon').textContent = icon;
        document.getElementById('previewName').textContent = name || 'Your Business';
        document.getElementById('previewMeta').textContent =
            (catLabel ? catLabel.charAt(0).toUpperCase() + catLabel.slice(1) : 'Business')
            + ' · AI-generated website';
    }

    function selectStyle(el) {
        document.querySelectorAll('.style-card').forEach(c => c.classList.remove('selected'));
        el.classList.add('selected');
        document.getElementById('colorInput').value = el.dataset.color;
    }

    // Submit loading state
    document.getElementById('genForm').addEventListener('submit', function(e) {
        const cat = document.getElementById('categoryInput').value.trim();
        const name = document.querySelector('input[name="business_name"]').value.trim();

        if (!cat || !name) { e.preventDefault(); return; }

        document.getElementById('genBtn').classList.add('loading');
    });

    // Restore category on validation error
    if (selectedCat) {
        const card = document.querySelector(`.cat-card[data-key="${selectedCat}"]`);
        if (card) { card.classList.add('selected'); updatePreview(); }
    }
</script>
</body>
</html>