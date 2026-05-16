<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RunScraperJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600;
    public $tries   = 1;

    public function __construct(
        public string $query,
        public int    $searchId,
        public int    $offset = 0
    ) {}

    public function handle(): void
{
    try {

        // 🔥 Update status to processing
        \App\Models\Search::where('id', $this->searchId)
            ->update([
                'status' => 'processing'
            ]);

        $pythonPath = "C:\\Python314\\python.exe";

        $scriptPath = "D:\\internship\\google-maps-extractor - Copy\\scraper\\main.py";

        /**
         * ============================================
         * 🔥 NORMAL EXECUTION (WAIT FOR COMPLETION)
         * ============================================
         */

        $command = "\"{$pythonPath}\" \"{$scriptPath}\" \"{$this->query}\" \"{$this->searchId}\" \"{$this->offset}\"";

        exec($command, $output, $resultCode);

        /**
         * ============================================
         * 🔥 UPDATE FINAL STATUS
         * ============================================
         */

        $search = \App\Models\Search::find($this->searchId);

        if ($search) {

            $totalLeads = \App\Models\Lead::where(
                'search_id',
                $this->searchId
            )->count();

            $search->update([

                'status' => 'completed',

                'processed_count' => $totalLeads,

                'total_places' => $totalLeads

            ]);
        }

    } catch (\Exception $e) {

        \Log::error($e->getMessage());

        \App\Models\Search::where('id', $this->searchId)
            ->update([
                'status' => 'failed'
            ]);
    }
}
}