<?php

namespace App\Jobs;

use App\Models\GeneratedSite;
use App\Models\Lead;
use App\Services\AIWebsiteService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GenerateWebsiteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 1;
    public int $timeout = 300; // 5 minutes — covers 5 retries × 25s wait

    public function __construct(public readonly int $leadId)
    {
    }

    public function handle(AIWebsiteService $aiService): void
    {
        $lead = Lead::find($this->leadId);

        if (!$lead) {
            Log::warning("GenerateWebsiteJob: Lead #{$this->leadId} not found.");
            return;
        }

        Log::info("GenerateWebsiteJob: Starting for [{$lead->name}]");

        // ── Build slug from business name ───────────────────────
        $slug = Str::slug($lead->name) . '-' . Str::random(4);

        // ── Find existing site by slug match or create new ──────
        // generated_sites has no lead_id — match on business_name
        $site = GeneratedSite::where('business_name', $lead->name)->first()
             ?? new GeneratedSite();

        // ── Populate all direct columns from lead ───────────────
        $site->slug              = $site->exists ? $site->slug : $slug;
        $site->business_name     = $lead->name;
        $site->category          = $lead->type     ?? $lead->category ?? '';
        $site->city              = $lead->city      ?? '';
        $site->phone             = $lead->phone     ?? '';
        $site->address           = $lead->address   ?? '';
        $site->html_content      = $site->html_content ?? '';
        $site->generation_status = 'generating';
        $site->save();

        try {
            // ── Build business data array for AI ────────────────
            $businessData = [
                'name'     => $lead->name,
                'type'     => $lead->type     ?? $lead->category ?? '',
                'category' => $lead->category ?? $lead->type     ?? '',
                'rating'   => $lead->rating   ?? null,
                'reviews'  => $lead->reviews  ?? null,
                'phone'    => $lead->phone     ?? '',
                'address'  => $lead->address   ?? '',
                'city'     => $lead->city      ?? '',
                'website'  => $lead->website   ?? '',
            ];

            // ── Call Gemini AI ──────────────────────────────────
            $config = $aiService->generateConfig($businessData);

            // ── Save AI config ──────────────────────────────────
            $site->ai_config = $config;  // model cast handles encoding
            $site->generation_status = 'done';
            $site->generated_at      = now();
            $site->save();
            $lead->website = url('/dashboard/generated-site/' . $site->slug);
$lead->save();

            Log::info("GenerateWebsiteJob: Done [{$lead->name}] → /dashboard/generated-site/{$site->slug}");

        } catch (\Throwable $e) {
            $site->generation_status = 'failed';
            $site->save();

            Log::error("GenerateWebsiteJob: Failed for [{$lead->name}]", [
                'error' => $e->getMessage(),
                'line'  => $e->getLine(),
                'file'  => $e->getFile(),
            ]);

            throw $e;
        }
    }
}