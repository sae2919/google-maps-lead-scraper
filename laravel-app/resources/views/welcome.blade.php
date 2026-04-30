<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lead Generator | Search</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }

        @keyframes pulse-ring {
            0%   { transform: scale(1); opacity: 1; }
            100% { transform: scale(2); opacity: 0; }
        }

        .status-dot { position: relative; display: inline-block; width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
        .status-dot::after { content: ''; position: absolute; inset: 0; border-radius: 50%; animation: pulse-ring 1.2s ease-out infinite; }

        .dot-running  { background: #22c55e; }
        .dot-running::after  { background: #22c55e; }
        .dot-paused   { background: #f59e0b; }
        .dot-paused::after   { animation: none; background: #f59e0b; }
        .dot-stopped  { background: #ef4444; }
        .dot-stopped::after  { animation: none; }
        .dot-idle     { background: #94a3b8; }
        .dot-idle::after     { animation: none; }

        .ctrl-btn {
            flex: 1; padding: 10px 8px;
            border-radius: 12px; border: none;
            font-size: 13px; font-weight: 700;
            cursor: pointer;
            transition: all 0.18s ease;
            display: flex; align-items: center; justify-content: center; gap: 6px;
        }
        .ctrl-btn:hover   { transform: translateY(-1px); }
        .ctrl-btn:active  { transform: scale(0.97); }
        .ctrl-btn:disabled { opacity: 0.35; cursor: not-allowed; transform: none; }

        .btn-pause  { background: #fef3c7; color: #d97706; }
        .btn-pause:hover:not(:disabled)  { background: #d97706; color: #fff; }

        .btn-resume { background: #dcfce7; color: #16a34a; }
        .btn-resume:hover:not(:disabled) { background: #16a34a; color: #fff; }

        .btn-stop   { background: #fee2e2; color: #dc2626; }
        .btn-stop:hover:not(:disabled)   { background: #dc2626; color: #fff; }
    </style>
</head>

<body class="bg-[#f8fafc] text-slate-800">

<div class="flex min-h-screen">

    {{-- SIDEBAR --}}
    <div class="w-64 bg-white border-r border-slate-200 p-4 flex flex-col shadow-sm fixed h-full">
        <div class="flex items-center gap-3 px-2 mb-8">
            <div class="w-10 h-10 bg-[#2563eb] rounded-lg flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-blue-100">LG</div>
            <span class="font-bold text-slate-700 tracking-tight text-lg">Lead Generator</span>
        </div>

        <nav class="space-y-1">
            <a href="/" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-[#eff6ff] text-[#2563eb] font-semibold transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Home
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
    </div>

    <div class="flex-1 ml-64 flex flex-col">

        {{-- TOPBAR --}}
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-end px-8 sticky top-0 z-40">
            <div class="flex items-center gap-4">
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

        <main class="p-8 max-w-3xl mx-auto w-full">

            {{-- HEADING --}}
            <div class="mb-10 text-center">
                <h1 class="text-3xl font-extrabold text-slate-900 mb-2 tracking-tight">Grow Your Business</h1>
                <p class="text-slate-500 text-sm">Extract high-quality business leads in seconds using our live scraper.</p>
            </div>

            {{-- SEARCH FORM --}}
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8 mb-6">
                <h2 class="font-bold text-slate-700 mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Start New Search
                </h2>

                <form id="searchForm" onsubmit="startSearch(event)" class="flex flex-col md:flex-row gap-3">
                    <input
                        type="text"
                        id="queryInput"
                        name="query"
                        placeholder="e.g. Restaurants in Hyderabad"
                        class="flex-1 px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-blue-400 focus:outline-none transition-all text-slate-700 placeholder:text-slate-400"
                        required>

                    <button id="submitBtn"
                        class="bg-[#2563eb] text-white px-8 py-3.5 rounded-2xl font-bold hover:bg-blue-700 shadow-lg shadow-blue-100 transition-all flex items-center justify-center gap-2 min-w-[160px]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Start Scraping
                    </button>
                </form>
            </div>

            {{-- STATUS + CONTROLS (hidden until search starts) --}}
            <div id="livePanel" class="hidden">

                {{-- STATUS CARD --}}
                <div class="bg-white rounded-2xl border border-slate-200 p-6 mb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-slate-700">Scraping Status</h3>
                        <div class="flex items-center gap-2">
                            <span class="status-dot dot-idle" id="statusDot"></span>
                            <span id="statusLabel" class="text-xs font-black uppercase tracking-wider text-slate-400">Idle</span>
                        </div>
                    </div>

                    {{-- STATS ROW --}}
                    <div class="grid grid-cols-3 gap-4 mb-5">
                        <div class="bg-slate-50 rounded-xl p-3 text-center">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Found</p>
                            <p class="text-2xl font-black text-slate-700" id="leadsFound">0</p>
                        </div>
                        <div class="bg-blue-50 rounded-xl p-3 text-center">
                            <p class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-1">Total</p>
                            <p class="text-2xl font-black text-blue-600" id="totalPlaces">—</p>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-3 text-center">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Progress</p>
                            <p class="text-2xl font-black text-slate-700" id="progressPct">0%</p>
                        </div>
                    </div>

                    {{-- PROGRESS BAR --}}
                    <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
                        <div id="progressBar"
                            class="h-full rounded-full transition-all duration-500"
                            style="width:0%; background: linear-gradient(90deg, #3b82f6, #6366f1);">
                        </div>
                    </div>
                </div>

                {{-- CONTROLS --}}
                <div class="bg-white rounded-2xl border border-slate-200 p-6 mb-4">
                    <h3 class="font-bold text-slate-700 mb-4">Live Controls</h3>
                    <div class="flex gap-3">

                        {{-- PAUSE --}}
                        <button id="btnPause" class="ctrl-btn btn-pause" onclick="controlScraper('pause')">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 9v6m4-6v6"/>
                            </svg>
                            Pause
                        </button>

                        {{-- RESUME --}}
                        <button id="btnResume" class="ctrl-btn btn-resume hidden" onclick="controlScraper('resume')">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 3l14 9-14 9V3z"/>
                            </svg>
                            Resume
                        </button>

                        {{-- STOP --}}
                        <button id="btnStop" class="ctrl-btn btn-stop" onclick="controlScraper('stop')">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <rect x="6" y="6" width="12" height="12" rx="2" stroke-width="2.5"/>
                            </svg>
                            Stop
                        </button>

                    </div>
                </div>

                {{-- VIEW RESULTS BUTTON (shown when stopped/done) --}}
                <div id="viewResultsWrap" class="hidden">
                    <a id="viewResultsBtn" href="#"
                        class="w-full flex items-center justify-center gap-2 bg-emerald-500 text-white py-4 rounded-2xl font-bold text-base hover:bg-emerald-600 transition-all shadow-lg shadow-emerald-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        View All Results
                    </a>
                </div>

            </div>

            {{-- FOOTER LINK --}}
            <div class="mt-8 text-center">
                <a href="/history" class="inline-flex items-center gap-2 text-sm font-semibold text-blue-600 hover:text-blue-800 transition-colors">
                    View Recent Searches
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>

        </main>
    </div>
</div>

<script>
const csrf = document.querySelector('meta[name="csrf-token"]').content;

let currentSearchId = null;
let pollingInterval = null;
let currentStatus   = 'idle';

// ── PROFILE MENU ──────────────────────────────────────────────────────────────
function toggleProfileMenu() {
    document.getElementById("profileMenu").classList.toggle("hidden");
}

// ── START SEARCH ──────────────────────────────────────────────────────────────
function startSearch(e) {
    e.preventDefault();

    const query  = document.getElementById("queryInput").value.trim();
    const btn    = document.getElementById("submitBtn");

    if (!query) return;

    // Disable button + show spinner
    btn.disabled = true;
    btn.innerHTML = `
        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
        </svg>
        Starting...`;

    fetch("/search", {
        method:  "POST",
        headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": csrf },
        body:    JSON.stringify({ query }),
    })
    .then(res => res.json())
    .then(data => {
        if (!data.id) {
            alert("Search failed. Please try again.");
            resetSubmitBtn();
            return;
        }

        currentSearchId = data.id;

        // Show live panel
        document.getElementById("livePanel").classList.remove("hidden");

        // Update view results link
        document.getElementById("viewResultsBtn").href = "/results/" + data.id;

        // Set total if provided
        if (data.total) {
            document.getElementById("totalPlaces").innerText = data.total;
        }

        updateStatusUI('RUNNING');
        startPolling(data.id);
    })
    .catch(err => {
        console.error(err);
        alert("Connection error. Is the server running?");
        resetSubmitBtn();
    });
}

// ── POLLING ───────────────────────────────────────────────────────────────────
function startPolling(id) {
    if (pollingInterval) clearInterval(pollingInterval);

    pollingInterval = setInterval(() => {
        fetch(`/api/progress/${id}`)
            .then(res => res.json())
            .then(data => {
                const progress = data.progress || 0;
                const total    = data.total    || 0;
                const status   = data.status   || 'RUNNING';

                // Update counts
                document.getElementById("leadsFound").innerText = progress;

                if (total > 0) {
                    document.getElementById("totalPlaces").innerText = total;
                    const pct = Math.min(Math.round((progress / total) * 100), 100);
                    document.getElementById("progressPct").innerText  = pct + "%";
                    document.getElementById("progressBar").style.width = pct + "%";
                }

                updateStatusUI(status);

                // Auto-redirect when finished
                if (status === 'STOPPED' && progress > 0) {
                    clearInterval(pollingInterval);
                    document.getElementById("viewResultsWrap").classList.remove("hidden");
                }
            })
            .catch(err => console.error("Poll error:", err));
    }, 2000);
}

// ── SCRAPER CONTROLS ─────────────────────────────────────────────────────────
function controlScraper(action) {
    if (!currentSearchId) return;

    const btn = document.getElementById('btn' + action.charAt(0).toUpperCase() + action.slice(1));
    if (btn) btn.disabled = true;

    fetch(`/api/${action}/${currentSearchId}`, {
        method:  "POST",
        headers: { "X-CSRF-TOKEN": csrf },
    })
    .then(res => res.json())
    .then(data => {
        console.log(`${action}:`, data);

        // Force an immediate poll to refresh UI
        fetch(`/api/progress/${currentSearchId}`)
            .then(r => r.json())
            .then(d => updateStatusUI(d.status || 'RUNNING'));
    })
    .catch(err => console.error(`${action} error:`, err))
    .finally(() => { if (btn) btn.disabled = false; });
}

// ── UPDATE STATUS UI ─────────────────────────────────────────────────────────
function updateStatusUI(status) {
    if (status === currentStatus) return;
    currentStatus = status;

    const dot      = document.getElementById("statusDot");
    const label    = document.getElementById("statusLabel");
    const btnPause  = document.getElementById("btnPause");
    const btnResume = document.getElementById("btnResume");
    const btnStop   = document.getElementById("btnStop");
    const bar       = document.getElementById("progressBar");

    // Reset dot classes
    dot.classList.remove('dot-running', 'dot-paused', 'dot-stopped', 'dot-idle');

    if (status === 'RUNNING') {
        dot.classList.add('dot-running');
        label.innerText    = 'Running';
        label.style.color  = '#16a34a';

        btnPause.classList.remove('hidden');
        btnResume.classList.add('hidden');
        btnStop.disabled   = false;
        bar.style.background = 'linear-gradient(90deg, #3b82f6, #6366f1)';

    } else if (status === 'PAUSED') {
        dot.classList.add('dot-paused');
        label.innerText    = 'Paused';
        label.style.color  = '#d97706';

        btnPause.classList.add('hidden');
        btnResume.classList.remove('hidden');
        btnStop.disabled   = false;
        bar.style.background = 'linear-gradient(90deg, #f59e0b, #d97706)';

    } else if (status === 'STOPPED') {
        dot.classList.add('dot-stopped');
        label.innerText    = 'Stopped';
        label.style.color  = '#dc2626';

        btnPause.classList.add('hidden');
        btnPause.disabled  = true;
        btnStop.classList.add('hidden');
        btnStop.disabled   = true;
        btnResume.classList.remove('hidden');
        btnResume.disabled = false;

        bar.style.background = 'linear-gradient(90deg, #ef4444, #dc2626)';
        document.getElementById("viewResultsWrap").classList.remove("hidden");

        if (pollingInterval) clearInterval(pollingInterval);
    }
}

// ── RESET SUBMIT BUTTON ───────────────────────────────────────────────────────
function resetSubmitBtn() {
    const btn = document.getElementById("submitBtn");
    btn.disabled = false;
    btn.innerHTML = `
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        Start Scraping`;
}
</script>
</body>
</html>