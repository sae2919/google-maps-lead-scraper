<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lead Results | Lead Generator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .table-container::-webkit-scrollbar { height: 8px; }
        .table-container::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }
    </style>
</head>

<body class="bg-[#f8fafc] text-slate-800">

<div class="flex min-h-screen">

    <div class="w-64 bg-white border-r border-slate-200 p-4 flex flex-col fixed h-full shadow-sm">
        <div class="flex items-center gap-3 px-2 mb-8">
            <div class="w-10 h-10 bg-[#2563eb] rounded-lg flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-blue-100">LG</div>
            <span class="font-bold text-slate-700 tracking-tight text-lg">Lead Generator</span>
        </div>

        <nav class="space-y-1">
            <a href="/" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition-all font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Home
            </a>
            <a href="{{ url()->previous() }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition-all font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back
            </a>
        </nav>
    </div>

    <div class="flex-1 ml-64 flex flex-col">

        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-end px-8 sticky top-0 z-40">
            <div class="flex items-center gap-4">
                <div class="relative">
                    <button onclick="toggleProfileMenu()" id="profileBtn" class="w-9 h-9 rounded-lg bg-purple-600 text-white flex items-center justify-center font-bold text-sm hover:ring-4 ring-purple-50 transition-all">
                        {{ strtoupper(substr(auth()->user()->name ?? 'AJ', 0, 2)) }}
                    </button>
                    <div id="profileMenu" class="hidden absolute right-0 mt-3 w-48 bg-white rounded-2xl shadow-xl border border-slate-100 z-50 py-2">
                        <a href="/profile" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 font-medium">👤 Edit Profile</a>
                        <hr class="my-1 border-slate-100">
                        <form method="POST" action="/logout">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 font-bold">🚪 Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="p-8">
            <div class="mb-8">
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Lead Results <span class="text-slate-300 font-normal ml-2">| Live Updates</span></h1>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
                <div class="flex gap-3">
                    <a href="/export/{{ $id }}" class="inline-flex items-center gap-2 bg-[#2563eb] text-white px-6 py-3 rounded-2xl font-bold shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Download All
                    </a>
                    <button onclick="exportVisible()" class="inline-flex items-center gap-2 bg-white border border-slate-200 text-slate-700 px-6 py-3 rounded-2xl font-bold hover:bg-slate-50 transition-all shadow-sm active:scale-95">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/></svg>
                        Export Filtered
                    </button>
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
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Total Places</p>
                    <p class="text-3xl font-black text-slate-800" id="totalPlaces">0</p>
                </div>
                <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Leads Found</p>
                    <p class="text-3xl font-black text-blue-600" id="leadCount">0</p>
                </div>
                <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Items Processed</p>
                    <p class="text-3xl font-black text-slate-800" id="progressText">0</p>
                </div>
                <div class="bg-white rounded-3xl p-6 border border-blue-50 bg-blue-50/30 shadow-sm">
                    <p class="text-[10px] font-black text-blue-400 uppercase tracking-[0.2em] mb-1">Status</p>
                    <p class="text-xl font-black text-blue-600 animate-pulse uppercase tracking-tight" id="statusText">Starting...</p>
                </div>
            </div>

            <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
                <div class="table-container overflow-x-auto">
                    <table class="w-full text-sm text-left border-collapse">
                        <thead class="bg-slate-50/50 border-b border-slate-100 text-slate-400 font-black uppercase text-[10px] tracking-[0.15em]">
                            <tr>
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
                    <span class="text-slate-400 text-xs font-bold uppercase tracking-widest" id="pageInfo">Awaiting live updates...</span>
                    <div id="pagination" class="flex items-center gap-2"></div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
const searchId = "{{ $id }}";
let currentPage = 1;
let currentLeadsData = []; // Store leads for filtering

// 🔥 LISTEN FOR FILTER CHANGES
document.getElementById('ratingFilter').addEventListener('change', renderTable);
document.getElementById('sourceFilter').addEventListener('change', renderTable);

function toggleProfileMenu() {
    document.getElementById("profileMenu").classList.toggle("hidden");
}

function loadLeads(page = 1) {
    fetch(`/api/leads/${searchId}?page=${page}`)
    .then(res => res.json())
    .then(data => {
        currentLeadsData = data.data; // Update local storage
        renderTable(); // Draw table with current filters
        renderPagination(data);
    });
}

