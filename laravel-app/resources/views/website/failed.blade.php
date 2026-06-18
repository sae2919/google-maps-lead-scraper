<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Generation Failed</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-900 text-white min-h-screen flex items-center justify-center p-6">

<div class="max-w-md w-full bg-slate-800 rounded-3xl border border-slate-700/80 p-8 text-center shadow-xl">
    <div class="w-16 h-16 bg-red-500/10 border border-red-500/20 text-red-500 text-3xl rounded-full flex items-center justify-center mx-auto mb-6">
        ⚠️
    </div>

    <h1 class="text-2xl font-black mb-2 tracking-tight">Generation Failed</h1>
    <p class="text-slate-400 text-sm mb-8">
        We encountered an issue while generating the AI website config for <span class="font-bold text-white">{{ $site->business_name }}</span>. This can happen due to AI API timeouts or temporary limits.
    </p>

    <div class="space-y-3">
        <form action="{{ route('site.regenerate', $site->id) }}" method="POST">
            @csrf
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-2xl transition shadow-lg shadow-blue-900/20">
                Retry AI Generation
            </button>
        </form>

        <a href="{{ route('dashboard') }}" class="block w-full bg-slate-700 hover:bg-slate-600 text-slate-200 font-bold py-3 rounded-2xl transition">
            Go to Dashboard
        </a>
    </div>
</div>

</body>
</html>
