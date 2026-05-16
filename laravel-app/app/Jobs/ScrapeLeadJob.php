<?php

namespace App\Jobs;

use App\Models\Lead;
use App\Models\Search;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ScrapeLeadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries   = 1;
    public $timeout = 3600;

    protected $searchId;
    protected $query;
    protected $offset;

    public function __construct($searchId, $query, $offset = 0)
    {
        $this->searchId = $searchId;
        $this->query    = $query;
        $this->offset   = $offset;
    }

    public function handle(): void
    {
        $search = Search::find($this->searchId);

        if (!$search) {
            Log::error("Search not found: " . $this->searchId);
            return;
        }

        // 🔥 Only reset counts if this is a fresh start, not a resume
        if ($this->offset === 0) {
            $search->update([
                'status'          => 'running',
                'processed_count' => 0,
                'total_places'    => 0,
                'is_stopped'      => false,
                'is_paused'       => false,
            ]);
        }

        Log::info("Scraping started: {$this->query} | Search ID: {$this->searchId} | Offset: {$this->offset}");

        $pythonPath = "C:\\Python314\\python.exe";
        $scriptPath = "D:\\internship\\google-maps-extractor - Copy\\scraper\\main.py";

        $offset  = (int) $this->offset;
$command = "\"{$pythonPath}\" \"{$scriptPath}\" \"{$this->query}\" {$this->searchId} {$offset}";

        Log::info("Running: " . $command);

        // 🔥 exec() blocks here — that's intentional
        // The job runs inside the queue worker, not inside your HTTP request
        // So blocking here is perfectly fine
        exec($command, $output, $exitCode);

        Log::info("Scraper exited with code: {$exitCode}");
        Log::info("Output: " . implode("\n", $output));

        // 🔥 Only mark completed if Python didn't get stopped mid-way
        $search->refresh();

        if (!$search->is_stopped) {
            $totalLeads = Lead::where('search_id', $this->searchId)->count();

            $search->update([
                'status'          => 'completed',
                'processed_count' => $totalLeads,
            ]);

            Log::info("Scraping completed. Total leads: {$totalLeads}");
        } else {
            Log::info("Scraping was stopped by user.");
        }
    }

    public function failed(\Throwable $e): void
    {
        Log::error("ScrapeLeadJob failed for search {$this->searchId}: " . $e->getMessage());

        Search::where('id', $this->searchId)->update([
            'status' => 'failed'
        ]);
    }
}