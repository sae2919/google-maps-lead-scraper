<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIWebsiteGenerator
{
    public function generate($lead): array
    {
        $name     = $lead->name     ?? 'Business';
        $category = $lead->category ?? 'Business';
        $address  = $lead->address  ?? '';
        $phone    = $lead->phone    ?? '';
        $area     = $lead->main_area ?? (str_contains($address, ',') ? trim(explode(',', $address)[0]) : 'Hyderabad');
        $rating   = $lead->rating   ?? '4.5';

        $prompt = $this->buildPrompt($name, $category, $address, $phone, $area, $rating);

        try {
            $response = Http::timeout(config('gemini.timeout', 60))
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post(config('gemini.url') . '?key=' . config('gemini.key'), [
                    'contents' => [[
                        'parts' => [['text' => $prompt]]
                    ]],
                    'generationConfig' => [
                        'temperature'     => 0.8,
                        'maxOutputTokens' => 4096,
                    ],
                ]);

            if (!$response->successful()) {
                Log::error('AIWebsiteGenerator Gemini error: ' . $response->status());
                return $this->fallback($name, $category, $area);
            }

            $rawText = $response->json('candidates.0.content.parts.0.text') ?? '';
            $clean   = trim(preg_replace('/^```json\s*|^```\s*|```\s*$/m', '', $rawText));
            $data    = json_decode($clean, true);

            if (!$data || !isset($data['sections'])) {
                Log::warning('AIWebsiteGenerator: invalid JSON returned');
                return $this->fallback($name, $category, $area);
            }

            return $data;

        } catch (\Exception $e) {
            Log::error('AIWebsiteGenerator exception: ' . $e->getMessage());
            return $this->fallback($name, $category, $area);
        }
    }

    // -------------------------------------------------------------------------

    private function buildPrompt(
        string $name, string $category, string $address,
        string $phone, string $area, string $rating
    ): string {
        return <<<PROMPT
You are an expert web content writer for Indian businesses.
Generate complete website content JSON for the business below.
ALL content must be 100% specific and realistic for THIS exact business type.
NEVER use generic phrases like "Quality Service" or "Customer First".

════════════════════════════════════════
BUSINESS DETAILS
════════════════════════════════════════
Name     : {$name}
Category : {$category}
Address  : {$address}
Area     : {$area}
Phone    : {$phone}
Rating   : {$rating}

════════════════════════════════════════
REQUIRED JSON — fill every field
════════════════════════════════════════
Return ONLY raw valid JSON. First character must be {. No markdown, no explanation.

{
  "tagline": "One punchy memorable tagline specific to this exact business type. No clichés.",

  "colors": {
    "primary": "#hex — pick a color that truly fits this category",
    "accent":  "#hex — darker shade for headings",
    "bg":      "#hex — light background appropriate for this type"
  },

  "navbar": ["Home", "About", "Services", "Gallery", "Contact"],

  "about": {
    "paragraph1": "2-3 sentences about {$name} in {$area}. Mention the category, location, what makes it stand out. Sound like it was written specifically for this {$category}.",
    "paragraph2": "2-3 sentences about the team, commitment to customers, and what differentiates this business from competitors in {$area}."
  },

  "stats": [
    { "value": "realistic number+", "label": "specific metric for {$category}" },
    { "value": "realistic number★ or /5", "label": "rating or quality metric" },
    { "value": "realistic years or number", "label": "experience or another metric" }
  ],

  "services": [
    { "icon": "one emoji", "title": "Real service name for {$category}", "desc": "2 realistic sentences about this specific service." },
    { "icon": "one emoji", "title": "Real service name", "desc": "2 realistic sentences." },
    { "icon": "one emoji", "title": "Real service name", "desc": "2 realistic sentences." },
    { "icon": "one emoji", "title": "Real service name", "desc": "2 realistic sentences." },
    { "icon": "one emoji", "title": "Real service name", "desc": "2 realistic sentences." },
    { "icon": "one emoji", "title": "Real service name", "desc": "2 realistic sentences." }
  ],

  "reviews": [
    {
      "name": "Realistic Indian first name + last initial",
      "role": "Specific customer type for {$category} e.g. Regular Diner / Dog Owner / Patient",
      "text": "2-3 sentence authentic review mentioning something specific about the {$category} experience.",
      "rating": 5
    },
    {
      "name": "Another Indian name",
      "role": "Different customer type",
      "text": "Different authentic review, different aspect of the business.",
      "rating": 5
    },
    {
      "name": "Third Indian name",
      "role": "Third customer type",
      "text": "Third review with a specific detail about the {$category}.",
      "rating": 4
    }
  ],

  "opening_hours": [
    { "day": "Monday",    "time": "realistic hours for {$category} — e.g. restaurant 11AM-11PM, hospital 24 hrs, school 8AM-4PM" },
    { "day": "Tuesday",   "time": "same or slightly different" },
    { "day": "Wednesday", "time": "same or slightly different" },
    { "day": "Thursday",  "time": "same or slightly different" },
    { "day": "Friday",    "time": "same or different" },
    { "day": "Saturday",  "time": "weekend hours — may differ" },
    { "day": "Sunday",    "time": null if closed OR realistic hours if open }
  ],

  "sections": [
    { "type": "hero",     "title": "{$name}", "body": "same as tagline" },
    { "type": "about",    "title": "About Us" },
    { "type": "services", "title": "Our Services", "items": [] },
    { "type": "gallery",  "title": "Our Gallery" },
    { "type": "reviews",  "title": "What Customers Say" },
    { "type": "info",     "title": "Business Info" },
    { "type": "contact",  "title": "Contact Us", "body": "Visit us or reach out — we respond promptly." }
  ]
}

════════════════════════════════════════
STRICT RULES
════════════════════════════════════════
- Return ONLY raw valid JSON. First char = {
- Colors must genuinely fit the category:
  * Restaurant → warm amber/orange on dark or cream bg
  * Hospital/Clinic → sky blue on white
  * Gym → red or orange on near-black
  * Salon/Spa → pink/rose on soft light bg
  * Hotel → gold on deep navy
  * Pet Store → teal/green on mint white
  * School → indigo on light yellow
  * Pharmacy → green on clean white
  * Law Firm → dark navy on off-white
  * Real Estate → cyan on dark blue-grey
- Opening hours: realistic for the category type (hospital=24/7, school=Mon-Fri, restaurant=lunch+dinner)
- Stats: realistic Indian business numbers
- Service titles: REAL names from this industry
- Reviews: authentic Indian names, specific to category
- Tagline: NO clichés like "Excellence in service" or "Your trusted partner"
PROMPT;
    }

    // -------------------------------------------------------------------------

    private function fallback(string $name, string $category, string $area): array
    {
        return [
            'tagline' => "Trusted {$category} services in {$area}",
            'colors'  => ['primary' => '#3b82f6', 'accent' => '#1e3a8a', 'bg' => '#ffffff'],
            'navbar'  => ['Home', 'About', 'Services', 'Gallery', 'Contact'],
            'about'   => [
                'paragraph1' => "Welcome to {$name}, proudly serving customers in {$area} with a commitment to excellence.",
                'paragraph2' => "Our experienced team takes pride in delivering outstanding results for every customer.",
            ],
            'stats' => [
                ['value' => '500+', 'label' => 'Happy Customers'],
                ['value' => '5★',   'label' => 'Avg Rating'],
                ['value' => '5+',   'label' => 'Years Active'],
            ],
            'services' => [
                ['icon' => '⭐', 'title' => 'Premium Service',      'desc' => 'High quality service tailored to your needs.'],
                ['icon' => '🎯', 'title' => 'Expert Professionals', 'desc' => 'Skilled team with years of experience.'],
                ['icon' => '💡', 'title' => 'Modern Approach',      'desc' => 'Up-to-date methods and best practices.'],
                ['icon' => '🤝', 'title' => 'Customer Care',        'desc' => 'Dedicated support for every customer.'],
                ['icon' => '⚡', 'title' => 'Fast Turnaround',      'desc' => 'Efficient delivery without compromise.'],
                ['icon' => '💰', 'title' => 'Fair Pricing',         'desc' => 'Transparent pricing, no hidden charges.'],
            ],
            'reviews' => [
                ['name' => 'Rahul S.',  'role' => 'Regular Customer', 'text' => 'Excellent service and very professional staff. Highly recommended to everyone!', 'rating' => 5],
                ['name' => 'Priya M.', 'role' => 'Verified Customer', 'text' => 'Very clean and well-organised. Quality has been consistently great over the years.', 'rating' => 4],
                ['name' => 'Arjun K.', 'role' => 'Happy Customer',    'text' => 'Best in the area. Friendly, knowledgeable, and affordable. Will keep coming back!', 'rating' => 5],
            ],
            'opening_hours' => [
                ['day' => 'Monday',    'time' => '9:00 AM – 9:00 PM'],
                ['day' => 'Tuesday',   'time' => '9:00 AM – 9:00 PM'],
                ['day' => 'Wednesday', 'time' => '9:00 AM – 9:00 PM'],
                ['day' => 'Thursday',  'time' => '9:00 AM – 9:00 PM'],
                ['day' => 'Friday',    'time' => '9:00 AM – 9:00 PM'],
                ['day' => 'Saturday',  'time' => '9:00 AM – 9:00 PM'],
                ['day' => 'Sunday',    'time' => null],
            ],
            'sections' => [
                ['type' => 'hero',     'title' => $name,         'body' => "Trusted {$category} in {$area}"],
                ['type' => 'about',    'title' => 'About Us'],
                ['type' => 'services', 'title' => 'Our Services', 'items' => []],
                ['type' => 'gallery',  'title' => 'Our Gallery'],
                ['type' => 'reviews',  'title' => 'What Customers Say'],
                ['type' => 'info',     'title' => 'Business Info'],
                ['type' => 'contact',  'title' => 'Contact Us',   'body' => 'Reach out — we respond promptly.'],
            ],
        ];
    }
}