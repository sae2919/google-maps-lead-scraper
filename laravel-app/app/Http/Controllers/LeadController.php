<?php

namespace App\Http\Controllers;

use App\Jobs\ScrapeLeadJob;
use App\Models\Search;
use App\Models\Lead;
use Illuminate\Http\Request;
use App\Exports\LeadsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use App\Services\AIWebsiteGenerator;
use App\Services\SearchService;
use App\Jobs\GenerateWebsiteJob;
use App\Models\GeneratedSite;

class LeadController extends Controller
{
    protected $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    // =========================================================================
    // SEARCH — start a new scrape
    // =========================================================================

    public function search(Request $request)
    {
        try {

            $request->validate([
                'query' => 'required|string|max:255'
            ]);

            $query = trim($request->input('query'));

            if (!$query) {

                return response()->json([
                    'success' => false,
                    'error'   => 'Query missing'
                ], 400);
            }

            $search = $this->searchService
                ->createSearch(
                    $query,
                    auth()->id()
                );

            return response()->json([
                'success' => true,
                'message' => 'Scraping started in background',
                'id'      => $search->id,
                'query'   => $query,
                'status'  => 'running',
                'total'   => 0
            ]);

        } catch (\Exception $e) {

            Log::error(
                'Search Error: ' .
                $e->getMessage()
            );

            return response()->json([
                'success' => false,
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // =========================================================================
    // STOP
    // =========================================================================

    public function stop($id)
    {
        $search = Search::where(
            'user_id',
            auth()->id()
        )->findOrFail($id);

        $this->searchService->stop($search);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'status' => 'stopped'
            ]);
        }

        return redirect()->back()->with('success', 'Scraping stopped!');
    }

    // =========================================================================
    // PAUSE
    // =========================================================================

    public function pause($id)
    {
        $search = Search::where(
            'user_id',
            auth()->id()
        )->findOrFail($id);

        $this->searchService->pause($search);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'status' => 'paused'
            ]);
        }

        return redirect()->back()->with('success', 'Scraping paused!');
    }

    // =========================================================================
    // RESUME
    // =========================================================================

    public function resume($id)
    {
        $search = Search::where(
            'user_id',
            auth()->id()
        )->findOrFail($id);

        $this->searchService->resume($search);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'status' => 'resumed'
            ]);
        }

        return redirect()->route('results.show', $id)->with('success', 'Scraping resumed!');
    }

    // =========================================================================
    // STATUS — called by Python scraper
    // =========================================================================

    // =========================================================================
// STATUS — called by Python scraper
// =========================================================================

