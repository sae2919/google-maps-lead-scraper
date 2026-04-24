<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Search;
use App\Models\Lead;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalUsers = User::count();
        $totalSearches = Search::count();
        $totalLeads = Lead::count();

        $latestUsers = User::latest()->limit(5)->get();
        $latestSearches = Search::latest()->limit(5)->get();

        // 🔥 CHART DATA
        $leadsPerDay = Lead::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $searchesPerDay = Search::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // 🔥 TOP SEARCHES (SAFE)
        $topSearches = Search::selectRaw('query, COUNT(*) as count')
            ->groupBy('query')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // 🔥 ALL USERS + LEADS
        $users = User::when(request('search'), function ($q) {
        $q->where('name', 'like', '%' . request('search') . '%')
          ->orWhere('email', 'like', '%' . request('search') . '%');
    })
    ->latest()
    ->paginate(5);
        $leads = Lead::latest()->limit(20)->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalSearches',
            'totalLeads',
            'latestUsers',
            'latestSearches',
            'leadsPerDay',
            'searchesPerDay',
            'topSearches',
            'users',
            'leads'
        ));
    }
    public function deleteUser($id)
{
    $user = \App\Models\User::findOrFail($id);

    // ❌ Prevent deleting yourself (IMPORTANT)
    if ($user->id == auth()->id()) {
        return back()->with('error', 'You cannot delete yourself!');
    }

    $user->delete();

    return back()->with('success', 'User deleted successfully!');
}
public function updateUser(Request $request, $id)
{
    $user = \App\Models\User::findOrFail($id);

    $user->name = $request->name;
    $user->email = $request->email;
    $user->save();

    return back()->with('success', 'User updated!');
}

public function toggleRole($id)
{
    $user = \App\Models\User::findOrFail($id);

    $user->role = $user->role === 'admin' ? 'user' : 'admin';
    $user->save();

    return back()->with('success', 'Role updated!');
}
}