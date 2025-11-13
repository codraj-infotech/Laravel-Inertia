<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Froiden\RestAPI\ApiResponse;

class UserController extends Controller
{
    /**
     * Display a listing of users for Inertia (initial page load).
     */
    public function index(Request $request): Response
    {
        return Inertia::render('Users/Index');
    }

    /**
     * Get users list via API with laravel-rest-api features.
     * Supports pagination, filtering, sorting, and field selection.
     */
    public function apiIndex(Request $request): JsonResponse
    {
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $filter = $request->input('filter', '');
        $sort = $request->input('sort', '-created_at');
        $fields = $request->input('fields', '');

        $query = User::query();

        // Apply field selection (if specified)
        if ($fields) {
            $fieldsArray = explode(',', $fields);
            $query->select($fieldsArray);
        } else {
            // Use default fields from model
            $defaultFields = (new User())->default ?? ['*'];
            $query->select($defaultFields);
        }

        // Apply filters
        if ($filter) {
            $filters = explode(',', $filter);
            $query->where(function ($q) use ($filters) {
                foreach ($filters as $filterItem) {
                    if (strpos($filterItem, ':') !== false) {
                        [$field, $value] = explode(':', $filterItem, 2);

                        // Check if field is filterable
                        $filterableFields = (new User())->filterable ?? [];
                        if (in_array($field, $filterableFields)) {
                            $q->orWhere($field, 'like', "%{$value}%");
                        }
                    }
                }
            });
        }

        // Apply sorting
        if ($sort) {
            $sortOrder = 'asc';
            $sortField = $sort;

            if (substr($sort, 0, 1) === '-') {
                $sortOrder = 'desc';
                $sortField = substr($sort, 1);
            }

            $query->orderBy($sortField, $sortOrder);
        }

        // Paginate
        $users = $query->paginate($limit, ['*'], 'page', $page);

        return response()->json([
            'data' => $users->items(),
            'meta' => [
                'current_page' => $users->currentPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
                'last_page' => $users->lastPage(),
                'from' => $users->firstItem(),
                'to' => $users->lastItem(),
            ],
        ]);
    }

    /**
     * Store a newly created user via API.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'min:8'],
            'profile_image' => ['nullable', 'string', 'max:500'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:male,female,other'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'profile_image' => $validated['profile_image'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'data' => ['id' => $user->id],
        ], 201);
    }

    /**
     * Display the specified user via API.
     */
    public function show(Request $request, $id): JsonResponse
    {
        $fields = $request->input('fields', '');

        $query = User::query();

        if ($fields) {
            $fieldsArray = explode(',', $fields);
            $query->select($fieldsArray);
        }

        $user = $query->findOrFail($id);

        return response()->json([
            'data' => $user,
        ]);
    }

    /**
     * Update the specified user via API.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'min:8'],
            'profile_image' => ['nullable', 'string', 'max:500'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:male,female,other'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:1000'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->profile_image = $validated['profile_image'] ?? null;
        $user->date_of_birth = $validated['date_of_birth'] ?? null;
        $user->gender = $validated['gender'] ?? null;
        $user->phone = $validated['phone'] ?? null;
        $user->address = $validated['address'] ?? null;

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return response()->json([
            'message' => 'User updated successfully',
            'data' => ['id' => $user->id],
        ]);
    }

    /**
     * Remove the specified user via API.
     */
    public function destroy($id): JsonResponse
    {
        $user = User::findOrFail($id);

        // Prevent deleting own account
        if ($user->id === auth()->id()) {
            return response()->json([
                'message' => 'You cannot delete your own account',
            ], 403);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully',
        ]);
    }
}
