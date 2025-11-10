<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Leave;
use App\Models\Admin\LeaveType;
use App\Models\Admin\UserRemainingLeave;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LeaveController extends Controller
{
    //  SHOW LEAVES
    public function index()
    {
        // If admin — show all leaves
        if (auth()->user()->role === 'admin') {
            $leaves = Leave::with(['employee', 'approver'])
                ->orderBy('id', 'desc')
                ->get();
        } else {
            // If employee — show only their own leaves
            $leaves = Leave::with(['employee', 'approver'])
                ->where('employee_id', auth()->id())
                ->orderBy('id', 'desc')
                ->get();
        }

        $leaveTypes = LeaveType::where('status', 'active')->get();
        $remainingLeaves = UserRemainingLeave::where('user_id', auth()->id())
            ->pluck('remaining_days', 'leave_type_id');

        return view('admin.leaves.index', compact('leaves', 'leaveTypes', 'remainingLeaves'));
    }

    public function adminIndex()
    {
        $leaves = Leave::with(['employee', 'approver'])->orderBy('id', 'desc')->get();
        return view('admin.leaves.admin_index', compact('leaves'));
    }

    //  APPLY LEAVE (for Employee)
    public function store(Request $request)
    {
        $request->validate([
            'leave_type' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
        ]);

        $days = Carbon::parse($request->start_date)
            ->diffInDays(Carbon::parse($request->end_date)) + 1;

        $leaveType = LeaveType::findOrFail($request->leave_type);

        $remaining = UserRemainingLeave::firstOrCreate(
            [
                'user_id' => auth()->id(),
                'leave_type_id' => $request->leave_type,
            ],
            [
                'remaining_days' => $leaveType->total_days,
            ]
        );

        if ($remaining->remaining_days < $days) {
            return response()->json([
                'success' => false,
                'message' => "You have only {$remaining->remaining_days} {$leaveType->name} remaining!",
            ]);
        }

        $leave = new Leave();
        $leave->employee_id = auth()->id();
        $leave->leave_type = $request->leave_type;
        $leave->start_date = $request->start_date;
        $leave->end_date = $request->end_date;
        $leave->reason = $request->reason;
        $leave->status = 'pending';
        $leave->days = $days;
        $leave->save();

        return response()->json([
            'success' => true,
            'message' => 'Leave applied successfully! Waiting for approval.',
            'remaining' => $remaining->remaining_days,
        ]);
    }

    //  APPROVE LEAVE (for Admin)
    public function approve($id)
    {
        $leave = Leave::findOrFail($id);

        if ($leave->status === 'approved') {
            return back()->with('info', 'Leave already approved.');
        }

        $days = Carbon::parse($leave->start_date)
            ->diffInDays(Carbon::parse($leave->end_date)) + 1;

        $leaveType = LeaveType::find($leave->leave_type);

        $remaining = UserRemainingLeave::firstOrCreate(
            [
                'user_id' => $leave->employee_id,
                'leave_type_id' => $leave->leave_type,
            ],
            [
                'remaining_days' => $leaveType->total_days,
            ]
        );

        if ($remaining->remaining_days < $days) {
            return back()->with('error', 'Not enough remaining leaves!');
        }

        $remaining->remaining_days -= $days;
        $remaining->save();

        $leave->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'days' => $days,
        ]);

        return back()->with('success', 'Leave approved successfully.');
    }

    //  REJECT LEAVE (for Admin)
    public function reject($id)
    {
        $leave = Leave::findOrFail($id);

        if ($leave->status === 'approved') {
            $remaining = UserRemainingLeave::where('user_id', $leave->employee_id)
                ->where('leave_type_id', $leave->leave_type)
                ->first();

            if ($remaining) {
                $remaining->remaining_days += $leave->days;
                $remaining->save();
            }
        }

        $leave->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
        ]);

        return back()->with('error', 'Leave rejected.');
    }

    //  DELETE LEAVE (Admin or Employee)
    public function destroy($id)
    {
        $leave = Leave::findOrFail($id);

        if ($leave->status === 'approved') {
            $remaining = UserRemainingLeave::where('user_id', $leave->employee_id)
                ->where('leave_type_id', $leave->leave_type)
                ->first();

            if ($remaining) {
                $remaining->remaining_days += $leave->days;
                $remaining->save();
            }
        }

        $leave->delete();

        return redirect()->route('leaves.index')->with('success', 'Leave request deleted successfully.');
    }
}