public function checkStatus($id)
{
    $search = Search::find($id);

    if (!$search) {

        return response()->json([
            'stopped' => true,
            'paused'  => false
        ]);
    }

    return response()->json([

        'stopped' => (
            $search->stopped ||
            $search->is_stopped
        ),

        'paused' => (
            $search->paused ||
            $search->is_paused
        ),

        'progress' => $search->progress,

        'total_places' => $search->total_places
    ]);
}

    // =========================================================================
    // PROGRESS
    // =========================================================================

    public function progress($id)
    {
        $search = Search::where(
            'user_id',
            auth()->id()
        )->findOrFail($id);

        $found = (int) $search->processed_count;

        $total = (int) ($search->total_places ?? 0);

        $progressPercent = 0;

        if ($total > 0) {

            $progressPercent = round(
                ($found / $total) * 100
            );

            if ($progressPercent > 100) {
                $progressPercent = 100;
            }
        }

        if ($search->is_stopped) {

            $status = 'STOPPED';

        } elseif ($search->is_paused) {

            $status = 'PAUSED';

        } elseif (
            $total > 0 &&
            $found >= $total
        ) {

            $status = 'COMPLETED';

        } else {

            $status = 'RUNNING';
        }

        return response()->json([

            'found'    => $found,

            'progress' => $progressPercent,

            'total'    => $total,

            'status'   => $status
        ]);
    }

    // =========================================================================
    // LAUNCH SCRAPER
    // =========================================================================

    private function launchScraper(
        string $query,
        int $searchId,
        int $offset = 0
    ): void
    {
        $pythonPath = env('PYTHON_PATH');

        $scriptPath = env('SCRAPER_SCRIPT');

        $command = "start /B \"\" \"{$pythonPath}\" \"{$scriptPath}\" \"{$query}\" \"{$searchId}\" \"{$offset}\"";

        pclose(
            popen($command, "r")
        );
    }

    // =========================================================================
    // UPDATE TOTAL
    // =========================================================================

    public function updateTotal(Request $request)
    {
        Search::where(
            'id',
            $request->search_id
        )->update([

            'total_places' =>
            (int) $request->total_places
        ]);

        return response()->json([
            'success' => true
        ]);
    }

    // =========================================================================
    // SAVE LEAD
    // =========================================================================

    public function saveLead(Request $request)
{
    try {

        $data = $request->all();

        Log::info('SAVE LEAD HIT', $data);

        if (empty($data['maps_url'])) {

            return response()->json([
                'success' => false,
                'message' => 'maps_url missing'
            ], 400);
        }

        if (isset($data['types'])) {

            $data['types'] = is_array($data['types'])
                ? json_encode($data['types'])
                : $data['types'];
        }

        Lead::updateOrCreate(

            [
                'maps_url'  => $data['maps_url'],
                'search_id' => $data['search_id']
            ],

            [

                'search_id' => $data['search_id'] ?? null,

                'name' => $data['business_name']
                    ?? $data['name']
                    ?? 'Unknown Business',

                'phone' => $data['phone'] ?? null,

                'email' => $data['email'] ?? null,

                'website' => $data['website'] ?? null,

                'address' => $data['address'] ?? null,

                'main_area' => $data['main_area'] ?? null,

                'pincode' => $data['pincode'] ?? null,

                'maps_url' => $data['maps_url'] ?? null,

                'rating' => $data['rating'] ?? null,

                'types' => $data['types'] ?? null,
            ]
        );

        Search::where(
            'id',
            $data['search_id']
        )->increment('processed_count');

        return response()->json([
            'success' => true
        ]);

    } catch (\Throwable $e) {

        Log::error('SAVE LEAD ERROR: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'error'   => $e->getMessage()
        ], 500);
    }
}

    // =========================================================================
    // RESULTS PAGE
    // =========================================================================

    public function results($id)
    {
        $search = Search::where(
            'user_id',
            auth()->id()
        )->findOrFail($id);

        return view(
            'results',
            compact('search')
        );
    }

    // =========================================================================
    // DASHBOARD
    // =========================================================================

    public function dashboard()
{
    $userId = auth()->id();

    return view('dashboard', [
        'totalSearches' => Search::where('user_id', $userId)->count(),

        'totalLeads' => Lead::whereHas('search', fn($q) => $q->where('user_id', $userId))->count(),

        'generatedSites' => \App\Models\GeneratedSite::whereIn(
    'business_name',
    Lead::whereHas('search', fn($q) => $q->where('user_id', $userId))->pluck('name')
)->where('generation_status', 'done')->count(),

        'activeSearches' => Search::where('user_id', $userId)
            ->where('is_stopped', false)
            ->where('is_paused', false)
            ->count(),

        'lastSearch'     => Search::where('user_id', $userId)->latest()->first(),
        'recentSearches' => Search::where('user_id', $userId)->latest()->limit(5)->get(),

        'searchesPerDay' => Search::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('user_id', $userId)->groupBy('date')->get(),

        'leadsPerDay' => Lead::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereHas('search', fn($q) => $q->where('user_id', $userId))
            ->groupBy('date')->get(),
    ]);
}
public function dashboardStats()
{
    $userId = auth()->id();

    return response()->json([
        'totalSearches'  => Search::where('user_id', $userId)->count(),
        'totalLeads'     => Lead::whereHas('search', fn($q) => $q->where('user_id', $userId))->count(),
        'generatedSites' => \App\Models\GeneratedSite::whereIn('business_name',
            Lead::whereHas('search', fn($q) => $q->where('user_id', $userId))->pluck('name')
        )->where('generation_status', 'done')->count(),
        'activeSearches' => Search::where('user_id', $userId)
            ->where('is_stopped', false)
            ->where('is_paused', false)
            ->count(),
    ]);
}

    // =========================================================================
    // HISTORY
    // =========================================================================

    public function history()
    {
        return view('history', [

            'searches' => Search::where(
                'user_id',
                auth()->id()
            )
                ->latest()
                ->get()
        ]);
    }

    // =========================================================================
    // DELETE SEARCH
    // =========================================================================

    public function deleteSearch($id)
    {
        $search = Search::where(
            'user_id',
            auth()->id()
        )->find($id);

        if (!$search) {

            return back()->with(
                'error',
                'Search not found'
            );
        }

        Lead::where(
            'search_id',
            $id
        )->delete();

        $search->delete();

        return back()->with(
            'success',
            'Deleted successfully'
        );
    }

    // =========================================================================
    // DELETE ALL SEARCHES
    // =========================================================================

    public function deleteAllSearches()
    {
        $userId = auth()->id();

        $searchIds = Search::where(
            'user_id',
            $userId
        )->pluck('id');

        Lead::whereIn(
            'search_id',
            $searchIds
        )->delete();

        Search::where(
            'user_id',
            $userId
        )->delete();

        return redirect('/history')
            ->with(
                'success',
                'All history cleared.'
            );
    }

    // =========================================================================
    // DELETE SINGLE LEAD
    // =========================================================================

    public function delete($id)
    {
        $lead = Lead::findOrFail($id);

        $search = Search::where(
            'user_id',
            auth()->id()
        )->findOrFail($lead->search_id);

        $lead->delete();

        return response()->json([
            'success' => true
        ]);
    }

    // =========================================================================
    // DELETE ALL LEADS
    // =========================================================================

    // =========================================================================
