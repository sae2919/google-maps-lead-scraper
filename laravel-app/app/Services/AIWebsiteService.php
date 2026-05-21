<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIWebsiteService
{
    private ?string $apiKey;
    private string  $model;
    private string  $baseUri;

    public function __construct()
    {
        $this->apiKey  = env('GEMINI_API_KEY');
        $this->model   = env('GEMINI_MODEL',   'gemini-1.5-flash');
        $this->baseUri = env('GEMINI_BASE_URI', 'https://generativelanguage.googleapis.com/v1beta/');
    }

    // =========================================================================
    // MAIN ENTRY POINT
    // =========================================================================

    public function generateConfig(array $business): array
    {
        if (empty($this->apiKey)) {
            Log::warning('AIWebsiteService: No Gemini API key — using fallback', [
                'business' => $business['name'] ?? 'unknown',
                'type'     => $business['type'] ?? $business['category'] ?? 'unknown',
            ]);
            return $this->fetchServiceImages($this->getFallbackConfig($business));
        }

        try {
            $prompt = $this->buildPrompt($business);

            $url = rtrim($this->baseUri, '/')
                 . '/models/' . $this->model
                 . ':generateContent?key=' . $this->apiKey;

            // Retry up to 3 times on 503
            $response = null;
            for ($attempt = 1; $attempt <= 3; $attempt++) {
                $response = Http::timeout(60)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post($url, [
                        'contents' => [
                            ['parts' => [['text' => $prompt]]]
                        ],
                        'generationConfig' => [
                            'temperature'     => 0.9,
                            'maxOutputTokens' => 8192,
                        ],
                    ]);

                if ($response->status() === 503) {
                    Log::warning("AIWebsiteService: Gemini 503, attempt {$attempt}/3. Waiting...");
                    sleep($attempt * 3);
                    continue;
                }
                break;
            }

            if ($response->failed()) {
                Log::error('AIWebsiteService: Gemini API failed — using fallback', [
                    'status'   => $response->status(),
                    'body'     => $response->body(),
                    'business' => $business['name'] ?? 'unknown',
                    'type'     => $business['type'] ?? $business['category'] ?? 'unknown',
                ]);
                return $this->fetchServiceImages($this->getFallbackConfig($business));
            }

            $text = $response->json('candidates.0.content.parts.0.text', '');

            if (empty($text)) {
                Log::error('AIWebsiteService: Empty Gemini response — using fallback', [
                    'full_response' => $response->json(),
                    'business'      => $business['name'] ?? 'unknown',
                ]);
                return $this->fetchServiceImages($this->getFallbackConfig($business));
            }

            // Extract JSON block from response
            if (preg_match('/```json\s*(.*?)\s*```/s', $text, $m)) {
                $json = $m[1];
            } elseif (preg_match('/\{.*\}/s', $text, $m)) {
                $json = $m[0];
            } else {
                Log::error('AIWebsiteService: No JSON found in Gemini response — using fallback', [
                    'text'     => substr($text, 0, 500),
                    'business' => $business['name'] ?? 'unknown',
                ]);
                return $this->fetchServiceImages($this->getFallbackConfig($business));
            }

            // STEP 1: Strip hero_gradient before parsing
            $json = $this->stripHeroGradient($json);

            // STEP 2: Parse JSON, repair if needed
            $config = json_decode($json, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $json   = $this->repairJson($json);
                $config = json_decode($json, true);
            }

            if (json_last_error() !== JSON_ERROR_NONE || empty($config)) {
                Log::error('AIWebsiteService: JSON parse failed after repair — using fallback', [
                    'error'    => json_last_error_msg(),
                    'json'     => substr($json, 0, 500),
                    'business' => $business['name'] ?? 'unknown',
                    'type'     => $business['type'] ?? $business['category'] ?? 'unknown',
                ]);
                return $this->fetchServiceImages($this->getFallbackConfig($business));
            }

            // STEP 3: Compute hero_gradient server-side
            $primary   = $config['theme']['primary']  ?? '#1a1a2e';
            $secondary = $config['theme']['secondary'] ?? '#16213e';
            $accent    = $config['theme']['accent']    ?? '#e94560';
            $config['theme']['hero_gradient'] =
                "linear-gradient(135deg, {$primary} 0%, {$secondary} 60%, {$accent} 100%)";

            // STEP 4: Fetch Pexels images
            $config = $this->fetchServiceImages($config);

            Log::info('AIWebsiteService: Config generated successfully via Gemini', [
                'business' => $business['name'] ?? 'unknown',
                'style'    => $config['theme']['style']        ?? 'unknown',
                'hero'     => $config['layout']['hero_type']   ?? 'unknown',
                'font'     => $config['theme']['font_heading'] ?? 'unknown',
            ]);

            return $config;

        } catch (\Throwable $e) {
            Log::error('AIWebsiteService: Exception thrown — using fallback', [
                'message'  => $e->getMessage(),
                'line'     => $e->getLine(),
                'file'     => $e->getFile(),
                'business' => $business['name'] ?? 'unknown',
                'type'     => $business['type'] ?? $business['category'] ?? 'unknown',
            ]);
            return $this->fetchServiceImages($this->getFallbackConfig($business));
        }
    }

    // =========================================================================
    // PEXELS IMAGE FETCHER — services
    // =========================================================================

    private function fetchServiceImages(array $config): array
    {
        $pexelsKey = env('PEXELS_API_KEY');

        if (empty($pexelsKey) || empty($config['content']['services']['items'])) {
            return $config;
        }

        foreach ($config['content']['services']['items'] as $i => $item) {
            $query = trim($item['image_query'] ?? $item['title'] ?? 'professional service');

            try {
                $response = Http::timeout(8)
                    ->withHeaders(['Authorization' => $pexelsKey])
                    ->get('https://api.pexels.com/v1/search', [
                        'query'       => $query,
                        'per_page'    => 3,
                        'orientation' => 'landscape',
                    ]);

                if ($response->successful()) {
                    $photos   = $response->json('photos', []);
                    $best     = collect($photos)->sortByDesc('width')->first();
                    $imageUrl = $best['src']['large'] ?? $best['src']['medium'] ?? null;

                    if ($imageUrl) {
                        $config['content']['services']['items'][$i]['image_url'] = $imageUrl;
                        Log::info("AIWebsiteService: Pexels image fetched [{$query}]");
                    } else {
                        Log::warning("AIWebsiteService: Pexels returned no photos for [{$query}]");
                    }
                } else {
                    Log::warning("AIWebsiteService: Pexels non-200 for [{$query}]", [
                        'status' => $response->status(),
                    ]);
                }
            } catch (\Throwable $e) {
                Log::warning("AIWebsiteService: Pexels exception for [{$query}]: " . $e->getMessage());
            }
        }

        // Fetch hero image if not set
        if (empty($config['content']['hero']['image_url'])) {
            $config = $this->fetchHeroImage($config);
        }

        return $config;
    }

    // =========================================================================
    // PEXELS IMAGE FETCHER — hero
    // =========================================================================

    private function fetchHeroImage(array $config): array
    {
        $pexelsKey = env('PEXELS_API_KEY');
        if (empty($pexelsKey)) return $config;

        $services = $config['content']['services']['items'] ?? [];
        $query    = !empty($services[0]['image_query'])
            ? $services[0]['image_query']
            : 'professional business building exterior';

        try {
            $response = Http::timeout(8)
                ->withHeaders(['Authorization' => $pexelsKey])
                ->get('https://api.pexels.com/v1/search', [
                    'query'       => $query,
                    'per_page'    => 1,
                    'orientation' => 'landscape',
                    'size'        => 'large',
                ]);

            if ($response->successful()) {
                $photos   = $response->json('photos', []);
                $imageUrl = $photos[0]['src']['large2x'] ?? $photos[0]['src']['large'] ?? null;
                if ($imageUrl) {
                    $config['content']['hero']['image_url'] = $imageUrl;
                }
            }
        } catch (\Throwable $e) {
            Log::warning('AIWebsiteService: Hero image fetch failed: ' . $e->getMessage());
        }

        return $config;
    }

    // =========================================================================
    // JSON HELPERS
    // =========================================================================

    private function stripHeroGradient(string $json): string
    {
        $json = preg_replace('/"hero_gradient"\s*:\s*"[^"]*",?\s*/s', '', $json);
        $json = preg_replace('/"hero_gradient"\s*:\s*"[^"]*$/s',       '', $json);
        $json = preg_replace('/,(\s*[}\]])/s',                         '$1', $json);
        return $json;
    }

    private function repairJson(string $json): string
    {
        $json = preg_replace('/,\s*"[^"]*"\s*:\s*"[^"]*$/s', '', $json);
        $json = preg_replace('/,\s*"[^"]*"\s*:\s*$/s',        '', $json);
        $json = preg_replace('/,\s*$/s',                       '', $json);

        $openBraces   = substr_count($json, '{') - substr_count($json, '}');
        $openBrackets = substr_count($json, '[') - substr_count($json, ']');

        $json .= str_repeat(']', max(0, $openBrackets));
        $json .= str_repeat('}', max(0, $openBraces));

        return $json;
    }

    // =========================================================================
    // PROMPT BUILDER
    // =========================================================================

    private function buildPrompt(array $b): string
    {
        $name    = $b['name']    ?? 'Business';
        $type    = $b['type']    ?? $b['category'] ?? 'Business';
        $rating  = $b['rating']  ?? 'N/A';
        $reviews = $b['reviews'] ?? '0';
        $phone   = $b['phone']   ?? '';
        $address = $b['address'] ?? '';

        return <<<PROMPT
You are an elite web designer. Generate a UNIQUE website configuration JSON for this real business.

Business Details:
- Name: {$name}
- Type/Category: {$type}
- Rating: {$rating} stars ({$reviews} reviews)
- Phone: {$phone}
- Address: {$address}

STRICT RULES:
1. Match colors to the industry:
   - Hotel/Resort    = warm gold + deep navy
   - Restaurant/Cafe = rich red or orange + cream
   - Tech/Software   = electric blue + dark background
   - Spa/Salon       = sage green + blush pink
   - Medical/Clinic  = clean white + teal
   - Retail/Shop     = bold accent + neutral base
   - Education       = deep indigo + warm white
2. Write REAL, SPECIFIC content for THIS business — zero placeholder text
3. Generate 3 authentic-sounding Indian customer testimonials
4. Pick hero_type by industry: hotels=fullscreen, restaurants=split, tech=centered, spa=minimal
5. NEVER use Inter, Roboto, Arial, or system fonts
6. Make every website unique — rotate styles: luxury, editorial, geometric, organic, industrial, elegant, vibrant
7. DO NOT include hero_gradient field anywhere in the JSON
8. image_query MUST be specific to the business type — e.g. for hospitals use "doctor patient consultation" NOT "professional service"

FONT OPTIONS:
- Headings: Playfair Display, Cormorant Garamond, Bebas Neue, Space Grotesk, DM Serif Display, Syne, Josefin Sans, Libre Baskerville
- Body: DM Sans, Nunito, Source Sans 3, Work Sans, Outfit, Plus Jakarta Sans

Return ONLY valid JSON inside triple-backtick json block. No explanation, no extra text:

```json
{
  "theme": {
    "style": "luxury|modern|editorial|geometric|organic|elegant|vibrant|industrial",
    "primary": "#hexcode",
    "secondary": "#hexcode",
    "accent": "#hexcode",
    "text_dark": "#hexcode",
    "text_light": "#ffffff",
    "bg": "#hexcode",
    "surface": "#hexcode",
    "hero_overlay": "rgba(0,0,0,0.45)",
    "font_heading": "Exact Google Font Name",
    "font_body": "Exact Google Font Name",
    "border_radius": "4px|8px|16px|24px",
    "shadow": "0 4px 24px rgba(0,0,0,0.12)"
  },
  "layout": {
    "hero_type": "split|centered|fullscreen|minimal",
    "nav_style": "solid|glass|colored|transparent",
    "sections": ["hero", "stats", "services", "about", "testimonials", "cta", "contact"]
  },
  "content": {
    "brand": "{$name}",
    "tagline": "short brand tagline",
    "hero": {
      "badge": "2-3 word badge text",
      "headline": "powerful specific headline for {$name}",
      "subheadline": "1-2 sentence value proposition",
      "cta_primary": "main CTA button text",
      "cta_secondary": "secondary link text",
      "highlight_stat": "e.g. 4.8 Star Rated or 500+ Guests"
    },
    "stats": [
      {"value": "X+",  "label": "label", "icon": "single emoji"},
      {"value": "X+",  "label": "label", "icon": "single emoji"},
      {"value": "X%",  "label": "label", "icon": "single emoji"},
      {"value": "X+",  "label": "label", "icon": "single emoji"}
    ],
    "services": {
      "heading": "section heading",
      "subheading": "one line description",
      "items": [
        {"icon": "emoji", "title": "service name", "description": "2 sentence description", "image_query": "specific 3-4 word Pexels search for THIS service type e.g. doctor patient consultation"},
        {"icon": "emoji", "title": "service name", "description": "2 sentence description", "image_query": "specific 3-4 word Pexels search for THIS service type"},
        {"icon": "emoji", "title": "service name", "description": "2 sentence description", "image_query": "specific 3-4 word Pexels search for THIS service type"},
        {"icon": "emoji", "title": "service name", "description": "2 sentence description", "image_query": "specific 3-4 word Pexels search for THIS service type"}
      ]
    },
    "about": {
      "heading": "About {$name}",
      "story": "2-3 genuine sentences about this specific business",
      "mission": "one compelling mission statement",
      "highlight_items": [
        {"icon": "emoji", "text": "key differentiator"},
        {"icon": "emoji", "text": "key differentiator"},
        {"icon": "emoji", "text": "key differentiator"}
      ]
    },
    "testimonials": {
      "heading": "What Our Customers Say",
      "items": [
        {"name": "Indian full name", "role": "occupation or city", "text": "authentic 1-2 sentence review specific to this business type", "rating": 5, "avatar_letter": "first letter of name"},
        {"name": "Indian full name", "role": "occupation or city", "text": "authentic 1-2 sentence review specific to this business type", "rating": 5, "avatar_letter": "first letter of name"},
        {"name": "Indian full name", "role": "occupation or city", "text": "authentic 1-2 sentence review specific to this business type", "rating": 4, "avatar_letter": "first letter of name"}
      ]
    },
    "cta": {
      "heading": "compelling call-to-action heading",
      "subheading": "supporting sentence",
      "button": "button text"
    },
    "contact": {
      "heading": "Get In Touch",
      "subheading": "We would love to hear from you",
      "phone": "{$phone}",
      "address": "{$address}",
      "email": "",
      "hours": "infer realistic business hours for a {$type}"
    }
  }
}
```
PROMPT;
    }

    // =========================================================================
    // TYPE-AWARE FALLBACK CONFIG
    // =========================================================================

    private function getFallbackConfig(array $b): array
    {
        $name    = $b['name']    ?? 'Business';
        $type    = strtolower($b['type'] ?? $b['category'] ?? 'business');
        $phone   = $b['phone']   ?? '';
        $address = $b['address'] ?? '';

        $typeMap = [

            'hospital|clinic|medical|healthcare|doctor|nursing|surgery|health' => [
                'primary'   => '#0f4c75', 'secondary' => '#1b6ca8',
                'accent'    => '#00b4d8', 'bg'        => '#f0f8ff',
                'surface'   => '#e8f4f8', 'style'     => 'clean',
                'tagline'   => 'Compassionate Care, Advanced Medicine',
                'hero_type' => 'split',
                'cta_badge' => 'Medical Services',
                'stats'     => [
                    ['value' => '15+',  'label' => 'Years of Service',     'icon' => '🏥'],
                    ['value' => '200+', 'label' => 'Specialist Doctors',   'icon' => '👨‍⚕️'],
                    ['value' => '98%',  'label' => 'Patient Satisfaction', 'icon' => '❤️'],
                    ['value' => '24/7', 'label' => 'Emergency Care',       'icon' => '🚑'],
                ],
                'services' => [
                    ['icon' => '🚑', 'title' => 'Emergency Care',
                     'description' => 'Round-the-clock emergency services with fully equipped trauma units and rapid response teams.',
                     'image_query' => 'hospital emergency room doctors'],
                    ['icon' => '🔬', 'title' => 'Advanced Diagnostics',
                     'description' => 'State-of-the-art lab and imaging facilities including MRI, CT scan, and pathology.',
                     'image_query' => 'medical laboratory diagnostics equipment'],
                    ['icon' => '👨‍⚕️', 'title' => 'OPD Consultations',
                     'description' => 'Expert outpatient consultations across 30+ specialties with experienced physicians.',
                     'image_query' => 'doctor patient consultation clinic'],
                    ['icon' => '💊', 'title' => 'Surgical Procedures',
                     'description' => 'Modern operation theatres equipped for complex surgeries with expert surgical teams.',
                     'image_query' => 'surgical operation theatre procedure'],
                ],
                'highlight_items' => [
                    ['icon' => '✅', 'text' => 'NABH Accredited Facility'],
                    ['icon' => '🏅', 'text' => 'ISO Certified Hospital'],
                    ['icon' => '💯', 'text' => 'Patient-First Approach'],
                ],
            ],

            'hotel|resort|hospitality|inn|stay|lodge|guest house' => [
                'primary'   => '#1a0a00', 'secondary' => '#3d1f00',
                'accent'    => '#c9a84c', 'bg'        => '#fdf8f0',
                'surface'   => '#faf3e0', 'style'     => 'luxury',
                'tagline'   => 'Where Luxury Meets Comfort',
                'hero_type' => 'fullscreen',
                'cta_badge' => 'Luxury Hospitality',
                'stats'     => [
                    ['value' => '4.8★', 'label' => 'Guest Rating',     'icon' => '⭐'],
                    ['value' => '500+', 'label' => 'Happy Guests',      'icon' => '🏨'],
                    ['value' => '50+',  'label' => 'Premium Rooms',     'icon' => '🛏️'],
                    ['value' => '24/7', 'label' => 'Concierge Service', 'icon' => '🎩'],
                ],
                'services' => [
                    ['icon' => '🛏️', 'title' => 'Luxury Rooms',
                     'description' => 'Elegantly furnished rooms and suites with premium amenities and stunning views.',
                     'image_query' => 'luxury hotel room interior'],
                    ['icon' => '🍽️', 'title' => 'Fine Dining',
                     'description' => 'Multi-cuisine restaurant serving authentic local and international dishes.',
                     'image_query' => 'hotel restaurant fine dining food'],
                    ['icon' => '💆', 'title' => 'Spa & Wellness',
                     'description' => 'Rejuvenating spa treatments and wellness programs for complete relaxation.',
                     'image_query' => 'hotel spa massage wellness'],
                    ['icon' => '🏊', 'title' => 'Recreation',
                     'description' => 'Swimming pool, fitness centre, and curated recreational experiences.',
                     'image_query' => 'hotel swimming pool resort'],
                ],
                'highlight_items' => [
                    ['icon' => '✅', 'text' => '5-Star Certified Property'],
                    ['icon' => '🏅', 'text' => 'Award-Winning Hospitality'],
                    ['icon' => '💯', 'text' => 'Personalised Guest Experience'],
                ],
            ],

            'restaurant|cafe|food|bakery|dhaba|biryani|kitchen|eatery|bar' => [
                'primary'   => '#7b1e00', 'secondary' => '#b83200',
                'accent'    => '#ff6b35', 'bg'        => '#fff9f5',
                'surface'   => '#fff0e8', 'style'     => 'vibrant',
                'tagline'   => 'Every Bite Tells a Story',
                'hero_type' => 'split',
                'cta_badge' => 'Culinary Experience',
                'stats'     => [
                    ['value' => '200+', 'label' => 'Dishes on Menu',     'icon' => '🍽️'],
                    ['value' => '1K+',  'label' => 'Happy Diners Daily', 'icon' => '😋'],
                    ['value' => '15+',  'label' => 'Years of Flavour',   'icon' => '👨‍🍳'],
                    ['value' => '4.7★', 'label' => 'Customer Rating',    'icon' => '⭐'],
                ],
                'services' => [
                    ['icon' => '🍛', 'title' => 'Dine-In Experience',
                     'description' => 'Comfortable ambiance perfect for family meals, dates, and celebrations.',
                     'image_query' => 'indian restaurant dining ambiance interior'],
                    ['icon' => '📦', 'title' => 'Takeaway & Delivery',
                     'description' => 'Fresh food packed and delivered hot straight to your doorstep.',
                     'image_query' => 'food delivery takeaway packaging boxes'],
                    ['icon' => '🎉', 'title' => 'Event Catering',
                     'description' => 'Professional catering for corporate events, weddings, and private parties.',
                     'image_query' => 'wedding catering buffet event food'],
                    ['icon' => '👨‍🍳', 'title' => 'Signature Dishes',
                     'description' => 'Chef-special recipes made with farm-fresh ingredients and authentic spices.',
                     'image_query' => 'indian thali traditional food close up'],
                ],
                'highlight_items' => [
                    ['icon' => '✅', 'text' => 'FSSAI Licensed Kitchen'],
                    ['icon' => '🏅', 'text' => "Chef's Signature Recipes"],
                    ['icon' => '💯', 'text' => 'Farm-Fresh Ingredients'],
                ],
            ],

            'spa|salon|beauty|wellness|massage|parlour|grooming' => [
                'primary'   => '#4a7c59', 'secondary' => '#2d5a3d',
                'accent'    => '#f4a261', 'bg'        => '#f9f5f0',
                'surface'   => '#f0ebe3', 'style'     => 'organic',
                'tagline'   => 'Relax. Restore. Rejuvenate.',
                'hero_type' => 'minimal',
                'cta_badge' => 'Wellness & Beauty',
                'stats'     => [
                    ['value' => '50+',  'label' => 'Treatments',        'icon' => '💆'],
                    ['value' => '5K+',  'label' => 'Happy Clients',     'icon' => '✨'],
                    ['value' => '10+',  'label' => 'Expert Therapists', 'icon' => '👐'],
                    ['value' => '4.9★', 'label' => 'Client Rating',     'icon' => '⭐'],
                ],
                'services' => [
                    ['icon' => '💆', 'title' => 'Body Massage',
                     'description' => 'Therapeutic massages using premium oils to relieve stress and restore balance.',
                     'image_query' => 'spa body massage therapy treatment'],
                    ['icon' => '💅', 'title' => 'Beauty Treatments',
                     'description' => 'Complete beauty services including facials, manicure, pedicure, and more.',
                     'image_query' => 'beauty salon facial treatment'],
                    ['icon' => '✂️', 'title' => 'Hair Styling',
                     'description' => 'Expert hair care, colouring, and styling by certified professionals.',
                     'image_query' => 'hair salon styling professional'],
                    ['icon' => '🧘', 'title' => 'Wellness Packages',
                     'description' => 'Curated wellness packages combining multiple treatments for full body renewal.',
                     'image_query' => 'wellness spa package relaxation'],
                ],
                'highlight_items' => [
                    ['icon' => '✅', 'text' => 'Certified Therapists'],
                    ['icon' => '🌿', 'text' => 'Natural Organic Products'],
                    ['icon' => '💯', 'text' => 'Hygienic Private Cabins'],
                ],
            ],

            'school|college|academy|institute|education|tuition|coaching|training' => [
                'primary'   => '#2c3e7a', 'secondary' => '#1a2560',
                'accent'    => '#f39c12', 'bg'        => '#f8f9ff',
                'surface'   => '#eef0fa', 'style'     => 'elegant',
                'tagline'   => 'Shaping Futures, Building Leaders',
                'hero_type' => 'centered',
                'cta_badge' => 'Quality Education',
                'stats'     => [
                    ['value' => '5K+',  'label' => 'Students Enrolled',  'icon' => '🎓'],
                    ['value' => '200+', 'label' => 'Expert Faculty',      'icon' => '👩‍🏫'],
                    ['value' => '98%',  'label' => 'Pass Rate',           'icon' => '📊'],
                    ['value' => '20+',  'label' => 'Years of Excellence', 'icon' => '🏆'],
                ],
                'services' => [
                    ['icon' => '📚', 'title' => 'Academic Programmes',
                     'description' => 'Comprehensive curriculum designed to build strong academic foundations.',
                     'image_query' => 'students classroom learning education'],
                    ['icon' => '💻', 'title' => 'Digital Learning',
                     'description' => 'Technology-enabled classrooms with modern e-learning platforms.',
                     'image_query' => 'students digital learning laptop computer'],
                    ['icon' => '🏆', 'title' => 'Competitive Exam Prep',
                     'description' => 'Expert coaching for JEE, NEET, UPSC and other national examinations.',
                     'image_query' => 'exam preparation study books'],
                    ['icon' => '🎯', 'title' => 'Career Guidance',
                     'description' => 'Dedicated career counselling and placement support for every student.',
                     'image_query' => 'career guidance counselling students'],
                ],
                'highlight_items' => [
                    ['icon' => '✅', 'text' => 'Government Recognised'],
                    ['icon' => '🏅', 'text' => 'Award-Winning Faculty'],
                    ['icon' => '💯', 'text' => '100% Placement Support'],
                ],
            ],
        ];

        // Match type to config
        $matched = null;
        foreach ($typeMap as $pattern => $cfg) {
            if (preg_match('/(' . $pattern . ')/i', $type)) {
                $matched = $cfg;
                break;
            }
        }

        // Generic fallback if no type matched
        if (!$matched) {
            $matched = [
                'primary'   => '#1a1a2e', 'secondary' => '#16213e',
                'accent'    => '#e94560', 'bg'        => '#ffffff',
                'surface'   => '#f8f9fa', 'style'     => 'modern',
                'tagline'   => 'Excellence in Every Detail',
                'hero_type' => 'split',
                'cta_badge' => ucfirst($type) . ' Services',
                'stats'     => [
                    ['value' => '10+',  'label' => 'Years Experience', 'icon' => '🏆'],
                    ['value' => '500+', 'label' => 'Happy Clients',    'icon' => '😊'],
                    ['value' => '98%',  'label' => 'Satisfaction',     'icon' => '✨'],
                    ['value' => '24/7', 'label' => 'Support',          'icon' => '🕐'],
                ],
                'services' => [
                    ['icon' => '⭐', 'title' => ucfirst($type) . ' Services',
                     'description' => "Top-quality {$type} services tailored precisely to your specific needs.",
                     'image_query' => "{$type} professional service office"],
                    ['icon' => '🎯', 'title' => 'Expert Team',
                     'description' => 'Experienced professionals fully dedicated to delivering your best outcome.',
                     'image_query' => 'professional team meeting office'],
                    ['icon' => '🔧', 'title' => 'Full Support',
                     'description' => 'Comprehensive end-to-end support available at every step of your journey.',
                     'image_query' => 'customer support help desk'],
                    ['icon' => '💡', 'title' => 'Modern Approach',
                     'description' => 'Leveraging the latest technology and proven techniques for superior results.',
                     'image_query' => 'modern technology innovation workspace'],
                ],
                'highlight_items' => [
                    ['icon' => '✅', 'text' => 'Professionally Certified'],
                    ['icon' => '🏅', 'text' => 'Award Winning Service'],
                    ['icon' => '💯', 'text' => 'Satisfaction Guaranteed'],
                ],
            ];
        }

        $heroGradient = "linear-gradient(135deg, {$matched['primary']} 0%, {$matched['secondary']} 60%, {$matched['accent']} 100%)";

        return [
            'theme' => [
                'style'         => $matched['style'],
                'primary'       => $matched['primary'],
                'secondary'     => $matched['secondary'],
                'accent'        => $matched['accent'],
                'text_dark'     => '#1a1a2e',
                'text_light'    => '#ffffff',
                'bg'            => $matched['bg'],
                'surface'       => $matched['surface'],
                'hero_gradient' => $heroGradient,
                'hero_overlay'  => 'rgba(0,0,0,0.45)',
                'font_heading'  => 'Playfair Display',
                'font_body'     => 'DM Sans',
                'border_radius' => '8px',
                'shadow'        => '0 4px 24px rgba(0,0,0,0.12)',
            ],
            'layout' => [
                'hero_type' => $matched['hero_type'],
                'nav_style' => 'solid',
                'sections'  => ['hero', 'stats', 'services', 'about', 'testimonials', 'cta', 'contact'],
            ],
            'content' => [
                'brand'   => $name,
                'tagline' => $matched['tagline'],
                'hero' => [
                    'badge'          => $matched['cta_badge'],
                    'headline'       => "Welcome to {$name}",
                    'subheadline'    => "Trusted {$type} services committed to delivering excellence for every client.",
                    'cta_primary'    => 'Get Started',
                    'cta_secondary'  => 'Learn More',
                    'highlight_stat' => $matched['stats'][0]['value'] . ' ' . $matched['stats'][0]['label'],
                ],
                'stats'    => $matched['stats'],
                'services' => [
                    'heading'    => 'Our Services',
                    'subheading' => "Specialized {$type} solutions crafted to meet your every need.",
                    'items'      => $matched['services'],
                ],
                'about' => [
                    'heading'         => "About {$name}",
                    'story'           => "{$name} is a trusted {$type} establishment committed to delivering exceptional results. Our dedicated professionals bring deep expertise and genuine care to every client we serve.",
                    'mission'         => 'To deliver exceptional service that consistently exceeds every expectation.',
                    'highlight_items' => $matched['highlight_items'],
                ],
                'testimonials' => [
                    'heading' => 'What Our Customers Say',
                    'items' => [
                        ['name' => 'Priya Sharma', 'role' => 'Regular Customer',
                         'text' => "Outstanding experience at {$name}. The team was professional and went well above and beyond.",
                         'rating' => 5, 'avatar_letter' => 'P'],
                        ['name' => 'Rahul Verma',  'role' => 'Business Owner',
                         'text' => "I have been a loyal customer for years now. The quality and service here is truly unmatched.",
                         'rating' => 5, 'avatar_letter' => 'R'],
                        ['name' => 'Anitha Reddy', 'role' => 'Local Resident',
                         'text' => 'Highly recommended to everyone I know. Professional, reliable, and genuinely caring.',
                         'rating' => 4, 'avatar_letter' => 'A'],
                    ],
                ],
                'cta' => [
                    'heading'    => "Ready to Experience {$name}?",
                    'subheading' => 'Contact us today and discover the difference quality makes.',
                    'button'     => 'Contact Us Now',
                ],
                'contact' => [
                    'heading'    => 'Get In Touch',
                    'subheading' => "We'd love to hear from you",
                    'phone'      => $phone,
                    'address'    => $address,
                    'email'      => '',
                    'hours'      => 'Mon-Sat: 9:00 AM - 8:00 PM',
                ],
            ],
        ];
    }
}