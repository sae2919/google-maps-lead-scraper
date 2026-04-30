<div class="container" style="display:grid;grid-template-columns:1fr 1fr;gap:50px;align-items:center;">

    @if(!empty($images[1]))
        <img src="{{ $images[1] }}">
    @endif

    <div>
        <h2>{{ $content['title'] ?? 'About Us' }}</h2>

        <p style="color:#64748b;">
            {{ $content['body'] ?? 'About content...' }}
        </p>
    </div>

</div>