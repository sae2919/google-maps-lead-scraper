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

class LeadController extends Controller
{
    // =========================================================================
    // SEARCH — start a new scrape
    // =========================================================================
    

public function search(Request $request)
{
    try {

        $query = trim($request->input('query'));

        if (!$query) {
            return response()->json([
                'success' => false,
                'error'   => 'Query missing'
            ], 400);
        }

        $search = Search::create([
            'query'           => $query,
            'user_id'         => auth()->id() ?? 1,
            'status'          => 'running',
            'is_stopped'      => false,
            'is_paused'       => false,
            'processed_count' => 0,
            'total_places'    => 0,
        ]);

        ScrapeLeadJob::dispatch($search->id, $query);

        return response()->json([
            'success' => true,
            'message' => 'Scraping started in background',
            'id'      => $search->id,
            'query'   => $query,
            'status'  => 'running',
            'total'   => 0
        ]);

    } catch (\Exception $e) {

        Log::error('Search Error: ' . $e->getMessage());

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
        Search::findOrFail($id)->update(['is_stopped' => true, 'is_paused' => false]);
        return response()->json(['status' => 'stopped']);
    }

    // =========================================================================
    // PAUSE
    // =========================================================================
    public function pause($id)
    {
        Search::where('id', $id)->update(['is_paused' => true, 'is_stopped' => false]);
        return response()->json(['status' => 'paused']);
    }

    // =========================================================================
    // RESUME
    // =========================================================================
    public function resume($id)
{
    $search = Search::findOrFail($id);

    $search->update([
        'is_stopped' => false,
        'is_paused'  => false,
        'status'     => 'running'
    ]);

    ScrapeLeadJob::dispatch($search->id, $search->query);

    return response()->json([
        'status' => 'resumed'
    ]);
}

    // =========================================================================
    // STATUS — called by Python scraper
    // =========================================================================
    public function checkStatus($id)
    {
        $search = Search::find($id);
        if (!$search) return response()->json(['stopped' => true, 'paused' => false]);

        return response()->json([
            'stopped' => (bool) $search->is_stopped,
            'paused'  => (bool) $search->is_paused,
            'status'  => $search->is_stopped ? 'STOPPED' : ($search->is_paused ? 'PAUSED' : 'RUNNING'),
        ]);
    }

    // =========================================================================
    // PROGRESS — polled by frontend
    // =========================================================================
    public function progress($id)
{
    $search = Search::find($id);

    if (!$search) {

        return response()->json([
            'progress' => 0,
            'total'    => 0,
            'found'    => 0,
            'status'   => 'NOT_FOUND'
        ]);
    }

    // 🔥 FOUND LEADS
    $found = Lead::where(
        'search_id',
        $id
    )->count();

    // 🔥 TOTAL FROM PYTHON
    $total = (int) (
        $search->total_places ?? 0
    );

    // 🔥 PROGRESS %
    $progressPercent = 0;

    if ($total > 0) {

        $progressPercent = round(
            ($found / $total) * 100
        );

        if ($progressPercent > 100) {
            $progressPercent = 100;
        }
    }

    // 🔥 STATUS
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
    // FIND THIS:


// REPLACE WITH:
private function launchScraper(string $query, int $searchId, int $offset = 0): void
{
    $pythonPath = "C:\\Python314\\python.exe";
    $scriptPath = "D:\\internship\\google-maps-extractor - Copy\\scraper\\main.py";
    $command    = "start /B \"\" \"{$pythonPath}\" \"{$scriptPath}\" \"{$query}\" \"{$searchId}\" \"{$offset}\"";
    pclose(popen($command, "r"));
}

    // =========================================================================
    // UPDATE TOTAL — called by Python after counting links
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
    // SAVE LEAD — called by Python
    // =========================================================================
    public function saveLead(Request $request)
{
    $data = $request->all();

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
            'maps_url' => $data['maps_url'],
            'search_id' => $data['search_id']
        ],
        $data
    );

    Search::where('id', $data['search_id'])
        ->increment('processed_count');

    return response()->json([
        'success' => true
    ]);
}

    // =========================================================================
    // PAGES
    // =========================================================================
    public function results($id)  { return view('results', compact('id')); }

    public function dashboard()
    {
        $userId = auth()->id() ?? 1;
        return view('dashboard', [
            'totalSearches'  => Search::where('user_id', $userId)->count(),
            'totalLeads'     => Lead::count(),
            'lastSearch'     => Search::where('user_id', $userId)->latest()->first(),
            'recentSearches' => Search::where('user_id', $userId)->latest()->limit(5)->get(),
            'searchesPerDay' => Search::selectRaw('DATE(created_at) as date, COUNT(*) as count')->where('user_id', $userId)->groupBy('date')->get(),
            'leadsPerDay'    => Lead::selectRaw('DATE(created_at) as date, COUNT(*) as count')->groupBy('date')->get(),
        ]);
    }

