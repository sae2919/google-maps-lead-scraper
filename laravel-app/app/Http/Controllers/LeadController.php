<?php

namespace App\Http\Controllers;

use App\Models\Search;
use App\Models\Lead;
use Illuminate\Http\Request;
use App\Exports\LeadsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

class LeadController extends Controller
{
    /**
     * 🔥 START SEARCH
     * Triggers the Python scraper in the background
     */
    public function search(Request $request)
    {
        try {
            $query = $request->input('query');

            if (!$query) {
                return response()->json(['error' => 'Query missing'], 400);
            }

            // Create a new search record
            $search = Search::create([
                'query' => $query,
                'user_id' => auth()->id() ?? 1,
                'is_stopped' => false,
                'is_paused' => false,
                'total_places' => 0
            ]);

            // Define Scraper Paths (Update these based on your environment)
            $pythonPath = "C:\\Python314\\python.exe";
            $scriptPath = "D:\\internship\\google-maps-extractor - Copy\\scraper\\main.py";

            // Command to run Python script in background (Windows style)
            $command = "start /B \"\" \"$pythonPath\" \"$scriptPath\" \"$query\" \"$search->id\"";

            Log::info("Launching Scraper:", ['cmd' => $command]);

            pclose(popen($command, "r"));

            return response()->json(['id' => $search->id]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * 🔥 RESULTS VIEW
     */
    public function results($id)
    {
        $search = Search::findOrFail($id);
        return view('results', compact('id'));
    }

    /**
     * 🔥 API: PROGRESS & STATUS (Crucial for Dashboard UI)
     */
    public function progress($id)
    {
        $search = Search::find($id);

        if (!$search) {
            return response()->json(['error' => 'Search not found'], 404);
        }

        // Count leads currently saved in the database
        $progressCount = Lead::where('search_id', $id)->count();
        
        // The total number of results found by the scraper (if reported)
        $totalFound = $search->total_places ?? 0;

        // Logic to determine current status string for the UI
        $status = 'SCRAPING...';
        
        if ($search->is_stopped) {
            $status = 'STOPPED';
        } elseif ($search->is_paused) {
            $status = 'PAUSED';
        } elseif ($totalFound > 0 && $progressCount >= $totalFound) {
            $status = 'COMPLETED';
        }

        return response()->json([
            'progress' => $progressCount,
            'total' => $totalFound,
            'status' => $status, 
            'stopped' => $search->is_stopped ?? false,
            'paused' => $search->is_paused ?? false
        ]);
    }

    /**
     * 🔥 API: GET LEADS (Paginated for the Table)
     */
    public function getLeads($id)
    {
        return response()->json(
            Lead::where('search_id', $id)
                ->orderBy('id', 'asc') // Chronological order
                ->paginate(10)
        );
    }

    /**
     * 🔥 SAVE LEAD (Endpoint for Python Scraper)
     */
    public function saveLead(Request $request)
    {
        $data = $request->all();

        if (!isset($data['search_id'])) {
            return response()->json(['error' => 'Missing search_id'], 400);
        }

        Lead::firstOrCreate(
            [
                'maps_url' => $data['maps_url'],
                'search_id' => $data['search_id']
            ],
            [
                'name'      => $data['name'] ?? null,
                'phone'     => $data['phone'] ?? null,
                'email'     => $data['email'] ?? null,
                'website'   => $data['website'] ?? null,
                'address'   => $data['address'] ?? null,
                'main_area' => $data['main_area'] ?? null,
                'pincode'   => $data['pincode'] ?? null,
                'rating'    => $data['rating'] ?? null,
            ]
        );

        return response()->json(['status' => 'saved']);
    }

    /**
     * 🔥 UPDATE TOTAL (Endpoint for Python Scraper to report initial find count)
     */
    public function updateTotal(Request $request)
    {
        $search = Search::find($request->search_id);

        if ($search) {
            $search->total_places = $request->total_places;
            $search->save();
        }

        return response()->json(['status' => 'updated']);
    }

    /**
     * 🔥 CONTROL ACTIONS
     */
    public function stop($id)
    {
        Search::findOrFail($id)->update(['is_stopped' => true, 'is_paused' => false]);
        return response()->json(['status' => 'stopped']);
    }

    public function pause($id)
    {
        Search::findOrFail($id)->update(['is_paused' => true]);
        return response()->json(['status' => 'paused']);
    }

    public function resume($id)
    {
        Search::findOrFail($id)->update(['is_paused' => false]);
        return response()->json(['status' => 'resumed']);
    }

    /**
     * 🔥 DASHBOARD STATS & CHARTS
     */
    public function dashboard()
    {
        $userId = auth()->id() ?? 1;

        return view('dashboard', [
            'totalSearches'  => Search::where('user_id', $userId)->count(),
            'totalLeads'     => Lead::count(),
            'lastSearch'     => Search::where('user_id', $userId)->latest()->first(),
            'recentSearches' => Search::where('user_id', $userId)->latest()->limit(5)->get(),
            'leadsPerDay'    => Lead::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                                    ->groupBy('date')->orderBy('date')->get(),
            'searchesPerDay' => Search::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                                    ->where('user_id', $userId)
                                    ->groupBy('date')->orderBy('date')->get(),
        ]);
    }

    /**
     * 🔥 HISTORY
     */
    public function history()
    {
        $searches = Search::where('user_id', auth()->id() ?? 1)->latest()->get();
        return view('history', compact('searches'));
    }

    /**
     * 🔥 EXPORT METHODS
     */
    public function export($id)
    {
        return Excel::download(new LeadsExport($id), 'leads.xlsx');
    }

    public function exportFiltered(Request $request)
    {
        $data = $request->input('data');
        return Excel::download(new class($data) implements \Maatwebsite\Excel\Concerns\FromArray {
            protected $data;
            public function __construct($data) { $this->data = $data; }
            public function array(): array { return $this->data; }
        }, 'filtered_leads.xlsx');
    }
}