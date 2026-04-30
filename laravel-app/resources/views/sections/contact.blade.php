<div class="container">

    <h2 style="text-align:center;margin-bottom:40px;">
        {{ $content['title'] ?? 'Contact Us' }}
    </h2>

    <div style="
        max-width:600px;
        margin:auto;
        text-align:center;
        padding:30px;
        border:1px solid #eee;
        border-radius:20px;
    ">

        <p style="margin-bottom:10px;">📍 {{ $lead->address }}</p>
        <p style="margin-bottom:20px;">📞 {{ $lead->phone }}</p>

        <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">

            <a href="tel:{{ $lead->phone }}" style="
                padding:10px 20px;
                background:var(--primary);
                color:white;
                border-radius:10px;
                text-decoration:none;
            ">
                Call
            </a>

            <a href="https://wa.me/{{ preg_replace('/[^0-9]/','',$lead->phone) }}" target="_blank" style="
                padding:10px 20px;
                background:#25D366;
                color:white;
                border-radius:10px;
                text-decoration:none;
            ">
                WhatsApp
            </a>

            <a href="https://maps.google.com?q={{ urlencode($lead->address) }}" target="_blank" style="
                padding:10px 20px;
                background:#111;
                color:white;
                border-radius:10px;
                text-decoration:none;
            ">
                Directions
            </a>

        </div>

    </div>

</div>