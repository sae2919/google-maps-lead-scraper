<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard | Lead Generator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }

        .stat-card { transition: all 0.2s ease; }
        .stat-card:hover { transform: translateY(-2px); }

        @keyframes count-up {
            from { opacity: 0; transform: translateY(6px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .count-anim { animation: count-up 0.5s ease forwards; }

        .table-row { transition: background 0.15s ease; }
        .table-row:hover { background: #f8fafc; }

        /* Role badge */
        .badge-admin { background: #ede9fe; color: #7c3aed; }
        .badge-user  { background: #f1f5f9; color: #475569; }
    </style>
</head>

<body class="bg-[#f8fafc] text-slate-800">

<div class="flex min-h-screen">

    {{-- ── SIDEBAR ── --}}
    <div class="w-64 bg-white border-r border-slate-200 p-4 flex flex-col shadow-sm fixed h-full z-30">
        <div class="flex items-center gap-3 px-2 mb-8">
            <div class="w-10 h-10 bg-[#2563eb] rounded-lg flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-blue-100">LG</div>
            <div>
                <span class="font-bold text-slate-700 tracking-tight text-base block">Admin Panel</span>
                <span class="text-[10px] text-slate-400 font-medium uppercase tracking-widest">Control Center</span>
            </div>
        </div>

        <nav class="space-y-1 flex-1">
            <a href="/admin/dashboard"
                class="flex items-center gap-3 px-4 py-3 rounded-xl bg-[#eff6ff] text-[#2563eb] font-semibold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Overview
            </a>

            <a href="#users-section"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition-all font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Manage Users
            </a>

            

            <div class="pt-4 pb-1 px-4">
                <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.2em]">Platform</p>
            </div>

            


<a href="{{ route('search.page') }}"
    class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition-all font-medium">

    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
    </svg>

    Start Search
</a>
        </nav>

        {{-- Sidebar footer --}}
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

    {{-- ── MAIN ── --}}
    <div class="flex-1 ml-64 flex flex-col min-h-screen">

        {{-- TOPBAR --}}
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-8 sticky top-0 z-40">
            <div>
                <h1 class="text-lg font-extrabold text-slate-800 tracking-tight">System Overview</h1>
                <p class="text-[11px] text-slate-400">{{ now()->format('l, d M Y') }}</p>
            </div>

            <div class="flex items-center gap-3">
                {{-- Live indicator --}}
                <div class="flex items-center gap-2 bg-emerald-50 text-emerald-600 px-3 py-1.5 rounded-xl text-xs font-bold border border-emerald-100">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                    System Live
                </div>

                <div class="w-px h-6 bg-slate-200"></div>

                <div class="relative">
                    <button onclick="toggleMenu(event)" id="profileBtn"
                        class="flex items-center gap-2 bg-slate-50 hover:bg-slate-100 px-3 py-1.5 rounded-2xl transition-all border border-slate-100">
                        <div class="w-7 h-7 rounded-lg bg-[#2563eb] text-white flex items-center justify-center font-bold text-xs uppercase">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <span class="text-sm font-bold text-slate-700">{{ auth()->user()->name }}</span>
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div id="profileMenu" class="hidden absolute right-0 mt-3 w-48 bg-white border border-slate-100 rounded-2xl shadow-xl z-50 py-2">
                        <div class="px-4 py-2 text-[10px] font-black text-slate-400 uppercase tracking-widest">Admin Account</div>
                        <a href="/profile" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 font-medium">👤 Edit Profile</a>
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

            {{-- ── STAT CARDS ── --}}
            @php
                $websiteCount = \App\Models\Lead::whereNotNull('ai_metadata')
                    ->where('website', 'like', '%/sites/%')->count();
                $activeSearches = \App\Models\Search::where('is_stopped', false)
                    ->where('is_paused', false)->count();
                $pausedSearches = \App\Models\Search::where('is_paused', true)->count();
            @endphp

            <div class="grid grid-cols-2 xl:grid-cols-4 gap-5">

                <div class="stat-card bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:border-blue-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-11 h-11 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <span class="text-[9px] font-black text-blue-400 bg-blue-50 px-2 py-1 rounded-lg uppercase tracking-widest">Users</span>
                    </div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.15em] mb-0.5">Total Users</p>
                    <h2 class="text-3xl font-black text-[#2563eb] count-anim">{{ $totalUsers }}</h2>
                </div>

                <div class="stat-card bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:border-emerald-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-11 h-11 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <span class="text-[9px] font-black text-emerald-500 bg-emerald-50 px-2 py-1 rounded-lg uppercase tracking-widest">All Time</span>
                    </div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.15em] mb-0.5">Total Searches</p>
                    <h2 class="text-3xl font-black text-emerald-500 count-anim">{{ $totalSearches }}</h2>
                </div>

                <div class="stat-card bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:border-purple-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-11 h-11 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <span class="text-[9px] font-black text-purple-500 bg-purple-50 px-2 py-1 rounded-lg uppercase tracking-widest">Scraped</span>
                    </div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.15em] mb-0.5">Total Leads</p>
                    <h2 class="text-3xl font-black text-purple-600 count-anim">{{ number_format($totalLeads) }}</h2>
                </div>

                <div class="stat-card bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:border-rose-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-11 h-11 bg-rose-50 text-rose-500 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                        </div>
                        <span class="text-[9px] font-black text-rose-400 bg-rose-50 px-2 py-1 rounded-lg uppercase tracking-widest">AI</span>
                    </div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.15em] mb-0.5">Websites Built</p>
                    <h2 class="text-3xl font-black text-rose-500 count-anim">{{ $websiteCount }}</h2>
                </div>

            </div>

            {{-- SECONDARY STATS --}}
            <div class="grid grid-cols-3 gap-6">
                <div class="bg-white rounded-xl border border-slate-200 px-5 py-4 flex items-center gap-4 shadow-sm">
                    <div class="w-9 h-9 bg-amber-50 text-amber-500 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Active Searches</p>
                        <p class="text-xl font-black text-amber-500">{{ $activeSearches }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 px-5 py-4 flex items-center gap-4 shadow-sm">
                    <div class="w-9 h-9 bg-orange-50 text-orange-400 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Paused</p>
                        <p class="text-xl font-black text-orange-400">{{ $pausedSearches }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 px-5 py-4 flex items-center gap-4 shadow-sm">
                    <div class="w-9 h-9 bg-blue-50 text-blue-500 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Avg Leads / Search</p>
                        <p class="text-xl font-black text-blue-500">
                            {{ $totalSearches > 0 ? round($totalLeads / $totalSearches, 1) : 0 }}
                        </p>
                    </div>
                </div>

                
            </div>

            {{-- ── CHARTS ── --}}
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="font-extrabold text-slate-800 text-sm">Platform Growth — Leads</h2>
                            <p class="text-[11px] text-slate-400 mt-0.5">Total leads collected per day across all users</p>
                        </div>
                        <span class="w-2 h-2 bg-purple-500 rounded-full"></span>
                    </div>
                    <div class="h-60"><canvas id="leadsChart"></canvas></div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="font-extrabold text-slate-800 text-sm">Search Volume</h2>
                            <p class="text-[11px] text-slate-400 mt-0.5">Searches performed per day across all users</p>
                        </div>
                        <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
                    </div>
                    <div class="h-60"><canvas id="searchChart"></canvas></div>
                </div>
            </div>

            {{-- ── TRENDS + USER TABLE ── --}}
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6" id="trends-section">

                {{-- TRENDING SEARCHES --}}
                <div class="xl:col-span-1 bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                    <h2 class="font-extrabold text-slate-800 text-sm mb-1">Top Search Queries</h2>
                    <p class="text-[11px] text-slate-400 mb-5">Most searched terms on the platform</p>

                    <div class="space-y-2">
                        @foreach($topSearches as $i => $s)
                        <div class="flex items-center gap-3 bg-slate-50 px-4 py-3 rounded-xl hover:bg-blue-50 transition-all group">
                            <span class="text-[10px] font-black text-slate-300 w-4">{{ $i + 1 }}</span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-slate-600 group-hover:text-blue-700 truncate">{{ $s->query }}</p>
                            </div>
                            <span class="text-[10px] font-black text-blue-600 bg-white px-2.5 py-1 rounded-lg shadow-sm border border-slate-100 flex-shrink-0">
                                {{ $s->count }}×
                            </span>
                        </div>
                        @endforeach

                        @if($topSearches->isEmpty())
                        <p class="text-slate-400 text-sm italic text-center py-4">No searches yet.</p>
                        @endif
                    </div>
                </div>

                {{-- USER MANAGEMENT TABLE --}}
                <div id="users-section" class="xl:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 bg-slate-50/30">
                        <div>
                            <h2 class="font-extrabold text-slate-800 text-sm">User Management</h2>
                            <p class="text-[11px] text-slate-400">{{ $users->count() }} registered {{ Str::plural('user', $users->count()) }}</p>
                        </div>
                        <form method="GET" class="flex gap-2 w-full sm:w-auto">
                            <input type="text" name="search"
                                value="{{ request('search') }}"
                                placeholder="Search users..."
                                class="bg-white border border-slate-200 px-4 py-2 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-blue-100 w-full text-slate-600">
                            <button class="bg-[#2563eb] text-white px-4 py-2 rounded-xl text-sm font-bold shadow-md shadow-blue-100 hover:bg-blue-700 transition-all flex-shrink-0">
                                Filter
                            </button>
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50/50 border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-[0.15em]">
                                <tr>
                                    <th class="px-6 py-4">User</th>
                                    <th class="px-6 py-4 text-center">Role</th>
                                    <th class="px-6 py-4 text-center">Searches</th>
                                    <th class="px-6 py-4 text-center">Leads</th>
                                    <th class="px-6 py-4 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($users as $u)
                                @php
                                    $userSearchCount = \App\Models\Search::where('user_id', $u->id)->count();
                                    $userLeadCount   = \App\Models\Lead::whereIn('search_id',
                                        \App\Models\Search::where('user_id', $u->id)->pluck('id')
                                    )->count();
                                @endphp
                                <tr class="table-row">
                                    <td class="px-6 py-4">
                                        <form action="/admin/update-user/{{ $u->id }}" method="POST"
                                            class="flex flex-col gap-0.5" id="userForm{{ $u->id }}">
                                            @csrf
                                            <input name="name"
                                                value="{{ $u->name }}"
                                                class="font-bold text-slate-700 bg-transparent border-b border-transparent hover:border-slate-200 focus:border-blue-400 focus:outline-none px-0 text-sm transition-all w-full"
                                                onchange="document.getElementById('saveBtn{{ $u->id }}').classList.remove('hidden')">
                                            <input name="email"
                                                value="{{ $u->email }}"
                                                class="text-xs text-slate-400 bg-transparent border-b border-transparent hover:border-slate-200 focus:border-blue-400 focus:outline-none px-0 italic transition-all w-full"
                                                onchange="document.getElementById('saveBtn{{ $u->id }}').classList.remove('hidden')">
                                            <button type="submit" id="saveBtn{{ $u->id }}"
                                                class="hidden mt-1 text-[10px] font-black text-blue-600 bg-blue-50 px-2 py-0.5 rounded-lg w-fit">
                                                Save
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-tight {{ $u->role === 'admin' ? 'badge-admin' : 'badge-user' }}">
                                            {{ $u->role }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="text-sm font-black text-slate-600">{{ $userSearchCount }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="text-sm font-black text-slate-600">{{ number_format($userLeadCount) }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex gap-1 justify-center">

                                            {{-- Toggle Role --}}
                                            <form action="/admin/toggle-role/{{ $u->id }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all"
                                                    title="{{ $u->role === 'admin' ? 'Demote to User' : 'Promote to Admin' }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                                    </svg>
                                                </button>
                                            </form>

                                            {{-- Delete --}}
                                            @if($u->id !== auth()->id())
                                            <form action="/admin/delete-user/{{ $u->id }}" method="POST"
                                                onsubmit="return confirm('Delete user {{ $u->name }}? This cannot be undone.')">
                                                @csrf
                                                <button type="submit"
                                                    class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all"
                                                    title="Delete User">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                            @else
                                            <div class="w-8 h-8 flex items-center justify-center" title="Cannot delete yourself">
                                                <svg class="w-4 h-4 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                </svg>
                                            </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach

                                @if($users->isEmpty())
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-slate-400 italic text-sm">
                                        No users found.
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination if needed --}}
                    @if(method_exists($users, 'links'))
                    <div class="px-6 py-4 border-t border-slate-100">
                        {{ $users->links() }}
                    </div>
                    @endif
                </div>
            </div>

        </main>
    </div>
</div>

<script>
// ── CHARTS ───────────────────────────────────────────────────────────────────
const sharedOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
        y: {
            beginAtZero: true,
            grid: { color: '#f1f5f9', borderDash: [4, 4] },
            ticks: { font: { size: 11, family: 'Inter' } }
        },
        x: {
            grid: { display: false },
            ticks: { font: { size: 11, family: 'Inter' } }
        }
    }
};

new Chart(document.getElementById('leadsChart'), {
    type: 'line',
    data: {
        labels:   {!! json_encode($leadsPerDay->pluck('date')) !!},
        datasets: [{
            data:            {!! json_encode($leadsPerDay->pluck('count')) !!},
            borderColor:     '#9333ea',
            backgroundColor: 'rgba(147, 51, 234, 0.08)',
            fill:            true,
            tension:         0.45,
            borderWidth:     2.5,
            pointRadius:     4,
            pointBackgroundColor: '#9333ea',
            pointBorderColor:    '#fff',
            pointBorderWidth:    2,
        }]
    },
    options: sharedOptions
});

new Chart(document.getElementById('searchChart'), {
    type: 'bar',
    data: {
        labels:   {!! json_encode($searchesPerDay->pluck('date')) !!},
        datasets: [{
            data:            {!! json_encode($searchesPerDay->pluck('count')) !!},
            backgroundColor: 'rgba(16, 185, 129, 0.85)',
            borderRadius:    8,
            barThickness:    18,
        }]
    },
    options: sharedOptions
});

// ── PROFILE MENU ─────────────────────────────────────────────────────────────
function toggleMenu(event) {
    event.stopPropagation();
    document.getElementById("profileMenu").classList.toggle("hidden");
}

document.addEventListener("click", function(e) {
    const btn = document.getElementById("profileBtn");
    if (btn && !btn.contains(e.target)) {
        document.getElementById("profileMenu").classList.add("hidden");
    }
});
</script>

</body>
</html>