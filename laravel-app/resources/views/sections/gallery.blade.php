<div class="container">

    <h2 style="text-align:center;margin-bottom:40px;">
        {{ $content['title'] ?? 'Highlights' }}
    </h2>

    {{-- 🔥 IMAGE SHOWCASE (MODERN GRID) --}}
    <div style="
        display:grid;
        grid-template-columns:2fr 1fr;
        gap:20px;
        margin-bottom:50px;
    ">

        {{-- BIG IMAGE --}}
        @if(isset($images[0]))
            <img src="{{ $images[0] }}" style="
                width:100%;
                height:400px;
                object-fit:cover;
                border-radius:20px;
            ">
        @endif

        {{-- SMALL IMAGES --}}
        <div style="display:grid;gap:20px;">
            @foreach(array_slice($images ?? [], 1, 2) as $img)
                <img src="{{ $img }}" style="
                    width:100%;
                    height:190px;
                    object-fit:cover;
                    border-radius:20px;
                ">
            @endforeach
        </div>
    </div>

    {{-- 🔥 OUTLET CARD --}}
    <div style="
        display:grid;
        grid-template-columns:1fr 1fr;
        gap:40px;
        align-items:center;
    ">

        {{-- 📍 MAP --}}
        @if(!empty($lead->address))
            <iframe
                width="100%"
                height="350"
                style="border:0;border-radius:20px;"
                loading="lazy"
                allowfullscreen
                src="https://maps.google.com/maps?q={{ urlencode($lead->address) }}&output=embed">
            </iframe>
        @endif

        {{-- 🔥 BUSINESS DETAILS --}}
        <div>

            <h3 style="font-size:28px;margin-bottom:15px;">
                {{ $lead->name }}
            </h3>

            <p style="color:#64748b;margin-bottom:10px;">
                📍 {{ $lead->address }}
            </p>

            <p style="color:#64748b;margin-bottom:20px;">
                📞 {{ $lead->phone }}
            </p>

            {{-- 🔥 ACTION BUTTONS --}}
            <div style="display:flex;gap:15px;flex-wrap:wrap;">

                {{-- CALL --}}
                <a href="tel:{{ $lead->phone }}" style="
                    padding:12px 20px;
                    background:var(--primary);
                    color:white;
                    border-radius:10px;
                    text-decoration:none;
                    font-weight:600;
                ">
                    📞 Call
                </a>

                {{-- MAP --}}
                <a href="https://www.google.com/maps?q={{ urlencode($lead->address) }}" target="_blank" style="
                    padding:12px 20px;
                    background:#111;
                    color:white;
                    border-radius:10px;
                    text-decoration:none;
                    font-weight:600;
                ">
                    📍 Directions
                </a>

                {{-- WHATSAPP --}}
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/','',$lead->phone) }}" target="_blank" style="
                    padding:12px 20px;
                    background:#25D366;
                    color:white;
                    border-radius:10px;
                    text-decoration:none;
                    font-weight:600;
                ">
                    💬 WhatsApp
                </a>

            </div>

        </div>

    </div>

</div>