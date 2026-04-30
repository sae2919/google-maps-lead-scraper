<div class="container" style="text-align:center;">

    <h1 style="font-size:48px;font-weight:800;">
        {{ $content['title'] ?? $lead->name ?? 'Welcome' }}
    </h1>

    <p style="color:#64748b;margin:20px 0;">
        {{ $content['tagline'] ?? '' }}
    </p>

    @if(isset($content['cta_text']))
        <a href="#contact" style="
            background:var(--primary);
            color:#fff;
            padding:12px 25px;
            border-radius:10px;
            text-decoration:none;
            font-weight:600;
        ">
            {{ $content['cta_text'] }}
        </a>
    @endif

    @if(!empty($images[0]))
        <div style="margin-top:40px;">
            <img src="{{ $images[0] }}">
        </div>
    @endif

</div>