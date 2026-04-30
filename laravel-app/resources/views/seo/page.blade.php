<h1>Best {{ ucfirst($service) }} in {{ ucfirst($city) }}</h1>

@foreach($leads as $lead)
<div style="margin-bottom:20px;">
    <h2>{{ $lead->name }}</h2>
    <p>{{ $lead->address }}</p>
</div>
@endforeach