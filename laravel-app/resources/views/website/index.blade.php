<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Generated Websites | Lead Generator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .site-card { transition: all 0.22s ease; }
        .site-card:hover { transform: translateY(-3px); box-shadow: 0 16px 32px rgba(0,0,0,0.06); }
    </style>
</head>
<body class="bg-[#f8fafc] text-slate-800">

<div class="flex min-h-screen">
    {{-- SIDEBAR --}}
    @include('partials.sidebar')

    <div class="flex-1 ml-64 flex flex-col">
        {{-- TOPBAR --}}
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-end px-8 sticky top-0 z-40">
            <div class="flex items-center gap-4">
                <a href="{{ route('profile.edit') }}"
                    class="w-9 h-9 rounded-lg bg-purple-600 text-white flex items-center justify-center font-bold text-sm hover:ring-4 ring-purple-50 transition-all">
                    {{ strtoupper(substr(auth()->user()->name ?? 'AJ', 0, 2)) }}
                </a>
            </div>
        </header>

        <main class="p-8">
            {{-- PAGE HEADER --}}
            <div class="mb-8">
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">AI Generated Websites</h1>
                <p class="text-sm text-slate-500 mt-1">Manage and preview all landing pages built by SiteForge AI.</p>
            </div>

            {{-- GRID LIST --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($sites as $site)
                    @php
                        $status = strtolower($site->generation_status ?? 'pending');
                        $emoji = '🏢';
                        $cat = strtolower($site->category ?? '');
                        if (str_contains($cat, 'restaurant') || str_contains($cat, 'food')) $emoji = '🍽️';
                        elseif (str_contains($cat, 'hospital') || str_contains($cat, 'health')) $emoji = '🏥';
                        elseif (str_contains($cat, 'gym') || str_contains($cat, 'fitness')) $emoji = '💪';
                        elseif (str_contains($cat, 'hotel') || str_contains($cat, 'stay')) $emoji = '🏨';
                        elseif (str_contains($cat, 'salon') || str_contains($cat, 'beauty')) $emoji = '✂️';
                    @endphp

                    <div class="site-card bg-white rounded-3xl border border-slate-200/80 overflow-hidden shadow-sm flex flex-col justify-between">
                        <div class="p-6">
                            {{-- Header --}}
                            <div class="flex justify-between items-start gap-3 mb-4">
                                <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-2xl shadow-sm border border-slate-100 flex-shrink-0">
                                    {{ $emoji }}
                                </div>

                                @if($status === 'done')
                                    <span class="text-[10px] font-black text-emerald-600 bg-emerald-50 border border-emerald-100 px-2.5 py-1 rounded-full uppercase tracking-widest">
                                        Active
                                    </span>
                                @elseif($status === 'generating' || $status === 'pending')
                                    <span class="text-[10px] font-black text-amber-600 bg-amber-50 border border-amber-100 px-2.5 py-1 rounded-full uppercase tracking-widest animate-pulse">
                                        Building
                                    </span>
                                @else
                                    <span class="text-[10px] font-black text-red-600 bg-red-50 border border-red-100 px-2.5 py-1 rounded-full uppercase tracking-widest">
                                        Failed
                                    </span>
                                @endif
                            </div>

                            {{-- Title & Info --}}
                            <h3 class="font-extrabold text-slate-800 text-lg leading-tight mb-1 truncate" title="{{ $site->business_name }}">
                                {{ $site->business_name }}
                            </h3>
                            <p class="text-xs text-slate-400 capitalize font-medium mb-4">
                                {{ $site->category ?: 'Business' }} · {{ $site->city ?: 'Local area' }}
                            </p>

                            {{-- Contact details --}}
                            <div class="space-y-2 text-xs text-slate-500 font-medium">
                                @if($site->phone)
                                    <p class="flex items-center gap-2">
                                        <span class="text-slate-400">📞</span> {{ $site->phone }}
                                    </p>
                                @endif
                                @if($site->address)
                                    <p class="flex items-center gap-2 truncate" title="{{ $site->address }}">
                                        <span class="text-slate-400">📍</span> {{ $site->address }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        {{-- Footer Actions --}}
                        <div class="bg-slate-50/50 border-t border-slate-100 p-4 flex gap-2">
                            @if($status === 'done')
                                <a href="{{ route('site.view', $site->slug) }}" target="_blank"
                                    class="flex-1 text-center text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 py-2.5 rounded-xl transition-all shadow-sm">
                                    Visit Site
                                </a>
                                <form action="{{ route('site.regenerate', $site->id) }}" method="POST" class="flex-shrink-0" onsubmit="return confirm('Regenerate this website? Existing changes will be replaced.')">
                                    @csrf
                                    <button type="submit" class="p-2.5 bg-white border border-slate-200 text-slate-500 hover:text-blue-600 hover:border-blue-200 rounded-xl transition-all" title="Regenerate">
                                        🔄
                                    </button>
                                </form>
                            @elseif($status === 'generating' || $status === 'pending')
                                <a href="{{ route('site.view', $site->slug) }}"
                                    class="w-full text-center text-xs font-bold text-amber-700 bg-amber-50 hover:bg-amber-100 py-2.5 rounded-xl transition-all border border-amber-200">
                                    View Progress
                                </a>
                            @else
                                <form action="{{ route('site.regenerate', $site->id) }}" method="POST" class="w-full" onsubmit="return confirm('Try generating the website again?')">
                                    @csrf
                                    <button type="submit" class="w-full text-center text-xs font-bold text-red-700 bg-red-50 hover:bg-red-100 py-2.5 rounded-xl transition-all border border-red-200">
                                        Retry Generation
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    {{-- Empty state --}}
                    <div class="col-span-full bg-white rounded-3xl border border-dashed border-slate-300 p-16 text-center">
                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-5 border border-slate-100">
                            🎨
                        </div>
                        <h3 class="text-slate-700 font-bold text-lg mb-2">No websites generated yet</h3>
                        <p class="text-slate-400 text-sm mb-6 max-w-sm mx-auto">Generate a professional AI website for any of your extracted leads.</p>
                        <a href="{{ route('search.page') }}" class="bg-[#2563eb] text-white px-6 py-2.5 rounded-xl font-bold shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all">
                            Find Leads & Generate Sites
                        </a>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $sites->links() }}
            </div>
        </main>
    </div>
</div>

</body>
</html>
