<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard | Lead Generator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>

<body class="bg-[#f8fafc] text-slate-800">

<div class="flex min-h-screen">

    <div class="w-64 bg-white border-r border-slate-200 p-4 flex flex-col shadow-sm fixed h-full">
        <div class="flex items-center gap-3 px-2 mb-8">
            <div class="w-10 h-10 bg-[#2563eb] rounded-lg flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-blue-100">LG</div>
            <span class="font-bold text-slate-700 tracking-tight text-lg">Admin Panel</span>
        </div>

        <nav class="space-y-1">
            <a href="/admin/dashboard" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-[#eff6ff] text-[#2563eb] font-semibold transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Insights
            </a>
            <a href="#users-section" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition-all font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                Manage Users
            </a>
            <div class="pt-4 pb-2 px-4">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Platform</p>
            </div>
            <a href="/" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition-all font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                User Site
            </a>
        </nav>
    </div>

    <div class="flex-1 ml-64 flex flex-col min-h-screen">

        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-8 sticky top-0 z-40">
            <h1 class="text-lg font-bold text-slate-800">System Overview</h1>
            
            <div class="flex items-center gap-4">
                <div class="relative">
                    <button onclick="toggleMenu(event)" id="profileBtn" class="flex items-center gap-3 bg-slate-50 hover:bg-slate-100 px-3 py-1.5 rounded-2xl transition-all border border-slate-100">
                        <div class="w-7 h-7 rounded-lg bg-[#2563eb] text-white flex items-center justify-center font-bold text-xs uppercase">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <span class="text-sm font-bold text-slate-700">{{ auth()->user()->name }}</span>
                    </button>
                    
                    <div id="profileMenu" class="hidden absolute right-0 mt-3 w-48 bg-white border border-slate-100 rounded-2xl shadow-xl z-50 py-2">
                        <a href="/profile" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 font-medium">Edit Profile</a>
                        <hr class="my-2 border-slate-50">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 font-bold tracking-tight">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="p-8 space-y-8">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Total Users</p>
                    <h2 class="text-3xl font-black text-[#2563eb]">{{ $totalUsers }}</h2>
                </div>
                <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Total Searches</p>
                    <h2 class="text-3xl font-black text-emerald-500">{{ $totalSearches }}</h2>
                </div>
                <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Total Leads</p>
                    <h2 class="text-3xl font-black text-purple-600">{{ $totalLeads }}</h2>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
                    <h2 class="font-bold text-slate-700 mb-6 flex items-center gap-2">
                        <span class="w-2 h-2 bg-purple-500 rounded-full"></span> Platform Growth (Leads)
                    </h2>
                    <div class="h-64"><canvas id="leadsChart"></canvas></div>
                </div>
                <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
                    <h2 class="font-bold text-slate-700 mb-6 flex items-center gap-2">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full"></span> Search Volume
                    </h2>
                    <div class="h-64"><canvas id="searchChart"></canvas></div>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <div class="xl:col-span-1 bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
                    <h2 class="font-bold text-slate-700 mb-4 tracking-tight">Trending Search Queries</h2>
                    <div class="space-y-2">
                        @foreach($topSearches as $s)
                        <div class="flex justify-between items-center bg-slate-50 px-4 py-3 rounded-2xl group hover:bg-blue-50 transition-all">
                            <span class="text-sm font-bold text-slate-600 group-hover:text-blue-700">{{ $s->query }}</span>
                            <span class="bg-white px-3 py-1 rounded-lg text-xs font-black text-blue-600 shadow-sm border border-slate-100">{{ $s->count }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div id="users-section" class="xl:col-span-2 bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-slate-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-slate-50/30">
                        <h2 class="font-bold text-slate-700">User Management</h2>
                        <form method="GET" class="flex gap-2 w-full md:w-auto">
                            <input type="text" name="search" placeholder="Find user..." class="bg-white border border-slate-200 px-4 py-2 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-blue-100 w-full">
                            <button class="bg-[#2563eb] text-white px-4 py-2 rounded-xl text-sm font-bold shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all">Filter</button>
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-white text-[10px] font-black text-slate-400 uppercase tracking-widest border-b">
                                <tr>
                                    <th class="px-6 py-4">Identity</th>
                                    <th class="px-6 py-4">Current Role</th>
                                    <th class="px-6 py-4 text-center">Control</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($users as $u)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <form action="/admin/update-user/{{ $u->id }}" method="POST" class="space-y-1">
                                            @csrf
                                            <input name="name" value="{{ $u->name }}" class="font-bold text-slate-700 bg-transparent border-none p-0 focus:ring-0 w-full">
                                            <input name="email" value="{{ $u->email }}" class="text-xs text-slate-400 bg-transparent border-none p-0 focus:ring-0 w-full italic">
                                            <button class="hidden"></button> </form>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-tighter {{ $u->role == 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-slate-100 text-slate-600' }}">
                                            {{ $u->role }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex gap-2 justify-center">
                                            <form action="/admin/toggle-role/{{ $u->id }}" method="POST">
                                                @csrf
                                                <button class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all" title="Toggle Role">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                                </button>
                                            </form>
                                            <form action="/admin/delete-user/{{ $u->id }}" method="POST" onsubmit="return confirm('Delete user account?')">
                                                @csrf
                                                <button class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all" title="Delete User">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>

<script>
// CHART CONFIG
const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
        y: { beginAtZero: true, grid: { borderDash: [5, 5], color: '#f1f5f9' } },
        x: { grid: { display: false } }
    }
};

new Chart(document.getElementById('leadsChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode($leadsPerDay->pluck('date')) !!},
        datasets: [{
            label: 'Leads',
            data: {!! json_encode($leadsPerDay->pluck('count')) !!},
            borderColor: '#9333ea',
            backgroundColor: 'rgba(147, 51, 234, 0.1)',
            fill: true,
            tension: 0.4
        }]
    },
    options: chartOptions
});

new Chart(document.getElementById('searchChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($searchesPerDay->pluck('date')) !!},
        datasets: [{
            label: 'Searches',
            data: {!! json_encode($searchesPerDay->pluck('count')) !!},
            backgroundColor: '#10b981',
            borderRadius: 8
        }]
    },
    options: chartOptions
});

// DROPDOWN LOGIC
function toggleMenu(event) {
    event.stopPropagation();
    document.getElementById("profileMenu").classList.toggle("hidden");
}

document.addEventListener("click", function(e) {
    const menu = document.getElementById("profileMenu");
    const button = document.getElementById("profileBtn");
    if (button && !button.contains(e.target)) {
        menu.classList.add("hidden");
    }
});
</script>

</body>
</html>