function renderTable() {
    const tbody = document.getElementById("tableBody");
    const minRating = parseFloat(document.getElementById('ratingFilter').value);
    const sourceVal = document.getElementById('sourceFilter').value;
    
    tbody.innerHTML = "";

    // Apply Filter Logic
    const filteredLeads = currentLeadsData.filter(lead => {
        const ratingMatch = parseFloat(lead.rating || 0) >= minRating;
        const phoneMatch = sourceVal === 'phone' ? (lead.phone && lead.phone !== '-') : true;
        return ratingMatch && phoneMatch;
    });

    if (filteredLeads.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" class="px-8 py-10 text-center text-slate-400 italic font-medium">No results match your filters on this page.</td></tr>`;
        return;
    }

    filteredLeads.forEach(lead => {
        tbody.innerHTML += `
        <tr class="hover:bg-blue-50/30 transition-colors group">
            <td class="px-8 py-5">
                <p class="font-extrabold text-slate-700 group-hover:text-blue-600 transition-colors">${lead.name || '-'}</p>
            </td>
            <td class="px-8 py-5">
                <div class="flex flex-col">
                    <span class="text-blue-600 font-bold tracking-tight">${lead.phone || '-'}</span>
                    <span class="text-slate-400 text-[11px] font-medium italic">${lead.email || ''}</span>
                </div>
            </td>
            <td class="px-8 py-5 text-center">
                ${lead.website ? 
                    `<a href="${lead.website}" target="_blank" class="text-[10px] bg-white border border-slate-200 text-slate-600 px-3 py-1.5 rounded-lg font-black hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all shadow-sm uppercase tracking-tighter">Visit</a>` 
                    : '<span class="text-slate-300">-</span>'}
            </td>
            <td class="px-8 py-5 max-w-xs">
                <p class="text-slate-500 text-xs leading-relaxed mb-2">${lead.address || '-'}</p>
                <div class="flex gap-2">
                    <span class="text-[9px] font-black text-blue-500 bg-blue-50 px-1.5 py-0.5 rounded uppercase tracking-widest">${lead.main_area || lead.area || 'Unknown Area'}</span>
                    <span class="text-[9px] font-black text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded tracking-widest">${lead.pincode || 'No Pin'}</span>
                </div>
            </td>
            <td class="px-8 py-5 text-center">
                <a href="${lead.maps_url}" target="_blank" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-slate-50 text-slate-400 hover:bg-blue-100 hover:text-blue-600 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </a>
            </td>
            <td class="px-8 py-5 text-center">
                <div class="inline-flex items-center gap-1 font-black text-orange-500 bg-orange-50 px-3 py-1.5 rounded-xl text-xs">
                    ${lead.rating || '0.0'} <span class="text-[10px]">★</span>
                </div>
            </td>
        </tr>`;
    });
}

function loadProgress() {
    fetch(`/api/progress/${searchId}`)
    .then(res => res.json())
    .then(data => {
        document.getElementById("totalPlaces").innerText = data.total > 0 ? data.total : data.progress;
        document.getElementById("progressText").innerText = data.progress || 0;
        document.getElementById("leadCount").innerText = data.progress || 0;
        
        const statusEl = document.getElementById("statusText");
        statusEl.innerText = data.status || 'Scraping...';
        
        if(data.status === 'COMPLETED') {
            statusEl.classList.remove('animate-pulse', 'text-blue-600');
            statusEl.classList.add('text-emerald-500');
            clearInterval(interval);
        }
    });
}

function renderPagination(data) {
    let container = document.getElementById("pagination");
    if (data.last_page <= 1) { container.innerHTML = ""; return; }

    container.innerHTML = `
        <button onclick="changePage(${data.current_page - 1})" ${data.current_page === 1 ? 'disabled' : ''} class="p-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 disabled:opacity-30 transition-all shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </button>
        <span class="text-[10px] font-black text-slate-500 px-4 uppercase tracking-[0.2em]">${data.current_page} / ${data.last_page}</span>
        <button onclick="changePage(${data.current_page + 1})" ${data.current_page === data.last_page ? 'disabled' : ''} class="p-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 disabled:opacity-30 transition-all shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
    `;
    document.getElementById("pageInfo").innerText = `Viewing page ${data.current_page} of ${data.last_page}`;
}

function changePage(p) { currentPage = p; loadLeads(p); }

let interval = setInterval(() => { loadLeads(currentPage); loadProgress(); }, 4000);
loadLeads(currentPage);
loadProgress();

document.addEventListener('click', (e) => {
    const menu = document.getElementById('profileMenu');
    const btn = document.getElementById('profileBtn');
    if(btn && !btn.contains(e.target)) menu.classList.add('hidden');
});

// Helper for Exporting only what's currently in the HTML table
function exportVisible() {
    let rows = document.querySelectorAll("#tableBody tr");
    let data = [];
    rows.forEach(row => {
        let cols = row.querySelectorAll("td");
        if (cols.length > 1) {
            data.push({
                name: cols[0].innerText,
                phone: cols[1].innerText,
                address: cols[3].innerText,
                rating: cols[5].innerText
            });
        }
    });
    // Call your existing exportFiltered route with this data...
}
</script>
</body>
</html>