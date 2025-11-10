<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Admin\LeaveType;
use Illuminate\Http\Request;

class LeaveTypeController extends Controller
{

    public function index()
    {
        $leaveTypes = LeaveType::orderBy('id', 'desc')->get();
        return view('admin.settings.leaves-types', compact('leaveTypes'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:leave_types,name',
            'total_days' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive',
        ]);

        LeaveType::create($request->only('name', 'total_days', 'status'));

        return redirect()->route('leave-type.index')->with('success', 'Leave type added successfully!');
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:leave_types,name,' . $id,
            'total_days' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive',
        ]);

        $leaveType = LeaveType::findOrFail($id);

        $leaveType->update([
            'dept_name' => $request->name,
            'status' => $request->status,
        ]);
        return response()->json([
            'message' => 'Leaves Type updated successfully',
            'leaveType' => $leaveType
        ]);
    }

    public function destroy(LeaveType $leaveType)
    {
        $leaveType->delete();
        return redirect()->back()->with('success', 'Leave type deleted successfully!');
    }
}


