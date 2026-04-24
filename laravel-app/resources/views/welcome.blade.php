<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lead Generator | Search</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>

<body class="bg-[#f8fafc] text-slate-800">

<div class="flex min-h-screen">

    <div class="w-64 bg-white border-r border-slate-200 p-4 flex flex-col shadow-sm">
        <div class="flex items-center gap-3 px-2 mb-8">
            <div class="w-10 h-10 bg-[#2563eb] rounded-lg flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-blue-100">LG</div>
            <span class="font-bold text-slate-700 tracking-tight text-lg">Lead Generator</span>
        </div>

        <nav class="space-y-1">
            <a href="/" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-[#eff6ff] text-[#2563eb] font-semibold transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Home
            </a>

            <a href="/history" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition-all font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                History
            </a>
        </nav>
    </div>

    <div class="flex-1 flex flex-col">

        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-end px-8">
            <div class="flex items-center gap-4">
                @auth
                <div class="relative">
                    <button onclick="toggleProfileMenu()" class="w-9 h-9 rounded-full bg-purple-600 text-white flex items-center justify-center font-bold text-sm hover:ring-4 ring-purple-50 transition-all">
                        {{ strtoupper(substr(auth()->user()->name ?? 'AJ', 0, 2)) }}
                    </button>
                    <div id="profileMenu" class="hidden absolute right-0 mt-3 w-48 bg-white rounded-xl shadow-xl border border-slate-100 z-50 py-2">
                        <a href="/profile" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 font-medium">👤 Edit Profile</a>
                        <hr class="my-1 border-slate-100">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 font-medium">🚪 Logout</button>
                        </form>
                    </div>
                </div>
                @endauth
            </div>
        </header>

        <main class="p-8 max-w-4xl mx-auto w-full">

            <div class="mb-10 text-center">
                <h1 class="text-3xl font-bold text-slate-900 mb-2">Grow Your Business</h1>
                <p class="text-slate-500">Extract high-quality business leads in seconds using our live scraper.</p>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8 mb-6">
                <h2 class="font-bold text-slate-700 mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Start New Search
                </h2>

                <form id="searchForm" onsubmit="return false;" class="flex flex-col md:flex-row gap-3">
                    <input 
                        type="text" 
                        name="query" 
                        placeholder="e.g. Restaurants in Hyderabad"
                        class="flex-1 px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-blue-400 focus:outline-none transition-all text-slate-700"
                        required
                    >

                    <button id="submitBtn" class="bg-[#2563eb] text-white px-8 py-3.5 rounded-2xl font-bold hover:bg-blue-700 shadow-lg shadow-blue-100 transition-all flex items-center justify-center gap-2">
                        Start Scraping
                    </button>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div id="statusCard" class="bg-white rounded-2xl border border-slate-200 p-6 opacity-50 transition-all duration-500">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-slate-600">Scraping Status</h3>
                        <span id="loading" class="text-xs font-bold uppercase tracking-wider text-slate-400">Idle</span>
                    </div>
                    <div id="progressBarContainer" class="hidden w-full bg-slate-100 h-2 rounded-full mb-4 overflow-hidden">
                        <div id="progressBar" class="bg-blue-500 h-full w-0 transition-all duration-300"></div>
                    </div>
                </div>

                <div id="controls" class="bg-white rounded-2xl border border-slate-200 p-6 hidden flex-col justify-center">
                    <h3 class="font-bold text-slate-600 mb-4">Live Controls</h3>
                    <div class="flex gap-2">
                        <button onclick="pauseScraping()" class="flex-1 bg-amber-50 text-amber-600 font-bold py-2 rounded-xl hover:bg-amber-100 transition-all">Pause</button>
                        <button onclick="resumeScraping()" class="flex-1 bg-emerald-50 text-emerald-600 font-bold py-2 rounded-xl hover:bg-emerald-100 transition-all">Resume</button>
                        <button onclick="stopScraping()" class="flex-1 bg-red-50 text-red-600 font-bold py-2 rounded-xl hover:bg-red-100 transition-all">Stop</button>
                    </div>
                </div>
            </div>

            <div class="mt-8 text-center">
                <a href="/history" class="inline-flex items-center gap-2 text-sm font-semibold text-blue-600 hover:text-blue-800 transition-colors">
                    View Recent Searches
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>

        </main>
    </div>
</div>

<script>
function toggleProfileMenu() {
    document.getElementById("profileMenu").classList.toggle("hidden");
}

let currentSearchId = null;

document.getElementById("searchForm").addEventListener("submit", function(e) {
    e.preventDefault();

    let query = document.querySelector("input[name='query']").value;
    let btn = document.getElementById("submitBtn");
    let loader = document.getElementById("loading");
    let statusCard = document.getElementById("statusCard");

    btn.disabled = true;
    btn.innerHTML = `<svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Starting...`;
    
    loader.innerText = "Initializing...";
    loader.className = "text-xs font-bold uppercase tracking-wider text-blue-600 animate-pulse";
    statusCard.classList.remove("opacity-50");

    fetch("/search", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ query: query })
    })
    .then(res => res.json())
    .then(data => {
        if (!data.id) {
            loader.innerText = "Failed";
            btn.disabled = false;
            btn.innerText = "Start Scraping";
            return;
        }

        currentSearchId = data.id;
        document.getElementById("controls").classList.replace("hidden", "flex");
        document.getElementById("progressBarContainer").classList.remove("hidden");
        startTracking(data.id, loader);
    });
});

function startTracking(id, loader) {
    let prevProgress = 0;
    let stableCount = 0;
    let counter = 0;
    let maxWait = 40;

    let interval = setInterval(() => {
        fetch(`/api/progress/${id}`)
        .then(res => res.json())
        .then(data => {
            if (data.progress === undefined) return;

            loader.innerText = `Collecting: ${data.progress} Leads`;
            
            // Simple visual feedback for progress bar
            let progressWidth = Math.min((data.progress / 50) * 100, 100); 
            document.getElementById("progressBar").style.width = progressWidth + "%";

            if (data.progress === prevProgress && data.progress > 0) { stableCount++; } 
            else { stableCount = 0; }

            prevProgress = data.progress;
            counter++;

            if (stableCount >= 3 || counter >= maxWait) {
                clearInterval(interval);
                loader.innerText = "Finished! Redirecting...";
                loader.className = "text-xs font-bold uppercase tracking-wider text-emerald-600";
                setTimeout(() => { window.location.href = "/results/" + id; }, 1200);
            }
        });
    }, 1500);
}

// Control function styling feedback
function pauseScraping() {
    if (!currentSearchId) return;
    fetch(`/api/pause/${currentSearchId}`, { method: "POST", headers: { "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content }});
    document.getElementById("loading").innerText = "Paused";
}
// (Include Resume and Stop logic similar to above)
</script>
</body>
</html>