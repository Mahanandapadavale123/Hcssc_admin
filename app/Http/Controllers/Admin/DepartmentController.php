<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Department;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class DepartmentController extends Controller
{

    public function index(Request $request)
    {
        $departments = Department::orderBy('id', 'asc')->get();
        return view('admin.department.index', compact('departments'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'dept_name' => 'required|string|max:255|unique:departments,dept_name',
            'status' => 'required|in:active,inactive',
        ]);

        $dept = Department::create([
            'dept_name' => $request->dept_name,
            'status' => $request->status,
        ]);

        return response()->json([
            'message' => 'Department created successfully',
            'department' => $dept
        ]);
    }


    public function show($id)
    {
        $role = Department::findOrFail($id);
        return response()->json($role);
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'dept_name' => 'required|string|max:255|unique:departments,dept_name,' . $id,
            'status' => 'required|in:active,inactive',
        ]);

        $dept = Department::findOrFail($id);
        $dept->update([
            'dept_name' => $request->dept_name,
            'status' => $request->status,
        ]);

        return response()->json([
            'message' => 'Role updated successfully',
            'department' => $dept
        ]);
    }

}
