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
    $pythonPath = "C:\\Python314\\python.exe";
    $scriptPath = "D:\\internship\\google-maps-extractor - Copy\\scraper\\main.py";
    
    $command = "start /B \"\" \"{$pythonPath}\" \"{$scriptPath}\" \"{$this->query}\" \"{$this->searchId}\" \"{$this->offset}\"";
    pclose(popen($command, "r"));
}
}