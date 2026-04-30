<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\GeneratedSite;
use App\Services\AIWebsiteGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WebsiteController extends Controller
{
    // =========================================================================
    // USER-TRIGGERED GENERATION
    // Route: POST /generate
    // =========================================================================
    public function generate(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:100',
            'category'      => 'required|string|max:100',
        ]);

        $name     = trim($request->input('business_name'));
        $category = trim($request->input('category'));
        $city     = trim($request->input('city', 'Hyderabad'));
        $phone    = trim($request->input('phone', ''));
        $address  = trim($request->input('address', $city));
        $catKey   = strtolower(str_replace([' ', '-'], '_', $category));

        $pexels    = app(\App\Services\PexelsService::class);
        $images    = $pexels->fetchImages($catKey, 4);

        $generator = app(\App\Services\HTMLWebsiteGenerator::class);
        $html      = $generator->generate([
            'name'     => $name,
            'category' => $catKey,
            'city'     => $city,
            'phone'    => $phone,
            'address'  => $address,
        ], $images);

        $slug = $this->makeSlug($catKey, $name);

        $site = GeneratedSite::create([
            'slug'          => $slug,
            'business_name' => $name,
            'category'      => $catKey,
            'city'          => $city,
            'phone'         => $phone,
            'address'       => $address,
            'html_content'  => $html,
            'pexels_images' => $images,
            'metadata'      => [
                'color_scheme' => $request->input('color_scheme', 'auto'),
                'generated_at' => now()->toISOString(),
            ],
        ]);

        return redirect()->route('site.show', $site->slug);
    }

    // =========================================================================
    // SERVE SAVED GENERATED SITE
    // Route: GET /site/{slug}
    // =========================================================================
    public function serveSite(string $slug)
    {
        $site = GeneratedSite::where('slug', $slug)->firstOrFail();
        $site->incrementViews();

        return response($site->html_content, 200)
            ->header('Content-Type', 'text/html; charset=utf-8');
    }

    // =========================================================================
    // LEAD-BASED SITE
    // Route: GET /sites/{id}
    // =========================================================================
    public function show($id)
    {
        if (!is_numeric($id)) {
            $id = preg_replace('/[^0-9]/', '', $id);
        }

        $lead     = Lead::findOrFail($id);
        $metadata = json_decode($lead->ai_metadata, true) ?? [];

        // Check if rich AI data already exists (including opening_hours)
        $hasRichData = isset($metadata['about'])
            && isset($metadata['stats'])
            && isset($metadata['reviews'])
            && isset($metadata['services'])
            && isset($metadata['opening_hours']);

        if (!$hasRichData) {
            // Generate fresh content and cache it
            $aiData   = app(AIWebsiteGenerator::class)->generate($lead);
            $metadata = array_merge($metadata, $aiData);

            $lead->ai_metadata = json_encode($metadata);
            $lead->save();
        }

        // Build sections
        $sections = $metadata['sections'] ?? [];
        $sections = collect($sections)->unique('type')->values()->toArray();

        $required = ['hero', 'about', 'services', 'gallery', 'reviews', 'info', 'contact'];
        $existing = array_column($sections, 'type');

        foreach ($required as $type) {
            if (!in_array($type, $existing)) {
                $sections[] = ['type' => $type, 'title' => ucfirst($type)];
            }
        }

        $order = ['hero', 'about', 'services', 'gallery', 'reviews', 'info', 'contact'];
        usort($sections, function ($a, $b) use ($order) {
            $posA = array_search($a['type'], $order);
            $posB = array_search($b['type'], $order);
            return ($posA === false ? 999 : $posA) <=> ($posB === false ? 999 : $posB);
        });

        $data = [
            'tagline'       => $metadata['tagline']       ?? '',
            'colors'        => $metadata['colors']        ?? ['primary' => '#3b82f6', 'accent' => '#1e3a8a', 'bg' => '#ffffff'],
            'navbar'        => $metadata['navbar']        ?? ['Home', 'About', 'Services', 'Gallery', 'Contact'],
            'about'         => $metadata['about']         ?? [],
            'stats'         => $metadata['stats']         ?? [],
            'services'      => $metadata['services']      ?? [],
            'reviews'       => $metadata['reviews']       ?? [],
            'opening_hours' => $metadata['opening_hours'] ?? [],
            'images'        => $metadata['auto_images']   ?? [],
            'category'      => $this->detectCategory($lead->category ?? '', $lead->name ?? ''),
            'sections'      => $sections,
        ];

        return view('website.dynamic', compact('data', 'lead'));
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    private function detectCategory(string $rawCategory, string $name): string
    {
        $text = strtolower($rawCategory . ' ' . $name);

        $map = [
            'restaurant'  => ['restaurant','dining','food','cafe','coffee','biryani','diner','eatery','kitchen','dhaba','bar','pizza','burger','bakery','sweets','tiffin','mess','canteen','caterer','fast food','juice','family restaurant'],
            'hotel'       => ['hotel','resort','inn','lodge','suites','stay','rooms','accommodation','guest house','service apartment','heritage hotel'],
            'hospital'    => ['hospital','multispeciality','medical centre','nursing home','healthcare','medical college','super speciality','trauma','maternity'],
            'clinic'      => ['clinic','doctor','physician','specialist','dental','ortho','gynec','pediatric','dermat','eye care','ent','neurolog','cardiolog','ayurved','homeopath'],
            'gym'         => ['gym','fitness','crossfit','yoga','zumba','workout','sports','athletics','health club','martial arts','boxing','pilates','aerobics'],
            'salon'       => ['salon','beauty','spa','parlour','parlor','haircut','grooming','nail','skin care','cosmet','makeup','bridal','unisex','hair studio'],
            'pet_store'   => ['pet','veterinary','vet','animal','kennel','aquarium','dog','cat','bird','fish','pet shop'],
            'pharmacy'    => ['pharmacy','chemist','medical store','drug','medicine','pharmaceutical','medicals','medical hall'],
            'school'      => ['school','college','academy','institute','education','coaching','tutor','learning','preschool','kindergarten','cbse','icse'],
            'retail'      => ['shop','store','supermarket','mart','bazaar','showroom','boutique','retail','wholesale','electronics','mobile','textile','garments','furniture','hardware','jewel'],
            'law_firm'    => ['law','legal','advocate','attorney','solicitor','court','notary','lawyers'],
            'real_estate' => ['real estate','property','builder','developer','construction','realty','housing','plots','flats','apartments','interior','architects'],
        ];

        foreach ($map as $key => $keywords) {
            foreach ($keywords as $kw) {
                if (str_contains($text, $kw)) return $key;
            }
        }

        return 'business';
    }

    private function makeSlug(string $category, string $name): string
    {
        $base = Str::slug($category) . '-' . Str::slug(Str::limit($name, 30, ''));
        $slug = $base . '-' . strtolower(Str::random(5));

        while (GeneratedSite::where('slug', $slug)->exists()) {
            $slug = $base . '-' . strtolower(Str::random(5));
        }

        return $slug;
    }
}