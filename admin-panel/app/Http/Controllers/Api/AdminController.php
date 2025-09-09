<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /**
     * Display a listing of admins
     */
    public function index(Request $request)
    {
        $query = Admin::query();

        // Search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status')) {
            $status = $request->get('status');
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Filter by role
        if ($request->has('role')) {
            $query->where('role', $request->get('role'));
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $admins = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'admins' => $admins,
        ]);
    }

    /**
     * Store a newly created admin
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,moderator',
        ]);

        $admin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Admin created successfully',
            'admin' => $admin,
        ], 201);
    }

    /**
     * Display the specified admin
     */
    public function show(Admin $admin)
    {
        return response()->json([
            'success' => true,
            'admin' => $admin,
        ]);
    }

    /**
     * Update the specified admin
     */
    public function update(Request $request, Admin $admin)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => [
                'sometimes',
                'email',
                'max:255',
                Rule::unique('admins')->ignore($admin->id),
            ],
            'role' => 'sometimes|in:admin,moderator',
            'is_active' => 'sometimes|boolean',
        ]);

        // Prevent self-deactivation
        if ($request->has('is_active') && !$request->is_active && $admin->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot deactivate your own account',
            ], 422);
        }

        // Prevent role change for self
        if ($request->has('role') && $admin->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot change your own role',
            ], 422);
        }

        $admin->update($request->only(['name', 'email', 'role', 'is_active']));

        return response()->json([
            'success' => true,
            'message' => 'Admin updated successfully',
            'admin' => $admin->fresh(),
        ]);
    }

    /**
     * Remove the specified admin
     */
    public function destroy(Admin $admin)
    {
        // Prevent self-deletion
        if ($admin->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account',
            ], 422);
        }

        // Prevent deletion of super admin
        if ($admin->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Super admin cannot be deleted',
            ], 422);
        }

        $admin->delete();

        return response()->json([
            'success' => true,
            'message' => 'Admin deleted successfully',
        ]);
    }

    /**
     * Toggle admin status
     */
    public function toggleStatus(Admin $admin)
    {
        // Prevent self-deactivation
        if ($admin->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot change your own status',
            ], 422);
        }

        $admin->update([
            'is_active' => !$admin->is_active,
        ]);

        $status = $admin->is_active ? 'activated' : 'deactivated';

        return response()->json([
            'success' => true,
            'message' => "Admin {$status} successfully",
            'admin' => $admin->fresh(),
        ]);
    }

    /**
     * Change admin role
     */
    public function changeRole(Request $request, Admin $admin)
    {
        $request->validate([
            'role' => 'required|in:admin,moderator',
        ]);

        // Prevent role change for self
        if ($admin->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot change your own role',
            ], 422);
        }

        // Prevent changing super admin role
        if ($admin->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Super admin role cannot be changed',
            ], 422);
        }

        $admin->update([
            'role' => $request->role,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Admin role updated successfully',
            'admin' => $admin->fresh(),
        ]);
    }
}