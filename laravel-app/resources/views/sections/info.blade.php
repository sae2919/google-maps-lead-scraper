<div class="container">

    <h2 style="text-align:center;margin-bottom:40px;">Business Info</h2>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:40px;">

        <div>
            <p><strong>📍 Address:</strong><br>{{ $lead->address }}</p>
            <p><strong>📞 Phone:</strong><br>{{ $lead->phone }}</p>
            <p><strong>🏷 Category:</strong><br>{{ strtoupper($data['category']) }}</p>
        </div>

        <div>
            <p><strong>⏰ Opening Hours:</strong></p>
            <p>Mon - Sat: 9:00 AM - 9:00 PM</p>
            <p>Sunday: Closed</p>
        </div>

    </div>

</div>