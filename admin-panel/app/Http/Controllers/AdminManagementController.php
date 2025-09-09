<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminManagementController extends Controller
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

        return view('admin.admins.index', compact('admins'));
    }

    /**
     * Show the form for creating a new admin
     */
    public function create()
    {
        return view('admin.admins.create');
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

        Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_active' => true,
        ]);

        return redirect()->route('admin.admins.index')->with('success', 'Admin created successfully');
    }

    /**
     * Display the specified admin
     */
    public function show(Admin $admin)
    {
        return view('admin.admins.show', compact('admin'));
    }

    /**
     * Show the form for editing the specified admin
     */
    public function edit(Admin $admin)
    {
        return view('admin.admins.edit', compact('admin'));
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
        if ($request->has('is_active') && !$request->is_active && $admin->id === auth('admin')->id()) {
            return redirect()->back()->with('error', 'You cannot deactivate your own account');
        }

        // Prevent role change for self
        if ($request->has('role') && $admin->id === auth('admin')->id()) {
            return redirect()->back()->with('error', 'You cannot change your own role');
        }

        $admin->update($request->only(['name', 'email', 'role', 'is_active']));

        return redirect()->route('admin.admins.index')->with('success', 'Admin updated successfully');
    }

    /**
     * Remove the specified admin
     */
    public function destroy(Admin $admin)
    {
        // Prevent self-deletion
        if ($admin->id === auth('admin')->id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account');
        }

        // Prevent deletion of super admin
        if ($admin->isSuperAdmin()) {
            return redirect()->back()->with('error', 'Super admin cannot be deleted');
        }

        $admin->delete();

        return redirect()->route('admin.admins.index')->with('success', 'Admin deleted successfully');
    }

    /**
     * Toggle admin status
     */
    public function toggleStatus(Admin $admin)
    {
        // Prevent self-deactivation
        if ($admin->id === auth('admin')->id()) {
            return redirect()->back()->with('error', 'You cannot change your own status');
        }

        $admin->update([
            'is_active' => !$admin->is_active,
        ]);

        $status = $admin->is_active ? 'activated' : 'deactivated';

        return redirect()->back()->with('success', "Admin {$status} successfully");
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
        if ($admin->id === auth('admin')->id()) {
            return redirect()->back()->with('error', 'You cannot change your own role');
        }

        // Prevent changing super admin role
        if ($admin->isSuperAdmin()) {
            return redirect()->back()->with('error', 'Super admin role cannot be changed');
        }

        $admin->update([
            'role' => $request->role,
        ]);

        return redirect()->back()->with('success', 'Admin role updated successfully');
    }
}