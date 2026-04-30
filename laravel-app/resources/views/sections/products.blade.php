<section class="container">
    <h2>{{ $content['title'] }}</h2>

    <div class="grid">
        @foreach($content['items'] as $item)
            <div class="card">
                <h3>{{ $item['name'] }}</h3>
                <p>{{ $item['category'] }}</p>
                <strong>{{ $item['price'] }}</strong>
            </div>
        @endforeach
    </div>
</section>