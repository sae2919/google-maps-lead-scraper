<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Search History | Lead Generator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }

        @keyframes pulse-ring {
            0%   { transform: scale(1); opacity: 1; }
            100% { transform: scale(2); opacity: 0; }
        }

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
    @include('partials.sidebar')
        

    <div class="flex-1 ml-64 flex flex-col">

        {{-- TOPBAR --}}
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-8 sticky top-0 z-40">
            <h1 class="text-lg font-extrabold text-slate-800 tracking-tight">Search History</h1>

            <div class="flex items-center gap-4">
                <form action="/delete-all-searches" method="POST" onsubmit="return confirmDeleteAll()">
                    @csrf
                    <button class="text-xs font-bold text-red-500 hover:text-red-700 uppercase tracking-wider px-3 py-1.5 hover:bg-red-50 rounded-lg transition-all">
                        Clear All History
                    </button>
                </form>
                <div class="w-px h-6 bg-slate-200"></div>
                @auth
                <div class="relative">
                    <button onclick="toggleProfileMenu()"
                        class="w-9 h-9 rounded-full bg-purple-600 text-white flex items-center justify-center font-bold text-sm hover:ring-4 ring-purple-50 transition-all">
                        {{ strtoupper(substr(auth()->user()->name ?? 'AJ', 0, 2)) }}
                    </button>
                    <div id="profileMenu" class="hidden absolute right-0 mt-3 w-48 bg-white rounded-xl shadow-xl border border-slate-100 z-50 py-2">
                        <a href="/profile" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 font-medium">👤 Edit Profile</a>
                        <hr class="my-1 border-slate-100">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 font-bold">🚪 Logout</button>
                        </form>
                    </div>
                </div>
                @endauth
            </div>
        </header>

        <main class="p-8 max-w-5xl mx-auto w-full pb-24">

            <div class="mb-8 flex items-center justify-between flex-wrap gap-4">
                <p class="text-slate-500 text-sm">Manage and revisit your previous lead extraction tasks.</p>
                <span class="text-xs font-black text-slate-400 uppercase tracking-widest bg-slate-100 px-3 py-1.5 rounded-lg">
                    {{ $searches->count() }} {{ Str::plural('search', $searches->count()) }}
                </span>
            </div>

            {{-- SEARCH FILTER --}}
            <div class="relative mb-6">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input
                    type="text"
                    id="searchInput"
                    placeholder="Filter by search query..."
                    class="w-full pl-12 pr-4 py-3.5 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-blue-400 focus:outline-none transition-all shadow-sm text-sm"
                    onkeyup="filterHistory()">
            </div>

            {{-- HISTORY LIST --}}
            <div id="historyList" class="space-y-3">

                @forelse($searches as $search)
                @php
                    $leadCount   = $search->leads()->count() ?? 0;
                    $isCompleted = $search->total_places > 0
    && $leadCount >= $search->total_places;

                    if ($isCompleted) {
                        $statusKey   = 'done';
                        $statusLabel = 'Completed';
                        $statusColor = 'text-indigo-600 bg-indigo-50';
                    } elseif ($search->is_stopped) {
                        $statusKey   = 'stopped';
                        $statusLabel = 'Stopped';
                        $statusColor = 'text-red-500 bg-red-50';
                    } elseif ($search->is_paused) {
                        $statusKey   = 'paused';
                        $statusLabel = 'Paused';
                        $statusColor = 'text-amber-500 bg-amber-50';
                    } else {
                        $statusKey   = 'running';
                        $statusLabel = 'Running';
                        $statusColor = 'text-emerald-600 bg-emerald-50';
                    }
                @endphp

                <div class="history-row bg-white rounded-2xl border border-slate-200 p-5 flex justify-between items-center hover:shadow-md hover:border-blue-200 transition-all group"
                     data-query="{{ strtolower($search->query) }}">

                    {{-- LEFT: Icon + Info --}}
                    <div class="flex items-center gap-4 min-w-0">
                        <div class="w-12 h-12 bg-slate-50 rounded-xl flex items-center justify-center text-slate-400 group-hover:bg-blue-50 group-hover:text-blue-500 transition-colors flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>

                        <div class="min-w-0">
                            <div class="font-bold text-slate-700 text-base group-hover:text-blue-600 transition-colors truncate max-w-sm">
                                {{ $search->query }}
                            </div>

                            <div class="flex items-center gap-3 mt-1.5 flex-wrap">
                                {{-- Date --}}
                                <span class="flex items-center gap-1 text-xs text-slate-400">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $search->created_at->format('M d, Y · h:i A') }}
                                </span>

                                {{-- Lead count --}}
                                <span class="text-xs font-black text-blue-600 bg-blue-50 px-2 py-0.5 rounded-lg">
                                    {{ $leadCount }} {{ Str::plural('lead', $leadCount) }}
                                </span>

                                {{-- Status badge --}}
                                <span class="flex items-center gap-1.5 text-xs font-bold px-2 py-0.5 rounded-lg {{ $statusColor }}">
                                    <span class="status-dot dot-{{ $statusKey }}"></span>
                                    {{ $statusLabel }}
                                </span>

                                {{-- Total places if available --}}
                                @if($search->total_places > 0)
                                <span class="text-xs text-slate-400">
                                    of {{ $search->total_places }} places
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT: Actions --}}
                    <div class="flex gap-2 items-center flex-shrink-0 ml-4">

                        {{-- 🔥 Resume button — AJAX, no page navigation --}}
                        @if(($search->is_stopped || $search->is_paused) && !$isCompleted)
                        <button onclick="resumeSearch({{ $search->id }}, '{{ addslashes($search->query) }}', this)"
                            class="text-xs font-bold text-emerald-600 bg-emerald-50 hover:bg-emerald-600 hover:text-white px-4 py-2 rounded-xl transition-all border border-emerald-200 flex items-center gap-1.5">
                            ▶ Resume
                        </button>
                        @endif

                        {{-- View Results --}}
                        <a href="{{ route('results.show', $search->id) }}"
                            class="text-sm font-bold text-slate-600 bg-white border border-slate-200 px-5 py-2.5 rounded-xl hover:bg-[#2563eb] hover:text-white hover:border-[#2563eb] transition-all">
                            View Results
                        </a>

                        {{-- Export --}}
                        <a href="{{ route('export.leads', $search->id) }}"
                            class="p-2.5 text-slate-400 hover:text-blue-500 hover:bg-blue-50 rounded-xl transition-all"
                            title="Export to Excel">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                        </a>

                        {{-- Delete --}}
                        <form action="{{ route('search.delete', $search->id) }}" method="POST">
                            @csrf
                            <button type="submit"
                                onclick="return confirm('Delete this search?')"
                                class="text-xs font-bold text-red-600 bg-red-50 hover:bg-red-600 hover:text-white px-4 py-2 rounded-xl transition-all border border-red-200">
                                Delete
                            </button>
                        </form>

                    </div>

                </div>
                @empty

                {{-- EMPTY STATE --}}
                <div class="bg-white rounded-3xl border border-dashed border-slate-300 p-16 text-center">
                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-5">
                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <h3 class="text-slate-700 font-bold text-lg mb-2">No history yet</h3>
                    <p class="text-slate-400 text-sm mb-6">You haven't performed any lead searches yet.</p>
                    <a href="/dashboard" class="bg-[#2563eb] text-white px-6 py-2.5 rounded-xl font-bold shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all">
                        Start First Search
                    </a>
                </div>

                @endforelse
            </div>

        </main>
    </div>
