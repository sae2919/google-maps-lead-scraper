<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Search History | Lead Generator</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
            <a href="/" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition-all font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Home
            </a>

            <a href="/history" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-[#eff6ff] text-[#2563eb] font-semibold transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                History
            </a>
        </nav>
    </div>

    <div class="flex-1 flex flex-col">

        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-8">
            <div>
                 <h1 class="text-lg font-bold text-slate-800">Search History</h1>
            </div>
            
            <div class="flex items-center gap-4">
                <form action="/delete-all" method="POST" onsubmit="return confirmDeleteAll()">
                    @csrf
                    <button class="text-xs font-bold text-red-500 hover:text-red-700 uppercase tracking-wider px-3 py-1 hover:bg-red-50 rounded-lg transition-all">
                        Clear All History
                    </button>
                </form>
                <div class="w-px h-6 bg-slate-200"></div>
                @auth
                <div class="w-9 h-9 rounded-full bg-purple-600 text-white flex items-center justify-center font-bold text-sm">
                    {{ strtoupper(substr(auth()->user()->name ?? 'AJ', 0, 2)) }}
                </div>
                @endauth
            </div>
        </header>

        <main class="p-8 max-w-5xl mx-auto w-full">

            <div class="mb-8">
                <p class="text-slate-500">Manage and revisit your previous lead extraction tasks.</p>
            </div>

            <div class="relative mb-6">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input 
                    type="text" 
                    id="searchInput"
                    placeholder="Search past queries..."
                    class="w-full pl-12 pr-4 py-3.5 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-blue-400 focus:outline-none transition-all shadow-sm"
                    onkeyup="filterHistory()"
                >
            </div>

            <div id="historyTable" class="space-y-3">

                @forelse($searches as $search)
                <div class="bg-white rounded-2xl border border-slate-200 p-5 flex justify-between items-center hover:shadow-md hover:border-blue-200 transition-all group">

                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-slate-50 rounded-xl flex items-center justify-center text-slate-400 group-hover:bg-blue-50 group-hover:text-blue-500 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div>
                            <div class="font-bold text-slate-700 text-lg group-hover:text-blue-600 transition-colors">
                                {{ $search->query }}
                            </div>
                            <div class="flex items-center gap-2 text-sm text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                {{ $search->created_at->format('M d, Y • h:i A') }}
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-2 items-center">
                        <a href="/results/{{ $search->id }}" 
                           class="bg-white border border-slate-200 text-slate-600 font-bold px-5 py-2.5 rounded-xl hover:bg-[#2563eb] hover:text-white hover:border-[#2563eb] transition-all">
                            View Results
                        </a>

                        <form action="/delete/{{ $search->id }}" method="POST" onsubmit="return confirmDelete()">
                            @csrf
                            <button class="p-2.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-xl transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>

                </div>
                @empty
                <div class="bg-white rounded-3xl border border-dashed border-slate-300 p-12 text-center">
                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <h3 class="text-slate-700 font-bold text-lg">No history found</h3>
                    <p class="text-slate-400 mb-6">You haven't performed any lead searches yet.</p>
                    <a href="/" class="bg-[#2563eb] text-white px-6 py-2.5 rounded-xl font-bold shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all">Start First Search</a>
                </div>
                @endforelse

            </div>

        </main>
    </div>
</div>

<script>
function filterHistory() {
    let input = document.getElementById("searchInput").value.toLowerCase();
    let rows = document.querySelectorAll("#historyTable > div:not(.bg-white.rounded-3xl)"); // Don't filter the empty state

    rows.forEach(row => {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(input) ? "flex" : "none";
    });
}

function confirmDelete() { return confirm("Are you sure you want to delete this record?"); }
function confirmDeleteAll() { return confirm("This will permanently erase your entire search history. Continue?"); }
</script>

</body>
</html>