    public function history()
    {
        return view('history', [
            'searches' => Search::where('user_id', auth()->id() ?? 1)->latest()->get()
        ]);
    }

    // =========================================================================
    // DELETE — single search + its leads (used from history page)
    // =========================================================================
    public function deleteSearch($id)
{
    $search = Search::find($id);

    if (!$search) {
        return back()->with('error', 'Search not found');
    }

    Lead::where('search_id', $id)->delete();

    $search->delete();

    return back()->with('success', 'Deleted successfully');
}

    // =========================================================================
    // DELETE ALL — all searches + leads for current user
    // =========================================================================
    public function deleteAllSearches()
    {
        $userId    = auth()->id() ?? 1;
        $searchIds = Search::where('user_id', $userId)->pluck('id');
        Lead::whereIn('search_id', $searchIds)->delete();
        Search::where('user_id', $userId)->delete();
        return redirect('/history')->with('success', 'All history cleared.');
    }

    // =========================================================================
    // DELETE SINGLE LEAD — (used from results table if needed)
    // =========================================================================
    public function delete($id)
    {
        Lead::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    public function deleteAll(Request $request)
    {
        Lead::where('search_id', $request->search_id)->delete();
        return response()->json(['success' => true]);
    }

    // =========================================================================
    // EXPORT
    // =========================================================================
    public function export($id)
    {
        return Excel::download(new LeadsExport($id), 'leads.xlsx');
    }

    public function exportFiltered(Request $request)
    {
        return Excel::download(new LeadsExport(null, $request->ids), 'leads-filtered.xlsx');
    }

    public function contactSubmit(Request $request)
    {
        Log::info("New Lead Contact", $request->all());
        return back()->with('success', 'Message sent!');
    }

    // =========================================================================
    // BULK AI WEBSITE GENERATION
    // =========================================================================
    public function generateBulkWebsites(Request $request)
    {
        try {
            $ids = $request->input('ids');
            if (empty($ids)) return response()->json(['success' => false, 'message' => 'No leads selected'], 400);
            if (!config('gemini.api_key')) return response()->json(['success' => false, 'message' => 'Gemini API Key is missing'], 500);

            set_time_limit(500);
            $count = 0;

            foreach ($ids as $id) {
                $lead = Lead::find($id);
                if (!$lead) continue;

                $detectedCategory = $this->detectBusinessType($lead);
                $area = $lead->main_area ?? 'your local area';

                $prompt = "You are an expert AI website builder.
Business Name: {$lead->name}
Category: {$detectedCategory}
Location: {$area}

Generate a COMPLETE dynamic website structure.
CATEGORY RULES:
Pet Store: Navbar: Home, Pets, Products, Adoption, Contact | Sections: hero, pets, products, adoption, services, contact
Restaurant: Navbar: Home, Menu, Offers, Gallery, Contact | Sections: hero, menu, dishes, offers, gallery, contact
Gym: Navbar: Home, Programs, Trainers, Pricing, Contact | Sections: hero, programs, trainers, pricing, contact
Hospital: Navbar: Home, Doctors, Services, Emergency, Contact | Sections: hero, doctors, services, emergency, contact
Business: Navbar: Home, Services, About, Contact | Sections: hero, services, about, contact

STRICT JSON FORMAT:
{\"layout_style\":\"modern\",\"navbar\":[\"...\"],\"colors\":{\"bg\":\"#hex\",\"primary\":\"#hex\",\"accent\":\"#hex\"},\"sections\":[{\"type\":\"hero\",\"title\":\"...\",\"body\":\"...\"},{\"type\":\"services\",\"items\":[{\"title\":\"...\",\"desc\":\"...\"}]}]}
Return ONLY JSON.";

                $url      = config('gemini.base_uri') . 'models/' . config('gemini.model') . ':generateContent?key=' . config('gemini.api_key');
                $response = Http::timeout(config('gemini.timeout'))->withHeaders(['Content-Type' => 'application/json'])
                    ->post($url, ["contents" => [["parts" => [["text" => $prompt]]]]]);

                $decoded = null;
                if ($response->successful()) {
                    $rawText = trim(preg_replace('/```json|```/', '', $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? ''));
                    preg_match('/\{.*\}/s', $rawText, $matches);
                    $decoded = json_decode($matches[0] ?? '', true);
                }

                if (!$decoded || !isset($decoded['sections'])) {
                    $decoded = ["layout_style" => "modern", "navbar" => ["Home","Services","Contact"], "colors" => ["bg"=>"#ffffff","primary"=>"#3b82f6","accent"=>"#1e3a8a"],
                        "sections" => [["type"=>"hero","title"=>$lead->name,"body"=>"Best {$detectedCategory} in {$area}"],["type"=>"services","items"=>[["title"=>"Quality Service","desc"=>"We deliver the best"]]]]];
                }

                $imageArray = $this->getDynamicImages($detectedCategory, $lead->name);
                $slug = Str::slug($lead->name . '-' . $lead->id);
                $lead->slug = $slug;
                $lead->save();

                Lead::where('id', $id)->update([
                    'website'     => url('/sites/' . $slug . '?source=auto_v5&v=' . time()),
                    'ai_metadata' => json_encode(['design' => $decoded, 'auto_images' => $imageArray, 'category' => $detectedCategory, 'generated_at' => now()->toDateTimeString()])
                ]);

                $count++;
                usleep(300000);
            }

            return response()->json(['success' => true, 'message' => "Generated {$count} AI websites!", 'count' => $count]);

        } catch (\Exception $e) {
            Log::error("AI Bulk Build Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // =========================================================================
    // HELPERS
    // =========================================================================
    private function getDynamicImages($category, $name): array
    {
        $images = [];
        try {
            $response = Http::withHeaders(['Authorization' => config('pexels.key')])
                ->get('https://api.pexels.com/v1/search', [
                    'query'    => $category === 'hospital' ? "doctor hospital patient" : "{$category} {$name}",
                    'per_page' => 10,
                    'page'     => rand(1, 5),
                ]);
            if ($response->successful()) {
                foreach ($response->json()['photos'] ?? [] as $photo) $images[] = $photo['src']['large'];
            }
        } catch (\Exception $e) {}

        $images = array_values(array_unique(array_filter($images)));
        if (count($images) < 4) {
            $fallback = ["https://images.pexels.com/photos/8376293/pexels-photo-8376293.jpeg","https://images.pexels.com/photos/7088487/pexels-photo-7088487.jpeg","https://images.pexels.com/photos/8460373/pexels-photo-8460373.jpeg","https://images.pexels.com/photos/4386466/pexels-photo-4386466.jpeg"];
            shuffle($fallback);
            $images = array_merge($images, array_slice($fallback, 0, 4 - count($images)));
        }
        return array_slice($images, 0, 4);
    }

    private function detectBusinessType($lead): string
    {
        if (!empty($lead->types)) {
            $types = is_array($lead->types) ? $lead->types : json_decode($lead->types, true);
            if ($types) {
                foreach ($types as $type) {
                    if (str_contains($type, 'hospital') || str_contains($type, 'health')) return 'hospital';
                    if (str_contains($type, 'restaurant') || str_contains($type, 'food'))  return 'restaurant';
                    if (str_contains($type, 'hotel') || str_contains($type, 'lodging'))    return 'hotel';
                    if (str_contains($type, 'gym'))    return 'gym';
                    if (str_contains($type, 'school')) return 'education';
                    if (str_contains($type, 'store'))  return 'store';
                }
            }
        }
        $name = strtolower($lead->name ?? '');
        if (str_contains($name, 'hospital') || str_contains($name, 'clinic') || str_contains($name, 'medical')) return 'hospital';
        if (str_contains($name, 'restaurant') || str_contains($name, 'food') || str_contains($name, 'dhaba'))   return 'restaurant';
        if (str_contains($name, 'gym') || str_contains($name, 'fitness'))   return 'gym';
        if (str_contains($name, 'school') || str_contains($name, 'college')) return 'education';
        if (str_contains($name, 'pet')) return 'store';
        return 'business';
    }

    public function getLeads(Request $request, $id)
    {
        $query = Lead::where('search_id', $id)
    ->orderBy('id', 'asc');


// 🔥 SOURCE FILTER
if ($request->source === 'no_website') {

    $query->where(function ($q) {

        $q->whereNull('website')
          ->orWhere('website', '')
          ->orWhereRaw('TRIM(website) = ""')
          ->orWhere('website', '-');

    });

}

if ($request->source === 'has_website') {

    $query->whereNotNull('website')
          ->where('website', '!=', '');

}


// 🔥 RATING FILTER
if ($request->rating && $request->rating > 0) {

    $query->where('rating', '>=', $request->rating);

}


// ✅ FILTER FIRST → PAGINATE
$leads = $query->paginate(10);

return response()->json($leads);
    }

    public function seoPage($city, $service)
    {
        return view('seo.page', compact('city', 'service'));
    }
    
}