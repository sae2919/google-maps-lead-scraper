<section style="padding: 100px 0; background: #ffffff;">
    <div style="max-width: 1200px; margin: 0 auto; padding: 0 30px;">
        <h2 style="text-align: center; font-size: 36px; margin-bottom: 60px;">{{ $content['title'] ?? 'Gallery' }}</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
            {{-- We use the images array from the controller --}}
            @foreach(array_slice($images, 1, 6) as $index => $imageUrl)
                <div style="border-radius: 16px; overflow: hidden; height: 300px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                    <img src="{{ $imageUrl }}" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                </div>
            @endforeach
        </div>
    </div>
</section>