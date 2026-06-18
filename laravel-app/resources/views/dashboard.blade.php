<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard | Lead Generator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .stat-card { transition: all 0.2s ease; }
        .stat-card:hover { transform: translateY(-2px); }
        @keyframes count-up { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
        .count-anim { animation: count-up 0.4s ease forwards; }
        .quick-action { transition: all 0.2s ease; }
        .quick-action:hover { transform: translateY(-3px); box-shadow: 0 12px 32px rgba(0,0,0,0.1); }
        @keyframes pulse-ring { 0% { transform: scale(1); opacity: 1; } 100% { transform: scale(2); opacity: 0; } }
        .status-dot { position: relative; display: inline-block; width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }
        .status-dot::after { content: ''; position: absolute; inset: 0; border-radius: 50%; animation: pulse-ring 1.2s ease-out infinite; }
        .dot-running  { background: #22c55e; }
        .dot-running::after  { background: #22c55e; }
        .dot-paused   { background: #f59e0b; }
        .dot-paused::after   { animation: none; }
        .dot-stopped  { background: #ef4444; }
        .dot-stopped::after  { animation: none; }
        .dot-done     { background: #6366f1; }
        .dot-done::after     { animation: none; }
    </style>
</head>

<body class="bg-[#f8fafc] text-slate-800">

<div class="flex min-h-screen">

    @include('partials.sidebar')

    {{-- MAIN CONTENT --}}
<div class="flex-1 ml-64">

    {{-- TOP HEADER --}}
    <div class="bg-white border-b border-slate-200 px-8 py-5 flex items-center justify-between sticky top-0 z-20">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Dashboard</h1>
            <p class="text-sm text-slate-500 mt-1">Welcome back, {{ auth()->user()->name ?? 'User' }}</p>
        </div>
        <div class="flex items-center gap-4">
            <a href="{{ route('search.page') }}"
               class="inline-flex items-center gap-2 bg-[#2563eb] hover:bg-[#1d4ed8] text-white font-semibold px-5 py-3 rounded-2xl shadow-sm transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Search
            </a>
            <a href="{{ route('profile.edit') }}"
   class="flex items-center gap-4 bg-white border border-slate-200 px-4 py-2 rounded-2xl shadow-sm hover:border-blue-300 transition-all">
    <div class="w-11 h-11 rounded-full bg-gradient-to-r from-purple-500 to-indigo-600 flex items-center justify-center text-white font-bold text-sm">
        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
    </div>
    <div class="hidden md:block">
        <p class="text-sm font-semibold text-slate-800">{{ auth()->user()->name ?? 'User' }}</p>
        <p class="text-xs text-slate-500">{{ auth()->user()->email ?? 'user@example.com' }}</p>
    </div>
    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/>
    </svg>
</a>
        </div>
    </div>

    {{-- PAGE CONTENT --}}
    <main class="p-8">

        {{-- STATS --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">

            {{-- TOTAL SEARCHES --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Total Searches</p>
                        {{-- ✅ ID ADDED for live polling --}}
                        <h3 id="stat-total-searches" class="text-3xl font-bold text-slate-800 mt-2">
                            {{ $totalSearches ?? 0 }}
                        </h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center">
                        <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16l4-4 4 4m0-8l-4 4-4-4"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- TOTAL LEADS --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Total Leads</p>
                        {{-- ✅ ID ADDED for live polling --}}
                        <h3 id="stat-total-leads" class="text-3xl font-bold text-slate-800 mt-2">
                            {{ $totalLeads ?? 0 }}
                        </h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-center">
                        <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-3-3v6m8 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- GENERATED SITES --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Generated Sites</p>
                        {{-- ✅ ID ADDED for live polling --}}
                        <h3 id="stat-generated-sites" class="text-3xl font-bold text-slate-800 mt-2">
                            {{ $generatedSites ?? 0 }}
                        </h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-purple-50 flex items-center justify-center">
                        <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.298-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m8 0H8m8 0v2a2 2 0 002 2h2a2 2 0 002-2V6a2 2 0 00-2-2h-2a2 2 0 00-2 2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- ACTIVE SEARCHES --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Active Searches</p>
                        {{-- ✅ ID ADDED for live polling --}}
                        <h3 id="stat-active-searches" class="text-3xl font-bold text-slate-800 mt-2">
                            {{ $activeSearches ?? 0 }}
                        </h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-orange-50 flex items-center justify-center">
                        <svg class="w-7 h-7 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                </div>
            </div>

        </div>

        {{-- QUICK ACTIONS --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">

            <a href="{{ route('search.page') }}"
                class="quick-action bg-white border border-slate-200 rounded-2xl p-5 flex flex-col items-center gap-3 text-center shadow-sm hover:border-blue-300">
                <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-slate-700 text-sm">New Search</p>
                    <p class="text-[11px] text-slate-400">Start scraping</p>
                </div>
            </a>

            <a href="{{ route('generate.page') }}"
                class="quick-action bg-white border border-slate-200 rounded-2xl p-5 flex flex-col items-center gap-3 text-center shadow-sm hover:border-purple-300">
                <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-slate-700 text-sm">AI Website</p>
                    <p class="text-[11px] text-slate-400">Generate site</p>
                </div>
            </a>

            <a href="{{ route('history.index') }}"
                class="quick-action bg-white border border-slate-200 rounded-2xl p-5 flex flex-col items-center gap-3 text-center shadow-sm hover:border-emerald-300">
                <div class="w-12 h-12 bg-emerald-500 rounded-xl flex items-center justify-center text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-slate-700 text-sm">View History</p>
                    <p class="text-[11px] text-slate-400">Past searches</p>
                </div>
            </a>

            @if($lastSearch)
            <a href="{{ route('export.leads', $lastSearch->id) }}"
                class="quick-action bg-white border border-slate-200 rounded-2xl p-5 flex flex-col items-center gap-3 text-center shadow-sm hover:border-amber-300">
                <div class="w-12 h-12 bg-amber-400 rounded-xl flex items-center justify-center text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-slate-700 text-sm">Export Leads</p>
                    <p class="text-[11px] text-slate-400">Download Excel</p>
                </div>
            </a>
            @else
            <div class="quick-action bg-white border border-dashed border-slate-200 rounded-2xl p-5 flex flex-col items-center gap-3 text-center opacity-50">
                <div class="w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center text-slate-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-slate-400 text-sm">Export Leads</p>
                    <p class="text-[11px] text-slate-300">No data yet</p>
                </div>
            </div>
            @endif

        </div>

        {{-- CHARTS --}}
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="font-extrabold text-slate-800 text-sm">Leads Growth</h2>
                        <p class="text-[11px] text-slate-400 mt-0.5">Leads collected per day</p>
                    </div>
                    <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                </div>
                <div class="h-56"><canvas id="leadsChart"></canvas></div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="font-extrabold text-slate-800 text-sm">Search Activity</h2>
                        <p class="text-[11px] text-slate-400 mt-0.5">Searches performed per day</p>
                    </div>
                    <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
                </div>
                <div class="h-56"><canvas id="searchChart"></canvas></div>
            </div>
        </div>

        {{-- RECENT SEARCHES --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h2 class="font-extrabold text-slate-800 text-sm">Recent Searches</h2>
                <a href="{{ route('history.index') }}" class="text-xs font-bold text-blue-600 hover:underline flex items-center gap-1">
                    View All
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            <div class="divide-y divide-slate-50">
                @forelse($recentSearches as $search)
                @php
                    $leadCount = $search->leads()->count();
                    $isCompleted = $search->is_stopped
                        && $search->total_places > 0
                        && $leadCount >= $search->total_places;

                    if ($isCompleted) {
                        $statusKey = 'done';    $statusLabel = 'Completed'; $statusColor = 'text-indigo-600 bg-indigo-50';
                    } elseif ($search->is_stopped) {
                        $statusKey = 'stopped'; $statusLabel = 'Stopped';   $statusColor = 'text-red-500 bg-red-50';
                    } elseif ($search->is_paused) {
                        $statusKey = 'paused';  $statusLabel = 'Paused';    $statusColor = 'text-amber-500 bg-amber-50';
                    } else {
                        $statusKey = 'running'; $statusLabel = 'Running';   $statusColor = 'text-emerald-600 bg-emerald-50';
                    }
                @endphp
                <div class="px-6 py-4 flex justify-between items-center hover:bg-slate-50/50 transition-colors group">
                    <div class="flex items-center gap-4 min-w-0">
                        <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center text-slate-400 group-hover:bg-blue-50 group-hover:text-blue-500 transition-all flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <div class="font-bold text-slate-700 text-sm truncate max-w-xs group-hover:text-blue-600 transition-colors">
                                {{ $search->query }}
                            </div>
                            <div class="flex items-center gap-2 mt-1 flex-wrap">
                                <span class="text-[11px] text-slate-400">{{ $search->created_at->diffForHumans() }}</span>
                                <span class="text-[10px] font-black text-blue-500 bg-blue-50 px-1.5 py-0.5 rounded">{{ $leadCount }} leads</span>
                                <span class="flex items-center gap-1 text-[10px] font-bold px-1.5 py-0.5 rounded {{ $statusColor }}">
                                    <span class="status-dot dot-{{ $statusKey }}"></span>
                                    {{ $statusLabel }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 flex-shrink-0 ml-4">
                        @if(($search->is_stopped || $search->is_paused) && !$isCompleted)
                        <form action="{{ route('search.resume', $search->id) }}" method="POST">
                            @csrf
                            <button class="text-[11px] font-bold text-emerald-600 bg-emerald-50 hover:bg-emerald-500 hover:text-white px-3 py-1.5 rounded-lg transition-all border border-emerald-200">
                                ▶ Resume
                            </button>
                        </form>
                        @endif
                        <a href="{{ route('results.show', $search->id) }}"
                            class="text-xs font-bold text-slate-600 bg-white border border-slate-200 px-4 py-2 rounded-xl hover:bg-[#2563eb] hover:text-white hover:border-[#2563eb] transition-all">
                            View Data
                        </a>
                    </div>
                </div>
                @empty
                <div class="p-12 text-center">
                    <p class="text-slate-400 italic text-sm mb-4">No searches yet.</p>
                    <a href="{{ route('search.page') }}" class="text-blue-600 font-bold text-sm hover:underline">Start your first search →</a>
                </div>
                @endforelse
            </div>
        </div>

    </main>
</div>
</div>

<script>
const leadsCtx = document.getElementById('leadsChart').getContext('2d');
new Chart(leadsCtx, {
    type: 'line',
    data: {
        labels:   {!! json_encode($leadsPerDay->pluck('date')) !!},
        datasets: [{
            label: 'Leads',
            data:  {!! json_encode($leadsPerDay->pluck('count')) !!},
            borderColor: '#2563eb', backgroundColor: 'rgba(37,99,235,0.08)',
            fill: true, tension: 0.45, borderWidth: 2.5,
            pointRadius: 4, pointBackgroundColor: '#2563eb',
            pointBorderColor: '#fff', pointBorderWidth: 2,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 } } },
            x: { grid: { display: false }, ticks: { font: { size: 11 } } }
        }
    }
});

const searchCtx = document.getElementById('searchChart').getContext('2d');
new Chart(searchCtx, {
    type: 'bar',
    data: {
        labels:   {!! json_encode($searchesPerDay->pluck('date')) !!},
        datasets: [{
            label: 'Searches',
            data:  {!! json_encode($searchesPerDay->pluck('count')) !!},
            backgroundColor: 'rgba(16,185,129,0.85)',
            borderRadius: 8, barThickness: 20,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 } } },
            x: { grid: { display: false }, ticks: { font: { size: 11 } } }
        }
    }
});

function toggleMenu() {
    document.getElementById("profileMenu").classList.toggle("hidden");
}

window.addEventListener('click', function(e) {
    const menu = document.getElementById("profileMenu");
    if (!e.target.closest('.relative') && menu) menu.classList.add("hidden");
});

// ── Live stats polling ─────────────────────────────────────
function updateDashboardStats() {
    fetch('{{ route('dashboard.stats') }}')
        .then(r => r.json())
        .then(data => {
            const el = id => document.getElementById(id);
            if (el('stat-total-searches'))  el('stat-total-searches').innerText  = data.totalSearches;
            if (el('stat-total-leads'))     el('stat-total-leads').innerText     = data.totalLeads;
            if (el('stat-generated-sites')) el('stat-generated-sites').innerText = data.generatedSites;
            if (el('stat-active-searches')) el('stat-active-searches').innerText = data.activeSearches;
        })
        .catch(() => {});
}

// Run immediately then every 10 seconds
updateDashboardStats();
setInterval(updateDashboardStats, 10000);
</script>

@include('partials.global-bar')
</body>
</html>