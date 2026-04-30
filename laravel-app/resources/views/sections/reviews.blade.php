<div class="container">

    <h2 style="text-align:center;margin-bottom:40px;">Customer Reviews</h2>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px;">

        @php
            $reviews = [
                ['name'=>'Rahul','text'=>'Amazing service and friendly staff!','rating'=>5],
                ['name'=>'Sneha','text'=>'Very professional and clean environment.','rating'=>4],
                ['name'=>'Arjun','text'=>'Highly recommend this place!','rating'=>5],
            ];
        @endphp

        @foreach($reviews as $r)
            <div style="padding:20px;border:1px solid #eee;border-radius:15px;">
                <strong>{{ $r['name'] }}</strong>
                <p style="color:#64748b;">{{ $r['text'] }}</p>

                <div>
                    @for($i=0;$i<$r['rating'];$i++)
                        ⭐
                    @endfor
                </div>
            </div>
        @endforeach

    </div>

</div>