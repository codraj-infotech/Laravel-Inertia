<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Display a listing of users with search, sorting, and pagination.
     */
    public function index(Request $request): Response
    {
        $search = $request->input('search', '');
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        $sortField = $request->input('sort_field', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        // Validate sort field to prevent SQL injection
        $allowedSortFields = ['name', 'email', 'created_at'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'created_at';
        }

        // Validate sort order
        $sortOrder = strtolower($sortOrder) === 'asc' ? 'asc' : 'desc';

        $query = User::query();

        // Search functionality
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        $users = $query->orderBy($sortField, $sortOrder)
                       ->paginate($perPage, ['*'], 'page', $page);

        return Inertia::render('Users/Index', [
            'users' => [
                'data' => $users->items(),
                'current_page' => $users->currentPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
                'last_page' => $users->lastPage(),
            ],
            'filters' => [
                'search' => $search,
                'sort_field' => $sortField,
                'sort_order' => $sortOrder,
            ],
        ]);
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('users.index')
            ->with('message', 'User created successfully!');
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('users.index')
            ->with('message', 'User updated successfully!');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user): RedirectResponse
    {
        // Prevent deleting own account
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot delete your own account!');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('message', 'User deleted successfully!');
    }
}
