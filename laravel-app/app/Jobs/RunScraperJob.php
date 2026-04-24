<?php

namespace App\Jobs;

use App\Models\Search;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RunScraperJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $query;
    protected $searchId;

    public function __construct($query, $searchId)
    {
        $this->query = $query;
        $this->searchId = $searchId;
    }

    public function handle()
    {
        $pythonPath = "C:\\Python314\\python.exe";
        $scriptPath = "D:\\internship\\google-maps-extractor - Copy\\scraper\\main.py";

        $command = "\"$pythonPath\" \"$scriptPath\" \"$this->query\" \"$this->searchId\"";

        // ✅ RUN SILENTLY (NO TERMINAL)
        exec($command);
    }
}