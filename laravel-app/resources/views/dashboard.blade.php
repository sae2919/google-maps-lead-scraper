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

    {{-- SIDEBAR --}}
    <div class="w-64 bg-white border-r border-slate-200 p-4 flex flex-col shadow-sm fixed h-full z-30">
        <div class="flex items-center gap-3 px-2 mb-8">
            <div class="w-10 h-10 bg-[#2563eb] rounded-lg flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-blue-100">LG</div>
            <span class="font-bold text-slate-700 tracking-tight text-lg">Lead Generator</span>
        </div>

        <nav class="space-y-1 flex-1">
            <a href="{{ route('search.page') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition-all font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Home
            </a>

            <a href="/dashboard" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-[#eff6ff] text-[#2563eb] font-semibold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
                Dashboard
            </a>

            <a href="/history" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition-all font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                History
            </a>

            <a href="/generate" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition-all font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
                AI Generator
            </a>
        </nav>

        <div class="pt-4 border-t border-slate-100">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:bg-red-50 hover:text-red-500 transition-all font-medium w-full text-left">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </div>

    {{-- MAIN --}}
    <div class="flex-1 ml-64 flex flex-col">

        {{-- TOPBAR --}}
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-8 sticky top-0 z-40">
            <div>
                <h1 class="text-lg font-extrabold text-slate-800 tracking-tight">Dashboard</h1>
                <p class="text-xs text-slate-400 font-medium">Welcome back, {{ auth()->user()->name ?? 'User' }} 👋</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('search.page') }}"
                    class="bg-[#2563eb] text-white px-5 py-2 rounded-xl text-sm font-bold hover:bg-blue-700 transition shadow-md shadow-blue-100 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Search
                </a>

                <div class="w-px h-6 bg-slate-200"></div>

                <div class="relative">
                    <button onclick="toggleMenu()"
                        class="w-9 h-9 rounded-full bg-purple-600 text-white flex items-center justify-center font-bold text-sm hover:ring-4 ring-purple-50 transition-all">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                    </button>
                    <div id="profileMenu" class="hidden absolute right-0 mt-3 w-48 bg-white border border-slate-100 rounded-2xl shadow-xl z-50 py-2">
                        <div class="px-4 py-2 text-xs font-bold text-slate-400 uppercase tracking-widest">Account</div>
                        <a href="/profile" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 font-medium">👤 Profile Settings</a>
                        <hr class="my-1 border-slate-100">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 font-bold">🚪 Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="p-8 space-y-8">

            {{-- STAT CARDS --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">

                <div class="stat-card bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:border-blue-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-11 h-11 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <span class="text-[10px] font-black text-blue-400 bg-blue-50 px-2 py-1 rounded-lg uppercase tracking-widest">All time</span>
                    </div>
                    <p class="text-[11px] font-black text-slate-400 uppercase tracking-[0.15em] mb-1">Total Searches</p>
                    <h2 class="text-3xl font-black text-slate-800 count-anim">{{ $totalSearches }}</h2>
                </div>

                <div class="stat-card bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:border-emerald-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-11 h-11 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <span class="text-[10px] font-black text-emerald-500 bg-emerald-50 px-2 py-1 rounded-lg uppercase tracking-widest">Scraped</span>
                    </div>
                    <p class="text-[11px] font-black text-slate-400 uppercase tracking-[0.15em] mb-1">Total Leads</p>
                    <h2 class="text-3xl font-black text-slate-800 count-anim">{{ number_format($totalLeads) }}</h2>
                </div>

                @php
                    $websiteCount = \App\Models\Lead::whereNotNull('ai_metadata')
                        ->whereNotNull('website')->where('website', '!=', '')->count();
                @endphp
                <div class="stat-card bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:border-purple-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-11 h-11 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                        </div>
                        <span class="text-[10px] font-black text-purple-500 bg-purple-50 px-2 py-1 rounded-lg uppercase tracking-widest">AI</span>
                    </div>
                    <p class="text-[11px] font-black text-slate-400 uppercase tracking-[0.15em] mb-1">Websites Built</p>
                    <h2 class="text-3xl font-black text-slate-800 count-anim">{{ $websiteCount }}</h2>
                </div>

                <div class="stat-card bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:border-amber-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-11 h-11 bg-amber-50 text-amber-500 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="text-[10px] font-black text-amber-500 bg-amber-50 px-2 py-1 rounded-lg uppercase tracking-widest">Latest</span>
                    </div>
                    <p class="text-[11px] font-black text-slate-400 uppercase tracking-[0.15em] mb-1">Last Search</p>
                    <h2 class="text-sm font-black text-slate-700 leading-tight truncate mt-1">
                        {{ $lastSearch->query ?? 'No activity yet' }}
                    </h2>
                    @if($lastSearch)
                        <p class="text-[10px] text-slate-400 mt-1">{{ $lastSearch->created_at->diffForHumans() }}</p>
                    @endif
                </div>

            </div>

            {{-- QUICK ACTIONS --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

                {{-- ✅ FIXED: was href="/" which redirects back to dashboard --}}
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

                <a href="/generate"
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

                <a href="/history"
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
                <a href="/export/{{ $lastSearch->id }}"
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
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
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
                    <a href="/history" class="text-xs font-bold text-blue-600 hover:underline flex items-center gap-1">
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
                            <form action="/resume-search/{{ $search->id }}" method="POST">
                                @csrf
                                <button class="text-[11px] font-bold text-emerald-600 bg-emerald-50 hover:bg-emerald-500 hover:text-white px-3 py-1.5 rounded-lg transition-all border border-emerald-200">
                                    ▶ Resume
                                </button>
                            </form>
                            @endif
                            <a href="/results/{{ $search->id }}"
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
            borderColor:     '#2563eb',
            backgroundColor: 'rgba(37,99,235,0.08)',
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
            x: { grid: { display: false },                      ticks: { font: { size: 11 } } }
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
            x: { grid: { display: false },                      ticks: { font: { size: 11 } } }
        }
    }
});

function toggleMenu() {
    document.getElementById("profileMenu").classList.toggle("hidden");
}

window.addEventListener('click', function(e) {
    const menu = document.getElementById("profileMenu");
    if (!e.target.closest('.relative') && menu) {
        menu.classList.add("hidden");
    }
});
</script>

</body>
</html>