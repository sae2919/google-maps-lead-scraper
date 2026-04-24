<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard | Lead Generator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            <a href="/dashboard" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-[#eff6ff] text-[#2563eb] font-semibold transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                Dashboard
            </a>
            <a href="/history" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition-all font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                History
            </a>
        </nav>
    </div>

    <div class="flex-1 flex flex-col">

        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-8">
            <h1 class="text-lg font-bold text-slate-800">Dashboard Overview</h1>
            
            <div class="flex items-center gap-4">
                <a href="{{ route('search.page') }}" class="bg-[#2563eb] text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-blue-700 transition shadow-sm">
                    + New Search
                </a>
                <div class="w-px h-6 bg-slate-200 mx-2"></div>
                <div class="relative">
                    <button onclick="toggleMenu()" class="flex items-center gap-2 group">
                        <div class="w-9 h-9 rounded-full bg-purple-600 text-white flex items-center justify-center font-bold text-sm group-hover:ring-4 ring-purple-50 transition-all">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    </button>
                    <div id="profileMenu" class="hidden absolute right-0 mt-3 w-44 bg-white border border-slate-100 rounded-xl shadow-xl z-50 py-2">
                        <div class="px-4 py-2 text-xs font-bold text-slate-400 uppercase tracking-widest">Account</div>
                        <a href="/profile" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">Profile Settings</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="p-8 space-y-8">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:border-blue-200 transition-all">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Searches</p>
                            <h2 class="text-2xl font-black text-slate-800">{{ $totalSearches }}</h2>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:border-emerald-200 transition-all">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Leads</p>
                            <h2 class="text-2xl font-black text-slate-800">{{ $totalLeads }}</h2>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="flex-1 truncate">
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Recent Activity</p>
                            <h2 class="text-sm font-bold text-slate-700 truncate mt-1">{{ $lastSearch->query ?? 'No activity yet' }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                    <h2 class="text-sm font-bold text-slate-700 mb-6 flex items-center gap-2">
                        <span class="w-2 h-2 bg-blue-500 rounded-full"></span> Leads Growth
                    </h2>
                    <div class="h-64">
                        <canvas id="leadsChart"></canvas>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                    <h2 class="text-sm font-bold text-slate-700 mb-6 flex items-center gap-2">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full"></span> Usage Frequency
                    </h2>
                    <div class="h-64">
                        <canvas id="searchChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h2 class="font-bold text-slate-700">Recent Search History</h2>
                    <a href="/history" class="text-xs font-bold text-blue-600 hover:underline">View All</a>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($recentSearches as $search)
                    <div class="px-6 py-4 flex justify-between items-center hover:bg-slate-50 transition-colors group">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center text-slate-400 group-hover:bg-blue-50 group-hover:text-blue-500 transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            </div>
                            <div>
                                <div class="font-bold text-slate-700">{{ $search->query }}</div>
                                <div class="text-xs text-slate-400 font-medium">{{ $search->created_at->format('d M Y, h:i A') }}</div>
                            </div>
                        </div>
                        <a href="/results/{{ $search->id }}" class="bg-white border border-slate-200 text-slate-600 font-bold px-4 py-1.5 rounded-xl text-xs hover:bg-[#2563eb] hover:text-white hover:border-[#2563eb] transition-all">
                            View Data
                        </a>
                    </div>
                    @empty
                    <div class="p-12 text-center text-slate-400 italic">No search data available.</div>
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
        labels: {!! json_encode($leadsPerDay->pluck('date')) !!},
        datasets: [{
            label: 'Leads',
            data: {!! json_encode($leadsPerDay->pluck('count')) !!},
            borderColor: '#2563eb',
            backgroundColor: 'rgba(37, 99, 235, 0.1)',
            fill: true,
            tension: 0.4,
            borderWidth: 3,
            pointRadius: 4,
            pointBackgroundColor: '#2563eb'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
            x: { grid: { display: false } }
        }
    }
});

const searchCtx = document.getElementById('searchChart').getContext('2d');
new Chart(searchCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($searchesPerDay->pluck('date')) !!},
        datasets: [{
            label: 'Searches',
            data: {!! json_encode($searchesPerDay->pluck('count')) !!},
            backgroundColor: '#10b981',
            borderRadius: 8,
            barThickness: 20
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
            x: { grid: { display: false } }
        }
    }
});

function toggleMenu() {
    document.getElementById("profileMenu").classList.toggle("hidden");
}
window.onclick = function(e) {
    if (!e.target.closest('.relative')) {
        document.getElementById("profileMenu").classList.add("hidden");
    }
}
</script>

</body>
</html>