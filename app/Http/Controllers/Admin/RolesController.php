<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{

    public function index(Request $request)
    {
        $roles = Role::where('group_type', 'admin')
            // ->where('name', '!=', 'Admin')
            // ->where('name', '!=', 'Super Admin')
            ->orderBy('id', 'asc')->paginate(10);
        return view('admin.roles.index', compact('roles'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'status' => 'required|in:active,inactive',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'group_type' => 'admin',
            'status' => $request->status,
        ]);

        return response()->json([
            'message' => 'Role created successfully',
            'role' => $role
        ]);
    }


    public function show($id)
    {
        $role = Role::findOrFail($id);
        return response()->json($role);
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $id,
            'status' => 'required|in:active,inactive',
        ]);

        $role = Role::findOrFail($id);
        $role->update([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return response()->json([
            'message' => 'Role updated successfully',
            'role' => $role
        ]);
    }


    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        if ($role->users()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete role. Users are assigned to this role.'
            ], 400);
        }
        $role->delete();

        return response()->json([
            'message' => 'Role deleted successfully'
        ]);
    }


    public function getPermissions(Request $request)
    {
        $roleId = $request->role;
        if (!$roleId) {
            return redirect()->back()->with('error', 'Role ID is required.');
        }

        $role = Role::find($roleId);
        if (!$role) {
            return redirect()->back()->with('error', 'Role not found.');
        }

        $permissions = Permission::get()->groupBy('group_name');
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('admin.roles.permissions', compact('role', 'permissions', 'rolePermissions'));

    }


    public function updatePermissions(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        $permissionIds = $request->input('permissions', []);

        $permissions = Permission::whereIn('id', $permissionIds)->get();

        if ($permissions->isEmpty()) {
            return back()->with('error', 'No valid permissions found for this role/guard.');
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $role->syncPermissions($permissions);

        return back()->with('success', 'Permissions updated successfully!');
    }


}
