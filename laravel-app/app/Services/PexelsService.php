<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PexelsService
{
    /**
     * Category → Pexels search queries.
     * Tuned for business photography (not stock art).
     */
    private array $queryMap = [
        'restaurant'  => 'restaurant interior food',
        'cafe'        => 'cafe coffee shop interior',
        'hotel'       => 'luxury hotel lobby interior',
        'resort'      => 'resort pool luxury outdoor',
        'hospital'    => 'modern hospital medical clinic',
        'clinic'      => 'medical clinic doctor office',
        'gym'         => 'gym fitness workout interior',
        'fitness'     => 'fitness studio exercise equipment',
        'salon'       => 'hair salon beauty interior',
        'beauty'      => 'beauty salon spa interior',
        'pet_store'   => 'pet store animals grooming',
        'pharmacy'    => 'pharmacy medicine store',
        'school'      => 'school classroom education',
        'college'     => 'college campus education',
        'retail'      => 'retail store shopping interior',
        'shop'        => 'boutique store interior',
        'law_firm'    => 'law office professional modern',
        'real_estate' => 'luxury real estate property',
        'bakery'      => 'bakery pastry bread shop',
        'spa'         => 'spa wellness relaxation',
    ];

    /**
     * Fetch images from Pexels for a given business category.
     *
     * @param  string  $category  The business category key
     * @param  int     $count     Number of images to return
     * @return array<string>      Array of image URLs
     */
    public function fetchImages(string $category, int $count = 4): array
    {
        $query = $this->queryMap[$category] ?? ($category . ' business interior');

        try {
            $response = Http::timeout(15)
                ->withHeaders(['Authorization' => config('pexels.key')])
                ->get('https://api.pexels.com/v1/search', [
                    'query'       => $query,
                    'per_page'    => $count + 2, // fetch extra in case some fail
                    'orientation' => 'landscape',
                    'size'        => 'large',
                ]);

            if (!$response->successful()) {
                Log::warning("Pexels API returned {$response->status()} for query: {$query}");
                return $this->fallbacks($category, $count);
            }

            $photos = $response->json('photos', []);

            $urls = collect($photos)
                ->map(fn($p) => $p['src']['large2x'] ?? $p['src']['large'] ?? $p['src']['original'] ?? null)
                ->filter()
                ->take($count)
                ->values()
                ->toArray();

            if (count($urls) < $count) {
                return array_merge($urls, $this->fallbacks($category, $count - count($urls)));
            }

            return $urls;

        } catch (\Exception $e) {
            Log::error('PexelsService error: ' . $e->getMessage());
            return $this->fallbacks($category, $count);
        }
    }

    /**
     * Curated Unsplash fallbacks per category when Pexels fails.
     */
    private function fallbacks(string $category, int $count): array
    {
        $fallbackMap = [
            'restaurant'  => [
                'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=1400',
                'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=1400',
                'https://images.unsplash.com/photo-1424847651672-bf20a4b0982b?w=1400',
                'https://images.unsplash.com/photo-1466978913421-dad2ebd01d17?w=1400',
            ],
            'hospital'    => [
                'https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?w=1400',
                'https://images.unsplash.com/photo-1587351021759-3e566b6af7cc?w=1400',
                'https://images.unsplash.com/photo-1559757148-5c350d0d3c56?w=1400',
                'https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=1400',
            ],
            'gym'         => [
                'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=1400',
                'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=1400',
                'https://images.unsplash.com/photo-1583454110551-21f2fa2afe61?w=1400',
                'https://images.unsplash.com/photo-1593079831268-3381b0db4a77?w=1400',
            ],
            'hotel'       => [
                'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=1400',
                'https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?w=1400',
                'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?w=1400',
                'https://images.unsplash.com/photo-1568084680786-a84f91d1153c?w=1400',
            ],
            'salon'       => [
                'https://images.unsplash.com/photo-1560066984-138daaa70c17?w=1400',
                'https://images.unsplash.com/photo-1522338242992-e1a54906a8da?w=1400',
                'https://images.unsplash.com/photo-1600948836101-f9ffda59d250?w=1400',
                'https://images.unsplash.com/photo-1562322140-8baeececf3df?w=1400',
            ],
            'pet_store'   => [
                'https://images.unsplash.com/photo-1548767797-d8c844163c4a?w=1400',
                'https://images.unsplash.com/photo-1587300003388-59208cc962cb?w=1400',
                'https://images.unsplash.com/photo-1450778869180-41d0601e046e?w=1400',
                'https://images.unsplash.com/photo-1601758228041-f3b2795255f1?w=1400',
            ],
        ];

        $urls = $fallbackMap[$category] ?? [
            'https://images.unsplash.com/photo-1497366216548-37526070297c?w=1400',
            'https://images.unsplash.com/photo-1497366811353-6870744d04b2?w=1400',
            'https://images.unsplash.com/photo-1497366754035-f200968a6e72?w=1400',
            'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=1400',
        ];

        return array_slice($urls, 0, $count);
    }
}