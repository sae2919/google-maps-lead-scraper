<section style="padding:40px">
    <h2>{{ $data['title'] ?? ucfirst('emergency') }}</h2>

    @if(isset($data['items']))
        <ul>
            @foreach($data['items'] as $item)
                <li>{{ is_array($item) ? ($item['title'] ?? json_encode($item)) : $item }}</li>
            @endforeach
        </ul>
    @endif

    @if(isset($data['body']))
        <p>{{ $data['body'] }}</p>
    @endif

    @if(isset($data['content']))
        <p>{{ $data['content'] }}</p>
    @endif
</section>