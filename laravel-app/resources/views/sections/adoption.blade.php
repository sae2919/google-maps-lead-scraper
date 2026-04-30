<section class="container">
    <h2>{{ $content['title'] }}</h2>

    @foreach($content['items'] as $pet)
        <div class="card">
            <h3>{{ $pet['name'] }}</h3>
            <p>{{ $pet['description'] }}</p>
        </div>
    @endforeach
</section>