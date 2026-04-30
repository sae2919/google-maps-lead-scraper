<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HTMLWebsiteGenerator
{
    /**
     * Per-category design DNA.
     * Each business type gets a genuinely different aesthetic.
     */
    private array $designDNA = [

        'restaurant' => [
            'theme'       => 'dark',
            'bg'          => '#0a0504',
            'primary'     => '#f97316',
            'accent'      => '#fbbf24',
            'text'        => '#f5f0eb',
            'muted'       => '#a8896c',
            'font_head'   => 'Playfair Display',
            'font_body'   => 'Lato',
            'hero_style'  => 'full-bleed image with warm dark overlay, bold italic headline',
            'sections'    => 'hero,about,menu,specials,gallery,contact',
            'vibe'        => 'warm, indulgent, appetizing. Copy focuses on flavors, freshness, dining experience.',
            'services_label' => 'Our Menu',
            'items_hint'  => '6 signature dishes with prices in ₹ and short descriptions',
        ],

        'hospital' => [
            'theme'       => 'light',
            'bg'          => '#f8faff',
            'primary'     => '#0ea5e9',
            'accent'      => '#0369a1',
            'text'        => '#0f172a',
            'muted'       => '#475569',
            'font_head'   => 'Nunito',
            'font_body'   => 'Inter',
            'hero_style'  => 'clean split layout: left text, right image. Professional and reassuring.',
            'sections'    => 'hero,about,services,specialties,gallery,contact',
            'vibe'        => 'trustworthy, caring, clinical. Copy emphasizes expertise, technology, patient outcomes.',
            'services_label' => 'Our Services',
            'items_hint'  => '6 medical services/departments with brief descriptions',
        ],

        'clinic' => [
            'theme'       => 'light',
            'bg'          => '#f0fdf4',
            'primary'     => '#22c55e',
            'accent'      => '#15803d',
            'text'        => '#0f172a',
            'muted'       => '#475569',
            'font_head'   => 'Nunito',
            'font_body'   => 'Inter',
            'hero_style'  => 'clean modern, soft green accents, doctor imagery',
            'sections'    => 'hero,about,services,gallery,contact',
            'vibe'        => 'gentle, professional, health-focused. Emphasize care and expertise.',
            'services_label' => 'Our Services',
            'items_hint'  => '5 clinic services with descriptions',
        ],

        'gym' => [
            'theme'       => 'dark',
            'bg'          => '#050505',
            'primary'     => '#ef4444',
            'accent'      => '#fbbf24',
            'text'        => '#ffffff',
            'muted'       => '#9ca3af',
            'font_head'   => 'Oswald',
            'font_body'   => 'Barlow',
            'hero_style'  => 'full-screen dark hero with dramatic red overlay, massive bold typography, hero image showing gym floor',
            'sections'    => 'hero,about,programs,pricing,gallery,contact',
            'vibe'        => 'aggressive, motivational, powerful. Use action verbs. "Crush your goals", "Unleash potential".',
            'services_label' => 'Our Programs',
            'items_hint'  => '5 training programs with intensity levels and descriptions',
        ],

        'hotel' => [
            'theme'       => 'dark',
            'bg'          => '#0c0c0e',
            'primary'     => '#d4a843',
            'accent'      => '#f5e6c8',
            'text'        => '#f5f0eb',
            'muted'       => '#9e8c75',
            'font_head'   => 'Cormorant Garamond',
            'font_body'   => 'Montserrat',
            'hero_style'  => 'full-screen luxury: dark gradient over hotel image, gold serif headline, elegant white CTA',
            'sections'    => 'hero,about,rooms,amenities,gallery,contact',
            'vibe'        => 'luxurious, refined, exclusive. Every word exudes premium. "Unparalleled comfort", "Curated experiences".',
            'services_label' => 'Our Rooms',
            'items_hint'  => '4 room types with pricing per night and luxury amenities',
        ],

        'salon' => [
            'theme'       => 'light',
            'bg'          => '#fdf6f9',
            'primary'     => '#e879a0',
            'accent'      => '#9d174d',
            'text'        => '#1c0814',
            'muted'       => '#9d6b8a',
            'font_head'   => 'Josefin Sans',
            'font_body'   => 'DM Sans',
            'hero_style'  => 'split layout with editorial feel, pink accents, avant-garde typography',
            'sections'    => 'hero,about,services,pricing,gallery,contact',
            'vibe'        => 'stylish, empowering, modern. Copy about transformation, confidence, beauty rituals.',
            'services_label' => 'Our Services',
            'items_hint'  => '6 salon services with prices in ₹ and durations',
        ],

        'pet_store' => [
            'theme'       => 'light',
            'bg'          => '#f0fdf9',
            'primary'     => '#10b981',
            'accent'      => '#047857',
            'text'        => '#0d2416',
            'muted'       => '#6b7280',
            'font_head'   => 'Nunito',
            'font_body'   => 'Nunito',
            'hero_style'  => 'bright, cheerful, playful. Rounded elements, fun typography, animals prominent',
            'sections'    => 'hero,about,services,pets,gallery,contact',
            'vibe'        => 'warm, caring, joyful. Copy about pet happiness, expert care, trusted by pet parents.',
            'services_label' => 'Our Services',
            'items_hint'  => '6 pet care services: grooming, vet, boarding, training, supplies, daycare',
        ],

        'pharmacy' => [
            'theme'       => 'light',
            'bg'          => '#f0fdf4',
            'primary'     => '#16a34a',
            'accent'      => '#14532d',
            'text'        => '#0f172a',
            'muted'       => '#475569',
            'font_head'   => 'Inter',
            'font_body'   => 'Inter',
            'hero_style'  => 'clean, sterile, trustworthy. Green cross symbol, clear hierarchy',
            'sections'    => 'hero,about,services,products,contact',
            'vibe'        => 'reliable, knowledgeable, health-first. Emphasize licensed pharmacists, genuine medicines.',
            'services_label' => 'Our Services',
            'items_hint'  => '5 pharmacy services: prescription filling, health checkups, vaccines, home delivery, consultation',
        ],

        'school' => [
            'theme'       => 'light',
            'bg'          => '#fffbeb',
            'primary'     => '#6366f1',
            'accent'      => '#4338ca',
            'text'        => '#0f172a',
            'muted'       => '#6b7280',
            'font_head'   => 'Nunito',
            'font_body'   => 'DM Sans',
            'hero_style'  => 'bright, inspiring, educational. Children/students imagery, approachable',
            'sections'    => 'hero,about,programs,admissions,gallery,contact',
            'vibe'        => 'nurturing, inspirational, academic. Emphasize holistic development, results, faculty.',
            'services_label' => 'Our Programs',
            'items_hint'  => '5 academic programs/grades with highlights',
        ],

        'retail' => [
            'theme'       => 'dark',
            'bg'          => '#0a0a0a',
            'primary'     => '#f59e0b',
            'accent'      => '#ef4444',
            'text'        => '#ffffff',
            'muted'       => '#9ca3af',
            'font_head'   => 'Barlow Condensed',
            'font_body'   => 'Barlow',
            'hero_style'  => 'bold, product-focused. Large product imagery, sale badges, urgent CTAs',
            'sections'    => 'hero,about,products,offers,gallery,contact',
            'vibe'        => 'energetic, deal-driven, trendy. Urgency, value, exclusivity.',
            'services_label' => 'Featured Products',
            'items_hint'  => '6 product categories with starting prices',
        ],

        'law_firm' => [
            'theme'       => 'light',
            'bg'          => '#f8f7f4',
            'primary'     => '#1e293b',
            'accent'      => '#0f172a',
            'text'        => '#0f172a',
            'muted'       => '#64748b',
            'font_head'   => 'Libre Baskerville',
            'font_body'   => 'Source Sans 3',
            'hero_style'  => 'authoritative, conservative. Dark wood or library imagery, serif typography, gravitas',
            'sections'    => 'hero,about,practice_areas,team,contact',
            'vibe'        => 'authoritative, precise, trustworthy. "Protecting your rights", "Decades of experience".',
            'services_label' => 'Practice Areas',
            'items_hint'  => '5 legal practice areas with one-line descriptions',
        ],

        'real_estate' => [
            'theme'       => 'dark',
            'bg'          => '#080c12',
            'primary'     => '#22d3ee',
            'accent'      => '#0891b2',
            'text'        => '#f1f5f9',
            'muted'       => '#94a3b8',
            'font_head'   => 'Raleway',
            'font_body'   => 'Poppins',
            'hero_style'  => 'premium, aspirational. Luxury property photography, dark overlay, clean white text',
            'sections'    => 'hero,about,services,featured_properties,gallery,contact',
            'vibe'        => 'premium, investment-focused. "Your dream home awaits", "Prime locations", "Trusted by 500+ families".',
            'services_label' => 'Our Services',
            'items_hint'  => '5 real estate services: buying, selling, rental, commercial, consulting',
        ],
    ];

    /**
     * Generate a complete HTML website for a business.
     *
     * @param  array  $business  [name, category, city, phone, address]
     * @param  array  $images    Array of 4 image URLs from Pexels
     * @return string            Complete HTML document
     */
    public function generate(array $business, array $images): string
    {
        $catKey = strtolower(str_replace([' ', '-'], '_', $business['category']));
        $design = $this->designDNA[$catKey] ?? $this->defaultDesign();

        $prompt = $this->buildPrompt($business, $design, $images);

        try {
            $response = Http::timeout(config('gemini.timeout', 60))
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post(config('gemini.url') . '?key=' . config('gemini.key'), [
                    'contents' => [[
                        'parts' => [['text' => $prompt]]
                    ]],
                    'generationConfig' => [
                        'temperature'     => 0.7,
                        'maxOutputTokens' => 8192,
                    ],
                ]);

            if (!$response->successful()) {
                Log::error('Gemini API error: ' . $response->status() . ' — ' . $response->body());
                return $this->errorFallback($business, $design, $images);
            }

            $rawText = $response->json('candidates.0.content.parts.0.text') ?? '';

            return $this->extractHTML($rawText) ?: $this->errorFallback($business, $design, $images);

        } catch (\Exception $e) {
            Log::error('HTMLWebsiteGenerator error: ' . $e->getMessage());
            return $this->errorFallback($business, $design, $images);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PROMPT BUILDER
    // ─────────────────────────────────────────────────────────────────────────

    private function buildPrompt(array $b, array $d, array $imgs): string
    {
        $name     = $b['name'];
        $category = ucwords(str_replace('_', ' ', $b['category']));
        $city     = $b['city']    ?? 'Hyderabad';
        $phone    = $b['phone']   ?? '';
        $address  = $b['address'] ?? $city;
        $wa       = preg_replace('/[^0-9]/', '', $phone);
        $mapsQ    = urlencode($address);

        $hero   = $imgs[0] ?? '';
        $img1   = $imgs[1] ?? $imgs[0] ?? '';
        $img2   = $imgs[2] ?? $imgs[0] ?? '';
        $img3   = $imgs[3] ?? $imgs[0] ?? '';

        $fontUrl = urlencode($d['font_head']) . ':wght@400;700;900' .
                   ($d['font_body'] !== $d['font_head']
                       ? '&family=' . urlencode($d['font_body']) . ':wght@400;600'
                       : '');

        return <<<PROMPT
You are a world-class UI/UX designer and frontend developer.
Generate a COMPLETE, production-ready, visually stunning HTML5 website.

══════════════════════════════════════════════════════
BUSINESS
══════════════════════════════════════════════════════
Name     : {$name}
Type     : {$category}
City     : {$city}
Phone    : {$phone}
WhatsApp : {$wa}
Address  : {$address}

══════════════════════════════════════════════════════
IMAGES — USE EXACT URLs AS img src ATTRIBUTES
══════════════════════════════════════════════════════
Hero    : {$hero}
Image 2 : {$img1}
Image 3 : {$img2}
Image 4 : {$img3}

══════════════════════════════════════════════════════
DESIGN SYSTEM
══════════════════════════════════════════════════════
Theme          : {$d['theme']} background
Background     : {$d['bg']}
Primary Color  : {$d['primary']}
Accent Color   : {$d['accent']}
Text Color     : {$d['text']}
Muted Color    : {$d['muted']}
Headline Font  : {$d['font_head']}
Body Font      : {$d['font_body']}
Hero Style     : {$d['hero_style']}
Visual Vibe    : {$d['vibe']}

══════════════════════════════════════════════════════
REQUIRED SECTIONS (strict order)
══════════════════════════════════════════════════════
1. STICKY NAVBAR
   - Business name (logo-styled, primary color) on left
   - Nav links: Home, About, Services, Gallery, Contact
   - "📞 Call Now" button on right (links to tel:{$phone})
   - Background: semi-transparent with backdrop-blur, border-bottom

2. HERO SECTION
   - Full-width, min-height: 90vh
   - Hero image as background: {$hero}
   - Theme-appropriate overlay (dark overlay for dark theme)
   - Business name as H1 (large, bold, headline font)
   - Tagline underneath (specific to {$category})
   - TWO CTA buttons: "Book Now" (tel:{$phone}) and "Learn More" (scroll to #about)
   - Style: {$d['hero_style']}

3. ABOUT SECTION (id="about")
   - Split layout (50/50 on desktop, stacked on mobile)
   - Left: image ({$img1}) in stylish frame with CSS shape/clip
   - Right: "About Us" heading, 2 paragraphs of realistic {$category} copy
   - Include a stats row: 3 impressive numbers (years, customers, rating etc.)

4. SERVICES SECTION (id="services") — "{$d['services_label']}"
   - {$d['items_hint']}
   - Cards with: emoji icon, title, description, subtle hover animation
   - Grid layout: 3 columns desktop, 2 tablet, 1 mobile

5. GALLERY SECTION (id="gallery")
   - Heading: "Our Gallery"
   - 3-image responsive grid using Image 2, 3, 4
   - Each image: rounded corners, hover zoom effect (transform: scale)
   - overflow-hidden on container

6. CONTACT SECTION (id="contact")
   - Two-column layout: info left, quick action buttons right
   - Left: address, phone, opening hours (Mon-Sat 9AM-9PM, Sun closed)
   - Right: 3 action buttons stacked:
     * "📞 Call Us" → tel:{$phone}
     * "💬 WhatsApp" → https://wa.me/{$wa}
     * "📍 Get Directions" → https://www.google.com/maps/search/?api=1&query={$mapsQ}

7. FOOTER
   - Dark background (even in light theme)
   - Business name + tagline
   - Quick links (Home, About, Services, Gallery, Contact)
   - "© {year} {$name} | Powered by AI"

8. FIXED BOTTOM ACTION BAR
   - position: fixed, bottom: 0, width: 100%, z-index: 9999
   - 3 equal buttons side by side: "📞 Call" | "💬 WhatsApp" | "📍 Map"
   - Links: tel:{$phone} | https://wa.me/{$wa} | https://www.google.com/maps/search/?api=1&query={$mapsQ}
   - Add padding-bottom: 70px to body so content isn't hidden behind it

══════════════════════════════════════════════════════
TECHNICAL REQUIREMENTS
══════════════════════════════════════════════════════
- Tailwind CSS CDN: <script src="https://cdn.tailwindcss.com"></script>
- Google Fonts: <link href="https://fonts.googleapis.com/css2?family={$fontUrl}&display=swap" rel="stylesheet">
- CSS variables in :root: --primary, --accent, --bg, --text, --muted
- All images: class="w-full h-full object-cover" with proper container heights
- Mobile responsive: everything works on 320px viewport
- Smooth scroll: html { scroll-behavior: smooth; }
- Animations: fade-in on scroll using Intersection Observer (vanilla JS, 20 lines max)
- NO external JS libraries except Tailwind CDN
- NO placeholder images — use ONLY the 4 provided image URLs
- Section padding: py-20 or py-24
- Max content width: max-w-7xl mx-auto px-6

══════════════════════════════════════════════════════
CONTENT REQUIREMENTS
══════════════════════════════════════════════════════
- Write 100% REALISTIC content specific to a {$category} in {$city}, India
- Service/menu items must be real names used in actual {$category} businesses
- Include realistic Indian pricing (₹) where appropriate
- Tagline must be specific and memorable (not generic)
- About section copy: mention {$city}, years in business, commitment to quality

══════════════════════════════════════════════════════
⚠ OUTPUT RULES — NON-NEGOTIABLE
══════════════════════════════════════════════════════
- Return ONLY the raw HTML document
- First character must be: <
- First line must be: <!DOCTYPE html>
- Last line must be: </html>
- ZERO markdown fences (no ```)
- ZERO explanations before or after
- ZERO TODO or placeholder comments
- Complete, working code — every section fully implemented
PROMPT;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────────────────────

    private function extractHTML(string $text): string
    {
        // Strip markdown code fences if AI wrapped output
        $text = preg_replace('/^```html\s*/im', '', $text);
        $text = preg_replace('/^```\s*/im', '', $text);
        $text = trim($text);

        // Find the actual HTML start
        $start = stripos($text, '<!DOCTYPE');
        if ($start === false) {
            $start = stripos($text, '<html');
        }

        if ($start !== false) {
            $text = substr($text, $start);
        }

        // Ensure it ends with </html>
        $end = strripos($text, '</html>');
        if ($end !== false) {
            $text = substr($text, 0, $end + 7);
        }

        return $text;
    }

    private function defaultDesign(): array
    {
        return [
            'theme'          => 'light',
            'bg'             => '#ffffff',
            'primary'        => '#3b82f6',
            'accent'         => '#1e3a8a',
            'text'           => '#0f172a',
            'muted'          => '#64748b',
            'font_head'      => 'Plus Jakarta Sans',
            'font_body'      => 'Plus Jakarta Sans',
            'hero_style'     => 'modern, clean, professional',
            'sections'       => 'hero,about,services,gallery,contact',
            'vibe'           => 'professional and trustworthy.',
            'services_label' => 'Our Services',
            'items_hint'     => '5 professional services with descriptions',
        ];
    }

    /**
     * Minimal working fallback page when AI fails entirely.
     */
    private function errorFallback(array $b, array $d, array $imgs): string
    {
        $name    = htmlspecialchars($b['name']);
        $phone   = htmlspecialchars($b['phone'] ?? '');
        $address = htmlspecialchars($b['address'] ?? $b['city'] ?? '');
        $hero    = $imgs[0] ?? '';
        $wa      = preg_replace('/[^0-9]/', '', $phone);
        $mapsQ   = urlencode($address);

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$name}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root { --primary: {$d['primary']}; }
        body { background: {$d['bg']}; color: {$d['text']}; }
    </style>
</head>
<body>
    <nav class="sticky top-0 z-50 bg-white/90 backdrop-blur border-b px-6 py-4 flex justify-between items-center">
        <span class="text-xl font-black" style="color:var(--primary)">{$name}</span>
        <a href="tel:{$phone}" class="text-white text-sm font-bold px-5 py-2 rounded-full" style="background:var(--primary)">📞 Call</a>
    </nav>
    <section class="relative h-screen">
        <img src="{$hero}" class="w-full h-full object-cover absolute inset-0">
        <div class="absolute inset-0 bg-black/60 flex items-center justify-center">
            <div class="text-center text-white px-6">
                <h1 class="text-6xl font-black mb-4">{$name}</h1>
                <p class="text-xl text-gray-300 mb-8">{$address}</p>
                <a href="tel:{$phone}" class="text-black font-bold px-8 py-4 rounded-full text-lg" style="background:var(--primary)">Book Now</a>
            </div>
        </div>
    </section>
    <section class="py-20 text-center px-6">
        <p class="text-lg text-gray-500">📍 {$address} &nbsp;|&nbsp; 📞 {$phone}</p>
    </section>
    <div class="fixed bottom-0 left-0 right-0 z-50 flex bg-white border-t shadow-xl">
        <a href="tel:{$phone}" class="flex-1 py-4 text-center text-white font-bold text-sm" style="background:var(--primary)">📞 Call</a>
        <a href="https://wa.me/{$wa}" class="flex-1 py-4 text-center text-white font-bold text-sm bg-green-500">💬 WhatsApp</a>
        <a href="https://www.google.com/maps/search/?api=1&query={$mapsQ}" class="flex-1 py-4 text-center text-white font-bold text-sm bg-gray-800">📍 Map</a>
    </div>
</body>
</html>
HTML;
    }
}