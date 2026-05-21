<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateWebsiteJob;
use App\Models\GeneratedSite;
use App\Models\Lead;
use App\Services\AIWebsiteService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WebsiteController extends Controller
{
    public function __construct(private AIWebsiteService $aiService)
    {
    }

    // ──────────────────────────────────────────────────────────
    // GENERATE  →  POST /dashboard/leads/{lead}/generate-website
    // ──────────────────────────────────────────────────────────
    public function generate(Request $request, Lead $lead)
    {
        $existing = GeneratedSite::where('business_name', $lead->name)->first();

        if ($existing && $existing->generation_status === 'generating') {
            return back()->with('info', 'Website is already being generated.');
        }

        if (!$existing) {
            $existing = GeneratedSite::create([
                'slug'              => Str::slug($lead->name) . '-' . Str::random(4),
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

        GenerateWebsiteJob::dispatch($lead->id);

        return redirect()
            ->route('website.show', $existing->slug)
            ->with('success', 'AI website generation started!');
    }

    // ──────────────────────────────────────────────────────────
    // SHOW  →  GET /dashboard/generated-site/{slug}
    // ──────────────────────────────────────────────────────────
    public function show(string $slug)
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

    // ──────────────────────────────────────────────────────────
    // REGENERATE  →  POST /dashboard/generated-site/{site}/regenerate
    // ──────────────────────────────────────────────────────────
    public function regenerate(GeneratedSite $site)
    {
        // Find the lead by business_name to get the lead ID
        $lead = Lead::where('name', $site->business_name)->first();

        if (!$lead) {
            return back()->with('error', 'Original lead not found. Cannot regenerate.');
        }

        $site->update(['generation_status' => 'pending']);

        GenerateWebsiteJob::dispatch($lead->id);

        return redirect()
            ->route('website.show', $site->slug)
            ->with('success', 'Regenerating with a fresh AI design...');
    }

    // ──────────────────────────────────────────────────────────
    // STATUS  →  GET /dashboard/generated-site/{slug}/status
    // JSON polling endpoint for loading page
    // ──────────────────────────────────────────────────────────
    public function status(string $slug)
    {
        $site = GeneratedSite::where('slug', $slug)
            ->select('generation_status', 'generated_at')
            ->firstOrFail();

        return response()->json([
            'status'       => $site->generation_status,
            'done'         => $site->generation_status === 'done',
            'generated_at' => $site->generated_at?->toISOString(),
        ]);
    }
    public function generateBulk(Request $request)
{
    $ids = $request->input('ids', []);

    if (empty($ids)) {
        return response()->json(['message' => 'No leads selected.'], 422);
    }

    $dispatched = 0;

    foreach ($ids as $id) {
        $lead = Lead::find($id);
        if (!$lead) continue;

        // Skip if already generating
        $existing = GeneratedSite::where('business_name', $lead->name)->first();
        if ($existing && $existing->generation_status === 'generating') continue;

        // Create record if not exists
        if (!$existing) {
            GeneratedSite::create([
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

        GenerateWebsiteJob::dispatch($lead->id);
        $dispatched++;
    }

    return response()->json([
        'message' => "✅ Generating AI websites for {$dispatched} businesses. Check back in 15-20 seconds."
    ]);
}
}