<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            @keyframes globalPulse {
                0%   { box-shadow: 0 0 0 0 rgba(34,197,94,0.6); }
                70%  { box-shadow: 0 0 0 8px rgba(34,197,94,0); }
                100% { box-shadow: 0 0 0 0 rgba(34,197,94,0); }
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        {{-- ═══════════════════════════════════════════════════ --}}
        {{-- GLOBAL SCRAPING STATUS BAR — persists across pages --}}
        {{-- ═══════════════════════════════════════════════════ --}}
        <div id="globalScrapingBar"
             class="hidden fixed bottom-0 left-0 right-0 z-[9999] bg-white border-t-2 border-blue-100 shadow-2xl">
            <div class="px-6 py-3 flex items-center justify-between gap-4 flex-wrap">

                {{-- LEFT: status indicator --}}
                <div class="flex items-center gap-3">
                    <span id="globalDot" style="
                        display:inline-block;
                        width:10px; height:10px;
                        border-radius:50%;
                        background:#22c55e;
                        box-shadow:0 0 0 0 #22c55e;
                        animation:globalPulse 1.4s infinite;
                    "></span>
                    <span class="text-sm font-black text-slate-700 uppercase tracking-wide">
                        Scraping Running
                    </span>
                    <span id="globalQuery"
                          class="text-xs text-slate-400 font-medium italic max-w-xs truncate">
                    </span>
                </div>

                {{-- CENTER: live stats --}}
                <div class="flex items-center gap-6 text-sm">
                    <div class="flex flex-col items-center">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Found</span>
                        <span class="font-black text-blue-600 text-lg leading-tight" id="globalFound">0</span>
                    </div>
                    <div class="text-slate-200 text-xl">/</div>
                    <div class="flex flex-col items-center">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Total</span>
                        <span class="font-black text-slate-700 text-lg leading-tight" id="globalTotal">—</span>
                    </div>
                    <div class="flex flex-col items-center min-w-[60px]">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Progress</span>
                        <span class="font-black text-slate-700 text-lg leading-tight" id="globalPct">0%</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Status</span>
                        <span class="font-black text-xs leading-tight" id="globalStatus"
                              style="color:#16a34a;">RUNNING</span>
                    </div>
                </div>

                {{-- RIGHT: controls --}}
                <div class="flex items-center gap-2">
                    <a id="globalViewBtn" href="#"
                       class="text-xs font-black text-blue-600 bg-blue-50 border border-blue-100
                              px-3 py-2 rounded-xl hover:bg-blue-600 hover:text-white transition-all">
                        View Results →
                    </a>
                    <button id="globalPauseBtn" onclick="globalControl('pause')"
                        class="text-xs font-black text-amber-600 bg-amber-50 border border-amber-100
                               px-3 py-2 rounded-xl hover:bg-amber-500 hover:text-white transition-all">
                        ⏸ Pause
                    </button>
                    <button onclick="globalControl('stop')"
                        class="text-xs font-black text-red-600 bg-red-50 border border-red-100
                               px-3 py-2 rounded-xl hover:bg-red-600 hover:text-white transition-all">
                        ⏹ Stop
                    </button>
                    <button onclick="dismissGlobalBar()"
                        class="text-xs font-black text-slate-400 bg-slate-50 border border-slate-100
                               px-3 py-2 rounded-xl hover:bg-slate-200 transition-all">
                        ✕
                    </button>
                </div>
            </div>

            {{-- PROGRESS BAR STRIP --}}
            <div class="h-1 bg-slate-100">
                <div id="globalProgressBar"
                     class="h-1 bg-blue-500 transition-all duration-500"
                     style="width:0%">
                </div>
            </div>
        </div>

        <script>
        (function () {
            const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';
            let globalInterval = null;
            let activeSearchId = null;

            function bootGlobalBar() {
                const stored = localStorage.getItem('activeSearch');
                if (!stored) return;

                try {
                    const parsed   = JSON.parse(stored);
                    activeSearchId = parsed.id;

                    const queryEl = document.getElementById('globalQuery');
                    if (queryEl && parsed.query) queryEl.innerText = '"' + parsed.query + '"';

                    document.getElementById('globalViewBtn').href = '/results/' + activeSearchId;
                    document.getElementById('globalScrapingBar').classList.remove('hidden');

                    startGlobalPoll();
                } catch (e) {
                    localStorage.removeItem('activeSearch');
                }
            }

            function startGlobalPoll() {
                if (globalInterval) clearInterval(globalInterval);
                globalInterval = setInterval(tickGlobal, 2000);
                tickGlobal();
            }

            function tickGlobal() {
                if (!activeSearchId) return;

                fetch('/api/progress/' + activeSearchId)
                    .then(r => r.json())
                    .then(function(data) {
                        const found  = data.found    || 0;
                        const total  = data.total    || 0;
                        const pct    = data.progress || 0;
                        const status = data.status   || 'RUNNING';

                        document.getElementById('globalFound').innerText       = found;
                        document.getElementById('globalTotal').innerText       = total > 0 ? total : '—';
                        document.getElementById('globalPct').innerText         = pct + '%';
                        document.getElementById('globalProgressBar').style.width = pct + '%';

                        const statusEl = document.getElementById('globalStatus');
                        const dotEl    = document.getElementById('globalDot');
                        const pauseBtn = document.getElementById('globalPauseBtn');

                        if (status === 'RUNNING') {
                            statusEl.innerText     = 'RUNNING';
                            statusEl.style.color   = '#16a34a';
                            dotEl.style.background = '#22c55e';
                            dotEl.style.animation  = 'globalPulse 1.4s infinite';
                            pauseBtn.innerText     = '⏸ Pause';
                            pauseBtn.onclick       = () => globalControl('pause');

                        } else if (status === 'PAUSED') {
                            statusEl.innerText     = 'PAUSED';
                            statusEl.style.color   = '#d97706';
                            dotEl.style.background = '#f59e0b';
                            dotEl.style.animation  = 'none';
                            pauseBtn.innerText     = '▶ Resume';
                            pauseBtn.onclick       = () => globalControl('resume');

                        } else if (status === 'STOPPED') {
                            statusEl.innerText     = 'STOPPED';
                            statusEl.style.color   = '#dc2626';
                            dotEl.style.background = '#ef4444';
                            dotEl.style.animation  = 'none';
                            endGlobalBar(3000);

                        } else if (status === 'COMPLETED') {
                            statusEl.innerText     = '✅ COMPLETED';
                            statusEl.style.color   = '#6366f1';
                            dotEl.style.background = '#6366f1';
                            dotEl.style.animation  = 'none';
                            document.getElementById('globalProgressBar').style.width = '100%';
                            endGlobalBar(4000);
                        }
                    })
                    .catch(function() {});
            }

            function endGlobalBar(delay) {
                clearInterval(globalInterval);
                globalInterval = null;
                localStorage.removeItem('activeSearch');
                setTimeout(function() {
                    document.getElementById('globalScrapingBar').classList.add('hidden');
                }, delay);
            }

            window.dismissGlobalBar = function() {
                document.getElementById('globalScrapingBar').classList.add('hidden');
            };

            window.globalControl = function(action) {
                if (!activeSearchId) return;
                fetch('/api/' + action + '/' + activeSearchId, {
                    method:  'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF
                    }
                })
                .then(() => tickGlobal())
                .catch(() => {});
            };

            window.registerActiveSearch = function(id, query) {
                activeSearchId = id;
                localStorage.setItem('activeSearch', JSON.stringify({ id: id, query: query }));

                const queryEl = document.getElementById('globalQuery');
                if (queryEl) queryEl.innerText = '"' + query + '"';

                document.getElementById('globalViewBtn').href = '/results/' + id;
                document.getElementById('globalScrapingBar').classList.remove('hidden');

                startGlobalPoll();
            };

            document.addEventListener('DOMContentLoaded', bootGlobalBar);
        })();
        </script>

    </body>
</html>