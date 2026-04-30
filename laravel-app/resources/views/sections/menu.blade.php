<section>
    <h2>{{ ucfirst($data['type']) }}</h2>

    @if(isset($data['items']))
        <ul>
            @foreach($data['items'] as $item)
                <li>{{ $item }}</li>
            @endforeach
        </ul>
    @endif

    @if(isset($data['content']))
        <p>{{ $data['content'] }}</p>
    @endif
</section>