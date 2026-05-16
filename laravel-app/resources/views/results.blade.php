<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lead Results | Lead Generator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .table-container::-webkit-scrollbar { height: 8px; }
        .table-container::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }

        @keyframes pulse-ring {
            0%   { transform: scale(1); opacity: 1; }
            100% { transform: scale(1.8); opacity: 0; }
        }
        .status-dot { position: relative; display: inline-block; width: 8px; height: 8px; border-radius: 50%; }
        .status-dot::after { content: ''; position: absolute; inset: 0; border-radius: 50%; animation: pulse-ring 1.2s ease-out infinite; }
        .dot-running { background: #22c55e; }
        .dot-running::after { background: #22c55e; }
        .dot-paused  { background: #f59e0b; }
        .dot-paused::after  { animation: none; }
        .dot-stopped { background: #ef4444; }
        .dot-stopped::after { animation: none; }
        .dot-done    { background: #6366f1; }
        .dot-done::after    { animation: none; }

        .ctrl-btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 18px; border-radius: 12px;
            font-size: 13px; font-weight: 700;
            cursor: pointer; border: none;
            transition: all 0.18s ease;
        }
        .ctrl-btn:hover   { transform: translateY(-1px); }
        .ctrl-btn:active  { transform: scale(0.97); }
        .ctrl-btn:disabled { opacity: 0.4; cursor: not-allowed; transform: none; }
        .btn-stop   { background: #fee2e2; color: #dc2626; }
        .btn-stop:hover:not(:disabled)   { background: #dc2626; color: #fff; }
        .btn-pause  { background: #fef3c7; color: #d97706; }
        .btn-pause:hover:not(:disabled)  { background: #d97706; color: #fff; }
        .btn-resume { background: #dcfce7; color: #16a34a; }
        .btn-resume:hover:not(:disabled) { background: #16a34a; color: #fff; }

        @keyframes fadeInRow {
            from { opacity: 0; transform: translateY(-6px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .new-row { animation: fadeInRow 0.3s ease; }
    </style>
</head>

<body class="bg-[#f8fafc] text-slate-800">

<div class="flex min-h-screen">

    {{-- SIDEBAR --}}
    <div class="w-64 bg-white border-r border-slate-200 p-4 flex flex-col fixed h-full shadow-sm">
        <div class="flex items-center gap-3 px-2 mb-8">
            <div class="w-10 h-10 bg-[#2563eb] rounded-lg flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-blue-100">LG</div>
            <span class="font-bold text-slate-700 tracking-tight text-lg">Lead Generator</span>
        </div>
        <nav class="space-y-1">
            <a href="/dashboard" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition-all font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Home
            </a>
            <a href="{{ url()->previous() }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition-all font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back
            </a>
        </nav>
    </div>

    <div class="flex-1 ml-64 flex flex-col">

        {{-- TOPBAR --}}
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-end px-8 sticky top-0 z-40">
            <div class="flex items-center gap-4">
                <button onclick="toggleProfileMenu()" id="profileBtn"
                    class="w-9 h-9 rounded-lg bg-purple-600 text-white flex items-center justify-center font-bold text-sm hover:ring-4 ring-purple-50 transition-all">
                    {{ strtoupper(substr(auth()->user()->name ?? 'AJ', 0, 2)) }}
                </button>
                <div id="profileMenu" class="hidden absolute right-4 top-16 w-48 bg-white rounded-2xl shadow-xl border border-slate-100 z-50 py-2">
                    <a href="/profile" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 font-medium">👤 Edit Profile</a>
                    <hr class="my-1 border-slate-100">
                    <form method="POST" action="/logout">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 font-bold">🚪 Logout</button>
                    </form>
                </div>
            </div>
        </header>

        <main class="p-8">

            {{-- PAGE HEADER --}}
            <div class="flex items-center justify-between mb-8 flex-wrap gap-4">
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">
                    Lead Results
                    <span class="text-slate-300 font-normal ml-2">| Live Updates</span>
                </h1>

                {{-- SCRAPER CONTROLS --}}
                <div class="flex items-center gap-3" id="scraperControls">
                    <button id="btnStop" class="ctrl-btn btn-stop" onclick="controlScraper('stop')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <rect x="6" y="6" width="12" height="12" rx="2" stroke-width="2"/>
                        </svg>
                        Stop
                    </button>
                    <button id="btnPause" class="ctrl-btn btn-pause" onclick="controlScraper('pause')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6"/>
                        </svg>
                        Pause
                    </button>
                    <button id="btnResume" class="ctrl-btn btn-resume hidden" onclick="controlScraper('resume')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3l14 9-14 9V3z"/>
                        </svg>
                        Resume
                    </button>
                </div>

                {{-- Bulk Generate Button --}}
                <button id="generateBtn" onclick="generateBulkWebsites()"
                    class="hidden items-center gap-2 bg-emerald-500 text-white px-6 py-3 rounded-2xl font-bold shadow-lg shadow-emerald-100 hover:bg-emerald-600 transition-all active:scale-95">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                    </svg>
                    Generate AI Websites (<span id="selectedCount">0</span>)
                </button>
            </div>

            {{-- FILTERS --}}
            <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
                <div class="flex gap-3">
                    <a href="/export/{{ $id }}"
                        class="inline-flex items-center gap-2 bg-[#2563eb] text-white px-6 py-3 rounded-2xl font-bold shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Download All
                    </a>
                </div>
                <div class="flex gap-3">
                    <select id="ratingFilter" class="bg-white border border-slate-200 text-slate-600 text-xs font-bold uppercase tracking-wider rounded-xl px-4 py-3 focus:ring-4 ring-blue-50 outline-none cursor-pointer">
                        <option value="0">All Ratings</option>
                        <option value="4">4.0+ Stars</option>
                        <option value="3">3.0+ Stars</option>
                    </select>
                    <select id="sourceFilter" class="bg-white border border-slate-200 text-slate-600 text-xs font-bold uppercase tracking-wider rounded-xl px-4 py-3 focus:ring-4 ring-blue-50 outline-none cursor-pointer">
                        <option value="all">All Sources</option>
                        <option value="phone">Has Phone Number</option>
                        <option value="no_website">No Website</option>
                        <option value="has_website">Has Website</option>
                    </select>
                </div>
            </div>

            {{-- STAT CARDS --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Total Places</p>
                    <p class="text-3xl font-black text-slate-800" id="totalPlaces">—</p>
                </div>
                <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Leads Found</p>
                    <p class="text-3xl font-black text-blue-600" id="leadCount">0</p>
                </div>
                <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Progress</p>
                    <p class="text-3xl font-black text-slate-800" id="progressText">0%</p>
                </div>
                <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Status</p>
                    <div class="flex items-center gap-2">
                        <span class="status-dot dot-running" id="statusDot"></span>
                        <span class="text-xl font-black uppercase tracking-tight" id="statusText">Starting...</span>
                    </div>
                </div>
            </div>

            {{-- TABLE --}}
            <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
                <div class="table-container overflow-x-auto">
                    <table class="w-full text-sm text-left border-collapse">
                        <thead class="bg-slate-50/50 border-b border-slate-100 text-slate-400 font-black uppercase text-[10px] tracking-[0.15em]">
                            <tr>
                                <th class="px-8 py-5 w-10 text-center">
                                    <input type="checkbox" id="selectAll" onclick="toggleAllCheckboxes(this)"
                                        class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                </th>
                                <th class="px-8 py-5">Business Name</th>
                                <th class="px-8 py-5">Contact Details</th>
                                <th class="px-8 py-5 text-center">Website</th>
                                <th class="px-8 py-5">Address & Location</th>
                                <th class="px-8 py-5 text-center">Map</th>
                                <th class="px-8 py-5 text-center">Rating</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody" class="divide-y divide-slate-50"></tbody>
                    </table>
                </div>
                <div class="bg-slate-50/30 px-8 py-5 flex items-center justify-between border-t border-slate-100">
                    <span class="text-slate-400 text-xs font-bold uppercase tracking-widest" id="pageInfo">Loading...</span>
                    <div id="pagination" class="flex items-center gap-3"></div>
                </div>
            </div>

        </main>
    </div>
</div>

<script>
const searchId       = "{{ $id }}";
const csrfToken      = document.querySelector('meta[name="csrf-token"]').content;
let currentPage      = 1;
let currentLeadsData = [];
let selectedLeadIds  = new Set();
let pollingInterval  = null;
let leadsInterval    = null;
let currentStatus    = 'RUNNING';
let lastFoundCount   = 0;

let wasActiveThisSession = false;
let redirectScheduled    = false;

function toggleProfileMenu() {
    document.getElementById("profileMenu").classList.toggle("hidden");
}

// ── SCRAPER CONTROLS ──────────────────────────────────────────────────────────
async function controlScraper(action) {
    const btn = document.getElementById('btn' + action.charAt(0).toUpperCase() + action.slice(1));
    if (btn) btn.disabled = true;

    try {
        const res  = await fetch(`/api/${action}/${searchId}`, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        });
        const data = await res.json();
        console.log(`${action}:`, data);
        await loadProgress();
    } catch (err) {
        console.error(`${action} error:`, err);
        alert(`Failed to ${action} scraper.`);
    } finally {
        if (btn) btn.disabled = false;
    }
}

// ── UPDATE CONTROL BUTTONS ────────────────────────────────────────────────────
function updateControlButtons(status) {
    const btnStop   = document.getElementById('btnStop');
    const btnPause  = document.getElementById('btnPause');
    const btnResume = document.getElementById('btnResume');
    const statusDot = document.getElementById('statusDot');
    const statusEl  = document.getElementById('statusText');

    statusDot.classList.remove('dot-running', 'dot-paused', 'dot-stopped', 'dot-done');

    if (status === 'RUNNING') {
        btnStop.classList.remove('hidden');
        btnPause.classList.remove('hidden');
        btnResume.classList.add('hidden');
        btnStop.disabled  = false;
        btnPause.disabled = false;
        statusDot.classList.add('dot-running');
        statusEl.style.color = '#16a34a';

    } else if (status === 'PAUSED') {
        btnStop.classList.remove('hidden');
        btnPause.classList.add('hidden');
        btnResume.classList.remove('hidden');
        btnStop.disabled   = false;
        btnResume.disabled = false;
        statusDot.classList.add('dot-paused');
        statusEl.style.color = '#d97706';

    } else if (status === 'STOPPED') {
        btnStop.classList.add('hidden');
        btnPause.classList.add('hidden');
        btnResume.classList.remove('hidden');
        btnResume.disabled = false;
        statusDot.classList.add('dot-stopped');
        statusEl.style.color = '#dc2626';

    } else if (status === 'COMPLETED') {
        btnStop.classList.add('hidden');
        btnPause.classList.add('hidden');
        btnResume.classList.add('hidden');
        statusDot.classList.add('dot-done');
        statusEl.style.color = '#6366f1';
    }

    currentStatus = status;
}

// ── LOAD PROGRESS ─────────────────────────────────────────────────────────────
function loadProgress() {
    return fetch(`/api/progress/${searchId}`)
        .then(res => res.json())
        .then(data => {
            // 🔥 FIX: use correct fields
            const found    = data.found    || 0;   // actual lead count
            const total    = data.total    || 0;   // total places from Python
            const pct      = data.progress || 0;   // percentage 0-100
            let   status   = data.status   || 'RUNNING';

            // 🔥 FIX: completion detection uses found vs total (not pct vs total)
            if (status === 'RUNNING' && found > 0 && total > 0 && found >= total) {
                status = 'COMPLETED';
                fetch(`/api/stop/${searchId}`, {
                    method:  'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken }
                }).catch(() => {});
            }

            if (status === 'STOPPED' && found > 0 && total > 0 && found >= total) {
                status = 'COMPLETED';
            }

            // 🔥 FIX: always show total even if 0 during scroll phase (show live count)
            document.getElementById("totalPlaces").innerText = total > 0 ? total : '—';

            // 🔥 FIX: leadCount shows actual found count, not percentage
            document.getElementById("leadCount").innerText  = found;

            // 🔥 FIX: progressText shows percentage with % symbol
            document.getElementById("progressText").innerText = pct + '%';

            document.getElementById("statusText").innerText = status;

            updateControlButtons(status);

            // 🔥 If new leads arrived, reload table automatically on page 1
            if (found !== lastFoundCount) {
                lastFoundCount = found;
                // Only auto-reload table if user is on page 1 to avoid disrupting browsing
                if (currentPage === 1) {
                    loadLeads(1);
                }
            }

            if (status === 'RUNNING' || status === 'PAUSED') {
                wasActiveThisSession = true;
            }

            if (status === 'STOPPED' || status === 'COMPLETED') {
                stopAllPolling();

                if (wasActiveThisSession && !redirectScheduled) {
                    redirectScheduled = true;
                    const msg = status === 'COMPLETED'
                        ? '✅ Completed! Redirecting...'
                        : '⛔ Stopped. Redirecting...';
                    document.getElementById("statusText").innerText = msg;
                    setTimeout(() => {
                        window.location.href = `/results/${searchId}`;
                    }, 2500);
                }
            }

            return data;
        });
}

// ── STOP ALL POLLING ──────────────────────────────────────────────────────────
function stopAllPolling() {
    if (pollingInterval) { clearInterval(pollingInterval); pollingInterval = null; }
    if (leadsInterval)   { clearInterval(leadsInterval);   leadsInterval   = null; }
}

// ── LOAD LEADS TABLE ──────────────────────────────────────────────────────────
function loadLeads(page = 1) {
    const source = document.getElementById('sourceFilter')?.value || '';
    const rating = document.getElementById('ratingFilter')?.value || '';

    return fetch(`/api/leads/${searchId}?page=${page}&source=${source}&rating=${rating}`)
        .then(res => res.json())
        .then(data => {
            currentLeadsData = data.data || [];
            renderTable();
            renderPagination(data);
            return data;
        });
}

document.getElementById('sourceFilter')?.addEventListener('change', () => { loadLeads(1); });
document.getElementById('ratingFilter')?.addEventListener('change', () => { loadLeads(1); });

// ── RENDER TABLE ──────────────────────────────────────────────────────────────
function renderTable() {
    const tbody  = document.getElementById("tableBody");
    const minRat = parseFloat(document.getElementById('ratingFilter').value);
    const srcVal = document.getElementById('sourceFilter').value;

    const filtered = currentLeadsData.filter(lead => {
        const noWeb = (!lead.website || lead.website === '-' || lead.website.trim() === '');
        let src = true;
        if (srcVal === 'phone')       src = (lead.phone && lead.phone !== '-');
        if (srcVal === 'no_website')  src = noWeb;
        if (srcVal === 'has_website') src = !noWeb;
        return parseFloat(lead.rating || 0) >= minRat && src;
    });

    if (filtered.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7" class="px-8 py-10 text-center text-slate-400 italic font-medium">No results match your filters.</td></tr>`;
        return;
    }

    // 🔥 Track existing row IDs to animate only new ones
    const existingIds = new Set(
        [...tbody.querySelectorAll('tr[data-id]')].map(r => r.dataset.id)
    );

    tbody.innerHTML = '';

    filtered.forEach(lead => {
        const noWebsite   = (!lead.website || lead.website === '-' || lead.website.trim() === '');
        const isGenerated = lead.website && lead.website.includes('/sites/');
        const isChecked   = selectedLeadIds.has(lead.id.toString()) ? 'checked' : '';
        const isNew       = !existingIds.has(lead.id.toString());

        tbody.innerHTML += `
        <tr class="hover:bg-blue-50/30 transition-colors group ${isNew ? 'new-row' : ''}" data-id="${lead.id}">
            <td class="px-8 py-5 text-center">
                ${(noWebsite || isGenerated)
                    ? `<input type="checkbox" class="lead-checkbox w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500" value="${lead.id}" ${isChecked} onchange="handleCheckboxClick(this)">`
                    : `<div class="flex items-center justify-center" title="Original Website Exists">
                         <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                           <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                         </svg>
                       </div>`
                }
            </td>
            <td class="px-8 py-5">
                <div class="flex flex-col">
                    <p class="font-extrabold text-slate-700 group-hover:text-blue-600 transition-colors">${lead.name || '-'}</p>
                    ${isGenerated ? `<span class="text-[8px] font-black text-red-400 uppercase tracking-widest mt-1">AI Landing Page Live</span>` : ''}
                </div>
            </td>
            <td class="px-8 py-5">
                <div class="flex flex-col">
                    <span class="text-blue-600 font-bold tracking-tight">${lead.phone || '-'}</span>
                    <span class="text-slate-400 text-[11px] font-medium italic">${lead.email || ''}</span>
                </div>
            </td>
            <td class="px-8 py-5 text-center">
                ${!noWebsite
                    ? `<a href="${lead.website}" target="_blank"
                            class="text-[10px] px-3 py-1.5 rounded-lg font-black uppercase tracking-tighter transition-all shadow-sm
                            ${isGenerated ? 'bg-red-50 text-red-600 border border-red-200 hover:bg-red-600 hover:text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-blue-600 hover:text-white'}">
                            ${isGenerated ? 'View AI Site' : 'Visit'}
                       </a>`
                    : '<span class="text-slate-300">-</span>'
                }
            </td>
            <td class="px-8 py-5 max-w-xs">
                <p class="text-slate-500 text-xs leading-relaxed mb-2">${lead.address || '-'}</p>
                <div class="flex gap-2">
                    <span class="text-[9px] font-black text-blue-500 bg-blue-50 px-1.5 py-0.5 rounded uppercase tracking-widest">${lead.main_area || 'Area'}</span>
                    <span class="text-[9px] font-black text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded tracking-widest">${lead.pincode || ''}</span>
                </div>
            </td>
            <td class="px-8 py-5 text-center">
                <a href="${lead.maps_url}" target="_blank"
                    class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-slate-50 text-slate-400 hover:bg-blue-100 hover:text-blue-600 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </a>
            </td>
            <td class="px-8 py-5 text-center">
                <div class="inline-flex items-center gap-1 font-black text-orange-500 bg-orange-50 px-3 py-1.5 rounded-xl text-xs">
                    ${lead.rating || '0.0'} <span class="text-[10px]">★</span>
                </div>
            </td>
        </tr>`;
    });

    updateSelectedCount();
}

// ── BULK GENERATE ─────────────────────────────────────────────────────────────
function handleCheckboxClick(cb) {
    if (cb.checked) selectedLeadIds.add(cb.value.toString());
    else            selectedLeadIds.delete(cb.value.toString());
    updateSelectedCount();
}

function toggleAllCheckboxes(master) {
    document.querySelectorAll('.lead-checkbox').forEach(cb => {
        cb.checked = master.checked;
        if (master.checked) selectedLeadIds.add(cb.value.toString());
        else                selectedLeadIds.delete(cb.value.toString());
    });
    updateSelectedCount();
}

function updateSelectedCount() {
    const count = selectedLeadIds.size;
    const btn   = document.getElementById('generateBtn');
    document.getElementById('selectedCount').innerText = count;
    if (count > 0) { btn.classList.remove('hidden'); btn.classList.add('flex'); }
    else           { btn.classList.remove('flex');   btn.classList.add('hidden'); }
}

function generateBulkWebsites() {
    const ids = Array.from(selectedLeadIds);
    if (ids.length === 0) return;
    if (!confirm(`Generate AI landing pages for ${ids.length} businesses?`)) return;

    const btn  = document.getElementById('generateBtn');
    const orig = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = `<svg class="animate-spin h-5 w-5 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle class="opacity-25" cx="12" cy="12" r="10" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg> Building...`;

    fetch('/generate-bulk-websites', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body:    JSON.stringify({ ids }),
    })
    .then(async res => { const d = await res.json(); if (!res.ok) throw new Error(d.message || 'Error'); return d; })
    .then(data => { alert(data.message); selectedLeadIds.clear(); loadLeads(currentPage); })
    .catch(err => alert('Error: ' + err.message))
    .finally(() => { btn.disabled = false; btn.innerHTML = orig; updateSelectedCount(); });
}

// ── PAGINATION ────────────────────────────────────────────────────────────────
function renderPagination(data) {
    const container = document.getElementById("pagination");
    const pageInfo  = document.getElementById("pageInfo");
    if (pageInfo) pageInfo.innerText = `Page ${data.current_page} of ${data.last_page}`;
    if (!data.last_page || data.last_page <= 1) { container.innerHTML = ""; return; }

    container.innerHTML = `
        <button onclick="changePage(${data.current_page - 1})"
            ${data.current_page === 1 ? 'disabled' : ''}
            class="flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 disabled:opacity-30 disabled:cursor-not-allowed transition-all text-xs font-bold text-slate-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Prev
        </button>
        <div class="px-4 py-2 bg-slate-50 rounded-xl text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] border border-slate-100">
            ${data.current_page} / ${data.last_page}
        </div>
        <button onclick="changePage(${data.current_page + 1})"
            ${data.current_page === data.last_page ? 'disabled' : ''}
            class="flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 disabled:opacity-30 disabled:cursor-not-allowed transition-all text-xs font-bold text-slate-600">
            Next
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>`;
}

function changePage(p) {
    if (p < 1) return;
    currentPage = p;
    loadLeads(p);
}

// ── POLLING ───────────────────────────────────────────────────────────────────
function startPolling() {
    if (pollingInterval) clearInterval(pollingInterval);

    // 🔥 Progress polls every 2s — drives stat card updates + triggers table reload when found count changes
    pollingInterval = setInterval(() => {
        loadProgress();
    }, 2000);
}

// ── INIT ──────────────────────────────────────────────────────────────────────
async function init() {
    document.getElementById("statusText").innerText = "Connecting...";

    try {
        await loadLeads(currentPage);
        await loadProgress();

        if (currentStatus === 'RUNNING' || currentStatus === 'PAUSED') {
            startPolling();
        }
    } catch (err) {
        console.error("Init failed:", err);
        document.getElementById("statusText").innerText = "Error";
    }
}

document.addEventListener('DOMContentLoaded', init);
</script>
@include('partials.global-bar')
</body>
</html>