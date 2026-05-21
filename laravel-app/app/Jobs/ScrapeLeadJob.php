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

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ScrapeLeadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    public $timeout = 3600;

    protected $searchId;

    protected $query;

    protected $offset;

    public function __construct(
        $searchId,
        $query,
        $offset = 0
    ) {
        $this->searchId = $searchId;

        $this->query = $query;

        $this->offset = $offset;
    }

    public function handle(): void
    {
        try {

            $search = Search::find(
                $this->searchId
            );

            if (!$search) {

                Log::error(
                    "Search not found: {$this->searchId}"
                );

                return;
            }

            // ─────────────────────────────────────────────
            // RESET DATA
            // ─────────────────────────────────────────────

            if ($this->offset === 0) {

                Lead::where(
                    'search_id',
                    $this->searchId
                )->delete();

                $search->update([

                    'status' => 'running',

                    'processed_count' => 0,

                    'progress' => 0,

                    'total_places' => 0,

                    'is_stopped' => false,

                    'is_paused' => false,

                    'stopped' => false,

                    'paused' => false,
                ]);
            }

            Log::info(
                "======================================"
            );

            Log::info(
                "SCRAPING STARTED"
            );

            Log::info(
                "Query: {$this->query}"
            );

            Log::info(
                "Search ID: {$this->searchId}"
            );

            Log::info(
                "Offset: {$this->offset}"
            );

            Log::info(
                "======================================"
            );

            // ─────────────────────────────────────────────
            // PYTHON PATH
            // ─────────────────────────────────────────────

            $pythonPath =
                "C:\\Users\\91756\\AppData\\Local\\Programs\\Python\\Python312\\python.exe";

            // ─────────────────────────────────────────────
            // SCRIPT PATH
            // ─────────────────────────────────────────────

            $scriptPath =
                "D:\\internship\\google-maps-extractor - Copy\\scraper\\main.py";

            $offset = (int) $this->offset;

            Log::info(
                "Starting Python Process"
            );

            // ─────────────────────────────────────────────
            // PROCESS
            // ─────────────────────────────────────────────

            $process = new Process([

                $pythonPath,

                $scriptPath,

                $this->query,

                (string) $this->searchId,

                (string) $offset

            ]);

            $process->setTimeout(null);

            $process->run(function (
                $type,
                $buffer
            ) {

                Log::info(trim($buffer));

            });

            if (!$process->isSuccessful()) {

                throw new ProcessFailedException(
                    $process
                );
            }

            Log::info(
                "Python process completed"
            );

            // ─────────────────────────────────────────────
            // REFRESH SEARCH
            // ─────────────────────────────────────────────

            $search->refresh();

            $totalLeads = Lead::where(
                'search_id',
                $this->searchId
            )->count();

            Log::info(
                "Total leads saved: {$totalLeads}"
            );

            // ─────────────────────────────────────────────
            // STOPPED
            // ─────────────────────────────────────────────

            if (
                $search->is_stopped ||
                $search->stopped
            ) {

                $search->update([

                    'status' => 'stopped',

                    'processed_count' => $totalLeads,
                ]);

                Log::info(
                    "Scraping stopped by user."
                );

                return;
            }

            // ─────────────────────────────────────────────
            // FAILED
            // ─────────────────────────────────────────────

            if ($totalLeads <= 0) {

                $search->update([

                    'status' => 'failed',

                    'processed_count' => 0,
                ]);

                Log::error(
                    "No leads inserted into database."
                );

                return;
            }

            // ─────────────────────────────────────────────
            // SUCCESS
            // ─────────────────────────────────────────────

            $search->update([

                'status' => 'completed',

                'processed_count' => $totalLeads,

                'progress' => 100,
            ]);

            Log::info(
                "Scraping completed successfully."
            );

            Log::info(
                "Final leads count: {$totalLeads}"
            );

        } catch (\Throwable $e) {

            Log::error(
                "======================================"
            );

            Log::error(
                "SCRAPE JOB FAILED"
            );

            Log::error(
                $e->getMessage()
            );

            Log::error(
                $e->getTraceAsString()
            );

            Log::error(
                "======================================"
            );

            Search::where(
                'id',
                $this->searchId
            )->update([

                'status' => 'failed'
            ]);
        }
    }

    public function failed(
        \Throwable $e
    ): void {

        Log::error(
            "ScrapeLeadJob failed for search {$this->searchId}: "
            . $e->getMessage()
        );

        Search::where(
            'id',
            $this->searchId
        )->update([

            'status' => 'failed'
        ]);
    }
}