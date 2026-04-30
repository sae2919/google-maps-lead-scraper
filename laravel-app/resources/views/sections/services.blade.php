<div class="container">

    <h2 style="text-align:center;margin-bottom:40px;">
        {{ $content['title'] ?? 'Our Services' }}
    </h2>

    <div style="
        display:grid;
        grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
        gap:20px;
    ">

        @foreach($content['items'] ?? [] as $item)
            <div style="
                padding:25px;
                border:1px solid #e2e8f0;
                border-radius:15px;
            ">
                <h3>{{ $item['title'] ?? 'Service' }}</h3>
                <p style="color:#64748b;">
                    {{ $item['desc'] ?? '' }}
                </p>
            </div>
        @endforeach

    </div>

</div>