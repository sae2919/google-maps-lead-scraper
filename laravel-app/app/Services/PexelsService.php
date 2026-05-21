<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PexelsService
{
    /**
     * Category → Pexels search queries.
     * Tuned for business photography.
     */
    private array $queryMap = [

        'restaurant'  => 'restaurant interior food',

        'cafe'        => 'cafe coffee shop interior',

        'hotel'       => 'luxury hotel lobby interior',

        'resort'      => 'resort swimming pool luxury',

        'hospital'    => 'modern hospital medical clinic',

        'clinic'      => 'doctor medical clinic',

        'gym'         => 'gym fitness workout interior',

        'fitness'     => 'fitness studio exercise equipment',

        'salon'       => 'beauty salon luxury interior',

        'beauty'      => 'beauty spa salon',

        'pet_store'   => 'pet store animals grooming',

        'pharmacy'    => 'pharmacy medicine healthcare',

        'school'      => 'modern school classroom',

        'college'     => 'college university campus',

        'retail'      => 'retail store shopping interior',

        'shop'        => 'modern boutique store',

        'law_firm'    => 'law office professional',

        'real_estate' => 'luxury apartment building',

        'bakery'      => 'bakery pastry bread',

        'spa'         => 'spa wellness relaxation',

        'business'    => 'modern office business',
    ];

    /**
     * Fetch optimized images.
     */
    public function getImages(
        string $category,
        string $name,
        int $count = 4
    ): array
    {
        $query = $this->queryMap[$category]
            ?? ($category . ' business');

        try {

            $response = Http::timeout(20)

                ->retry(3, 1000)

                ->withHeaders([

                    'Authorization' => config('pexels.key')

                ])->get(
                    'https://api.pexels.com/v1/search',
                    [

                        'query'       => $query,

                        'per_page'    => $count + 4,

                        'orientation' => 'landscape',

                        'size'        => 'large',

                        'page'        => 1,
                    ]
                );

            if (!$response->successful()) {

                Log::warning(
                    "Pexels failed for {$query}"
                );

                return $this->fallbacks(
                    $category,
                    $count
                );
            }

            $photos = $response->json(
                'photos',
                []
            );

            $images = collect($photos)

                ->map(function ($photo) {

                    return
                        $photo['src']['large2x']
                        ?? $photo['src']['large']
                        ?? $photo['src']['medium']
                        ?? null;
                })

                ->filter()

                ->unique()

                ->take($count)

                ->values()

                ->toArray();

            if (count($images) < $count) {

                $fallbacks = $this->fallbacks(

                    $category,

                    $count - count($images)
                );

                $images = array_merge(
                    $images,
                    $fallbacks
                );
            }

            return array_values(
                array_unique($images)
            );

        } catch (\Exception $e) {

            Log::error(
                'PexelsService Error: '
                . $e->getMessage()
            );

            return $this->fallbacks(
                $category,
                $count
            );
        }
    }

    /**
     * Backup images if Pexels fails.
     */
    private function fallbacks(
        string $category,
        int $count
    ): array
    {
        $fallbackMap = [

            'restaurant' => [

                'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=1400',

                'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=1400',

                'https://images.unsplash.com/photo-1466978913421-dad2ebd01d17?w=1400',

                'https://images.unsplash.com/photo-1552566626-52f8b828add9?w=1400',
            ],

            'hospital' => [

                'https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?w=1400',

                'https://images.unsplash.com/photo-1587351021759-3e566b6af7cc?w=1400',

                'https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=1400',

                'https://images.unsplash.com/photo-1538108149393-fbbd81895907?w=1400',
            ],

            'gym' => [

                'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=1400',

                'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=1400',

                'https://images.unsplash.com/photo-1593079831268-3381b0db4a77?w=1400',

                'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?w=1400',
            ],

            'hotel' => [

                'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=1400',

                'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1400',

                'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?w=1400',

                'https://images.unsplash.com/photo-1522798514-97ceb8c4f1c8?w=1400',
            ],

            'salon' => [

                'https://images.unsplash.com/photo-1560066984-138daaa70c17?w=1400',

                'https://images.unsplash.com/photo-1522338242992-e1a54906a8da?w=1400',

                'https://images.unsplash.com/photo-1487412720507-e7ab37603c6f?w=1400',

                'https://images.unsplash.com/photo-1600948836101-f9ffda59d250?w=1400',
            ],

            'business' => [

                'https://images.unsplash.com/photo-1497366754035-f200968a6e72?w=1400',

                'https://images.unsplash.com/photo-1497366811353-6870744d04b2?w=1400',

                'https://images.unsplash.com/photo-1497366216548-37526070297c?w=1400',

                'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=1400',
            ],
        ];

        $urls = $fallbackMap[$category]

            ?? $fallbackMap['business'];

        return array_slice(
            $urls,
            0,
            $count
        );
    }
}