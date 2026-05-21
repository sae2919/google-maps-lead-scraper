<div class="w-64 bg-white border-r border-slate-200 p-4 flex flex-col shadow-sm fixed h-full z-30">

    {{-- LOGO --}}
    <div class="flex items-center gap-3 px-2 mb-8">
        <div class="w-10 h-10 bg-[#2563eb] rounded-lg flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-blue-100">LG</div>
        <span class="font-bold text-slate-700 tracking-tight text-lg">Lead Generator</span>
    </div>

    {{-- NAV --}}
    <nav class="space-y-1 flex-1">

        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('dashboard') ? 'bg-[#eff6ff] text-[#2563eb] font-semibold' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition-all font-medium' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Dashboard
        </a>

        <a href="{{ route('search.page') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('search.page') ? 'bg-[#eff6ff] text-[#2563eb] font-semibold' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition-all font-medium' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            Search
        </a>

        <a href="{{ route('history.index') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('history.*') ? 'bg-[#eff6ff] text-[#2563eb] font-semibold' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition-all font-medium' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            History
        </a>

        <a href="{{ route('generate.page') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('generate.page') ? 'bg-[#eff6ff] text-[#2563eb] font-semibold' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition-all font-medium' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            AI Generator
        </a>

        <a href="{{ route('profile.edit') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('profile.*') ? 'bg-[#eff6ff] text-[#2563eb] font-semibold' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition-all font-medium' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Profile
        </a>

    </nav>

    {{-- BOTTOM --}}
    <div class="mt-auto pt-5 border-t border-slate-200 space-y-4">

        

        <div class="flex items-center justify-between bg-slate-50 border border-slate-200 rounded-2xl px-3 py-3">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-full bg-gradient-to-r from-purple-500 to-indigo-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-slate-800 truncate">{{ auth()->user()->name ?? 'User' }}</p>
                    <p class="text-xs text-slate-500">Online</p>
                </div>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0 ml-2">
                <a href="{{ route('profile.edit') }}" class="text-slate-500 hover:text-[#2563eb] transition" title="Edit Profile">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-red-500 hover:text-red-600 transition" title="Logout">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>