// DELETE ALL LEADS
// =========================================================================

public function deleteAll(Request $request)
{
    Search::where(
        'user_id',
        auth()->id()
    )->findOrFail($request->search_id);

    Lead::where(
        'search_id',
        $request->search_id
    )->delete();

    return response()->json([
        'success' => true
    ]);
}

    // =========================================================================
    // EXPORT
    // =========================================================================

    public function export($id)
    {
        return Excel::download(
            new LeadsExport($id),
            'leads.xlsx'
        );
    }

    public function exportFiltered(Request $request)
    {
        return Excel::download(
            new LeadsExport(
                null,
                $request->ids
            ),
            'leads-filtered.xlsx'
        );
    }

    // =========================================================================
    // CONTACT FORM
    // =========================================================================

    public function contactSubmit(Request $request)
    {
        Log::info(
            "New Lead Contact",
            $request->all()
        );

        return back()->with(
            'success',
            'Message sent!'
        );
    }

    // =========================================================================
    // BULK AI WEBSITE GENERATION
    // =========================================================================

    public function generateBulkWebsites(Request $request)
{
    try {

        $ids = $request->input('ids');

        if (empty($ids)) {

            return response()->json([
                'success' => false,
                'message' => 'No leads selected'
            ], 400);
        }

        foreach ($ids as $id) {

            GenerateWebsiteJob::dispatch($id);
        }

        return response()->json([

            'success' => true,

            'message' => 'AI website generation started in background',

            'count' => count($ids)
        ]);

    } catch (\Exception $e) {

        Log::error(
            'Bulk AI Queue Error: '
            . $e->getMessage()
        );

        return response()->json([

            'success' => false,

            'message' => $e->getMessage()
        ], 500);
    }
}

    // =========================================================================
    // HELPERS
    // =========================================================================

   

    

    // =========================================================================
    // GET LEADS
    // =========================================================================

    public function getLeads(
        Request $request,
        $id
    )
    {
        $query = Lead::where(
            'search_id',
            $id
        )->orderBy('id', 'asc');

        // SOURCE FILTER

        if (
            $request->source === 'no_website'
        ) {

            $query->where(function ($q) {

                $q->whereNull('website')
                    ->orWhere('website', '')
                    ->orWhereRaw('TRIM(website) = ""')
                    ->orWhere('website', '-');
            });
        }

        if (
            $request->source === 'has_website'
        ) {

            $query->whereNotNull('website')
                ->where('website', '!=', '');
        }

        // RATING FILTER

        if (
            $request->rating &&
            $request->rating > 0
        ) {

            $query->where(
                'rating',
                '>=',
                $request->rating
            );
        }

        $leads = $query->paginate(10);

        return response()->json($leads);
    }

    // =========================================================================
    // SEO PAGE
    // =========================================================================

    public function seoPage(
    $city,
    $service
)
{
    $intro = "
    Discover the best {$service} businesses in {$city}.
    Our AI-powered platform helps users find trusted
    local businesses, generate leads, and improve
    online business visibility.
    ";

    $benefits = [

        "Verified {$service} businesses in {$city}",

        "AI-powered lead generation system",

        "Professional business discovery platform",

        "SEO-optimized business visibility",

        "Smart automation for local business growth"
    ];

    $faq = [

        [
            'q' => "How to find the best {$service} in {$city}?",

            'a' => "Our AI platform helps discover top-rated {$service} businesses in {$city}."
        ],

        [
            'q' => "Can AI generate business leads?",

            'a' => "Yes. Businesses can generate leads and websites using AI automation."
        ]
    ];

    $content = [

        'title' => ucfirst($service)
            . ' Services in '
            . ucfirst($city),

        'description' => "Find the best {$service} businesses in {$city} using our AI-powered lead generation platform.",

        'intro' => $intro,

        'benefits' => $benefits,

        'faq' => $faq
    ];

    return view(
        'seo.page',
        compact(
            'city',
            'service',
            'content'
        )
    );
}
    public function viewGeneratedSite(string $slug)
{
    $site = \App\Models\GeneratedSite::where('slug', $slug)->firstOrFail();

    if (in_array($site->generation_status, ['pending', 'generating'])) {
        return view('website.loading', compact('site'));
    }

    if ($site->generation_status === 'failed') {
        return view('website.failed', compact('site'));
    }

    // Handle double-encoded JSON
    $raw = $site->ai_config;
    if (is_array($raw)) {
        $config = $raw;
    } else {
        $config = json_decode($raw, true);
        if (is_string($config)) {
            $config = json_decode($config, true);
        }
    }

    if (empty($config) || !is_array($config)) {
        return back()->with('error', 'Config empty. Try regenerating.');
    }

    return view('website.dynamic', compact('config', 'site'));
}

