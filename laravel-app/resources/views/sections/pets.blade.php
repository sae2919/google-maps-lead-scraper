<section class="container">
    <h2>{{ $content['title'] }}</h2>
    <p>{{ $content['body'] }}</p>

    <div class="grid">
        @foreach($content['items'] as $pet)
            <div class="card">
                <h3>{{ $pet['name'] }}</h3>
                <p>{{ $pet['breed'] }}</p>
                <small>{{ $pet['age'] }}</small>
            </div>
        @endforeach
    </div>
</section>