</div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

function toggleProfileMenu() {
    document.getElementById("profileMenu").classList.toggle("hidden");
}

function filterHistory() {
    const input = document.getElementById("searchInput").value.toLowerCase();
    document.querySelectorAll(".history-row").forEach(row => {
        const query = row.getAttribute("data-query") || "";
        row.style.display = query.includes(input) ? "flex" : "none";
    });
}

function confirmDelete() {
    return confirm("Delete this search and all its leads? This cannot be undone.");
}

function confirmDeleteAll() {
    return confirm("This will permanently erase your ENTIRE search history and all leads. Are you sure?");
}

// 🔥 AJAX resume — no page navigation, registers global bar, goes to results
async function resumeSearch(id, query, btn) {
    if (!confirm('Resume scraping for this search?')) return;

    btn.disabled  = true;
    btn.innerText = 'Starting...';

    try {
        const res  = await fetch("{{ route('search.resume', ['id' => 'TEMP_ID']) }}".replace('TEMP_ID', id), {
            method:  'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF
            }
        });
        const data = await res.json();

        if (data.status === 'resumed') {
            window.registerActiveSearch(id, query);
            window.location.href = '/dashboard/results/' + id;
        } else {
            alert('Failed to resume.');
            btn.disabled  = false;
            btn.innerText = '▶ Resume';
        }

    } catch (err) {
        alert('Error: ' + err.message);
        btn.disabled  = false;
        btn.innerText = '▶ Resume';
    }
}
</script>

@include('partials.global-bar')
</body>
</html>