public function sitemap()
{
    $cities = config('seo.cities');

$services = config('seo.services');

    return response()->view(
        'seo.sitemap',
        compact(
            'cities',
            'services'
        )
    )->header(
        'Content-Type',
        'application/xml'
    );
}

public function generatedSites()
{
    $userId = auth()->id();

    $businessNames = Lead::whereHas('search', fn($q) => $q->where('user_id', $userId))
        ->pluck('name')
        ->toArray();

    $sites = \App\Models\GeneratedSite::whereIn('business_name', $businessNames)
        ->latest()
        ->paginate(12);

    return view('website.index', compact('sites'));
}

public function generateSite(Request $request, Lead $lead)
{
    $existing = \App\Models\GeneratedSite::where('business_name', $lead->name)->first();

    if ($existing && $existing->generation_status === 'generating') {
        return back()->with('info', 'Website is already being generated.');
    }

    if (!$existing) {
        $existing = \App\Models\GeneratedSite::create([
            'slug'              => \Str::slug($lead->name) . '-' . \Str::random(4),
            'business_name'     => $lead->name,
            'category'          => $lead->type     ?? $lead->category ?? '',
            'city'              => $lead->city      ?? '',
            'phone'             => $lead->phone     ?? '',
            'address'           => $lead->address   ?? '',
            'html_content'      => '',
            'generation_status' => 'pending',
        ]);
    } else {
        $existing->update(['generation_status' => 'pending']);
    }

    \App\Jobs\GenerateWebsiteJob::dispatch($lead->id);

    return redirect()
        ->route('site.view', $existing->slug)
        ->with('success', 'AI website generation started!');
}

public function regenerateSite(\App\Models\GeneratedSite $site)
{
    $lead = Lead::where('name', $site->business_name)->first();

    if (!$lead) {
        return back()->with('error', 'Original lead not found. Cannot regenerate.');
    }

    $site->update(['generation_status' => 'pending']);

    \App\Jobs\GenerateWebsiteJob::dispatch($lead->id);

    return redirect()
        ->route('site.view', $site->slug)
        ->with('success', 'Regenerating with a fresh AI design...');
}

public function searchPage()
{
    return view('search'); // your search blade
}

public function generatePage()
{
    return view('website.form');
}
public function generateFromForm(Request $request)
{
    $request->validate([
        'business_name' => 'required|string|max:255',
        'category'      => 'required|string|max:100',
    ]);

    $name    = $request->input('business_name');
    $slug    = \Str::slug($name) . '-' . \Str::random(4);

    // Create GeneratedSite directly from form data
    $site = \App\Models\GeneratedSite::create([
        'slug'              => $slug,
        'business_name'     => $name,
        'category'          => $request->input('category', ''),
        'city'              => $request->input('city', ''),
        'phone'             => $request->input('phone', ''),
        'address'           => $request->input('address', ''),
        'html_content'      => '',
        'generation_status' => 'generating',
    ]);

    // Generate AI config directly (synchronous)
    $aiService = app(\App\Services\AIWebsiteService::class);

    $config = $aiService->generateConfig([
        'name'     => $name,
        'type'     => $request->input('category', ''),
        'category' => $request->input('category', ''),
        'phone'    => $request->input('phone', ''),
        'address'  => $request->input('address', ''),
        'city'     => $request->input('city', ''),
    ]);

    $site->update([
        'ai_config'         => $config,
        'generation_status' => 'done',
        'generated_at'      => now(),
    ]);

    return redirect()->route('site.view', $site->slug)
        ->with('success', 'Website generated successfully!');
}
}