<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Search | Lead Generator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>

<body class="bg-[#f8fafc] text-slate-800">
<div class="flex min-h-screen">

    {{-- SIDEBAR --}}
    @include('partials.sidebar')
        

    {{-- MAIN CONTENT --}}
    <div class="flex-1 ml-64 p-8">

        {{-- PAGE HEADER --}}
        <div class="mb-8">
            <h1 class="text-2xl font-black text-slate-800">New Search</h1>
            <p class="text-sm text-slate-400 mt-1">Scrape leads from Google Maps by keyword and location.</p>
        </div>

        {{-- SEARCH FORM --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8 max-w-2xl">

            <div id="alertBox" class="hidden mb-6 rounded-2xl p-4 text-sm font-medium"></div>

            <div class="mb-6">
                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">
                    Search Query
                </label>
                <input
                    type="text"
                    id="queryInput"
                    placeholder="e.g. restaurants in New York, dentists in London..."
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 text-sm font-medium focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-50 transition"
                />
                <p class="text-[11px] text-slate-400 mt-2">Include the location in your query for best results. e.g. <span class="font-bold text-slate-500">"plumbers in Chicago"</span></p>
            </div>

            <button
                id="searchBtn"
                onclick="startSearch()"
                class="w-full bg-[#2563eb] text-white py-3 rounded-xl font-bold text-sm hover:bg-blue-700 transition shadow-md shadow-blue-100 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                <svg id="searchIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <svg id="spinnerIcon" class="hidden w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span id="btnText">Start Scraping</span>
            </button>

        </div>

        {{-- TIPS --}}
        <div class="mt-6 max-w-2xl grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-blue-50 rounded-2xl p-4 border border-blue-100">
                <p class="text-xs font-black text-blue-600 uppercase tracking-widest mb-1">Tip</p>
                <p class="text-xs text-slate-600">Be specific. <span class="font-semibold">"Italian restaurants in NYC"</span> works better than <span class="font-semibold">"food"</span>.</p>
            </div>
            <div class="bg-emerald-50 rounded-2xl p-4 border border-emerald-100">
                <p class="text-xs font-black text-emerald-600 uppercase tracking-widest mb-1">Results</p>
                <p class="text-xs text-slate-600">After starting, you'll be taken to the live results page automatically.</p>
            </div>
            <div class="bg-purple-50 rounded-2xl p-4 border border-purple-100">
                <p class="text-xs font-black text-purple-600 uppercase tracking-widest mb-1">Export</p>
                <p class="text-xs text-slate-600">All scraped leads can be exported to Excel from the History page.</p>
            </div>
        </div>

    </div>
</div>

<script>
async function startSearch() {
    const query   = document.getElementById('queryInput').value.trim();
    const btn     = document.getElementById('searchBtn');
    const label   = document.getElementById('btnText');
    const alert   = document.getElementById('alertBox');
    const icon    = document.getElementById('searchIcon');
    const spinner = document.getElementById('spinnerIcon');

    // Reset alert
    alert.className = 'hidden mb-6 rounded-2xl p-4 text-sm font-medium';
    alert.textContent = '';

    if (!query) {
        showAlert('Please enter a search query.', 'error');
        return;
    }

    // Loading state
    btn.disabled = true;
    label.textContent = 'Starting...';
    icon.classList.add('hidden');
    spinner.classList.remove('hidden');

    try {
        const res = await fetch('{{ route('search.start') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ query })
        });

        const data = await res.json();

        if (data.success) {
            const searchId = data.id ?? null;
            if (searchId) {
                showAlert('Search started! Redirecting to results...', 'success');
                setTimeout(() => {
                    window.location.href = '{{ url('dashboard/results') }}/' + searchId;
                }, 800);
            } else {
                showAlert('Search started! Check History for results.', 'success');
            }
        } else {
            showAlert(data.error ?? 'Something went wrong. Try again.', 'error');
            resetBtn();
        }

    } catch (e) {
        showAlert('Request failed. Check your connection and try again.', 'error');
        resetBtn();
    }
}

function resetBtn() {
    document.getElementById('searchBtn').disabled = false;
    document.getElementById('btnText').textContent = 'Start Scraping';
    document.getElementById('searchIcon').classList.remove('hidden');
    document.getElementById('spinnerIcon').classList.add('hidden');
}

function showAlert(msg, type) {
    const alert = document.getElementById('alertBox');
    alert.textContent = msg;
    if (type === 'success') {
        alert.className = 'mb-6 rounded-2xl p-4 text-sm font-medium bg-emerald-50 border border-emerald-200 text-emerald-700';
    } else {
        alert.className = 'mb-6 rounded-2xl p-4 text-sm font-medium bg-red-50 border border-red-200 text-red-600';
    }
}

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
