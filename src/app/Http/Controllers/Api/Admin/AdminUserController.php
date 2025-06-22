<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->withCount(['orders', 'addresses']);

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $perPage = $request->get('per_page', 15);
        $users = $query->latest()->paginate($perPage);

        return response()->json([
            'data' => $users->items(),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
            'links' => [
                'next' => $users->nextPageUrl(),
                'prev' => $users->previousPageUrl(),
            ]
        ]);
    }

    public function show(User $user)
    {
        $user->load(['orders.items.product', 'addresses']);

        return response()->json([
            'data' => $user
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,customer',
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
        ]);

        $user->update($request->validated());

        return response()->json([
            'message' => 'User updated successfully',
            'data' => $user
        ]);
    }
} 