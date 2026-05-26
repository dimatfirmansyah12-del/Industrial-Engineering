<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private const ROLES = [
        'admin',
        'manager',
        'customer',
        'approver',
        'drafter',
        'section_head',
        'division_head',
        'director',
        'purchasing',
        'workshop',
        'qc',
    ];

    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('role', 'like', '%' . $search . '%')
                    ->orWhere('position', 'like', '%' . $search . '%');
            });
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        return view('users.index', compact('users'));
    }

    public function edit(User $user)
    {
        return view('users.edit', [
            'user' => $user,
            'roles' => self::ROLES,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => 'required|string|in:' . implode(',', self::ROLES),
            'position' => 'nullable|string|max:255',
        ]);

        $user->update([
            'role' => $validated['role'],
            'position' => $validated['position'] ?? null,
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', 'Role user berhasil diperbarui.');
    }
}
