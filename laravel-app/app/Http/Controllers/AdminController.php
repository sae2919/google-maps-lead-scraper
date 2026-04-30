<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Search;
use App\Models\Lead;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        return view('admin.dashboard', [

            // ── Core stats ──────────────────────────────────────────────
            'totalUsers'    => User::count(),
            'totalSearches' => Search::count(),
            'totalLeads'    => Lead::count(),

            // ── Recent activity ─────────────────────────────────────────
            'latestUsers'   => User::latest()->limit(5)->get(),
            'latestSearches'=> Search::latest()->limit(5)->get(),
            'leads'         => Lead::latest()->limit(20)->get(),

            // ── Chart data ───────────────────────────────────────────────
            'leadsPerDay' => Lead::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get(),

            'searchesPerDay' => Search::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get(),

            // ── Top search queries ───────────────────────────────────────
            'topSearches' => Search::selectRaw('query, COUNT(*) as count')
                ->groupBy('query')
                ->orderByDesc('count')
                ->limit(8)
                ->get(),

            // ── User table with optional search filter + pagination ──────
            'users' => User::when($request->input('search'), function ($q) use ($request) {
                    $q->where('name',  'like', '%' . $request->input('search') . '%')
                      ->orWhere('email', 'like', '%' . $request->input('search') . '%');
                })
                ->latest()
                ->paginate(10),
        ]);
    }

    // ── UPDATE USER NAME / EMAIL ─────────────────────────────────────────────
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->name  = $request->input('name');
        $user->email = $request->input('email');
        $user->save();

        return back()->with('success', 'User updated successfully.');
    }

    // ── TOGGLE ROLE (admin ↔ user) ───────────────────────────────────────────
    public function toggleRole($id)
    {
        $user = User::findOrFail($id);
        $user->role = $user->role === 'admin' ? 'user' : 'admin';
        $user->save();

        return back()->with('success', 'Role updated to ' . $user->role . '.');
    }

    // ── DELETE USER ──────────────────────────────────────────────────────────
    public function deleteUser($id)
    {
        // Prevent self-deletion
        if ((int) $id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        User::findOrFail($id)->delete();

        return back()->with('success', 'User deleted successfully.');
    }
}