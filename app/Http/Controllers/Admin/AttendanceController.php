<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Attendance;
use App\Models\Admin\Holiday;
use Carbon\Carbon;
use Auth;
use Carbon\CarbonPeriod;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $userId = $user->id;
        $today = now()->toDateString();


        if (request()->has('month')) {
            $selectedMonth = request('month'); // example: "2025-10"
            [$year, $month] = explode('-', $selectedMonth);
        } else {
            $year = now()->year;
            $month = now()->month;
        }

        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth()->toDateString();

        if ($year == now()->year && $month == now()->month) {
            $endDate = now()->toDateString();
        } else {
            $endDate = Carbon::create($year, $month, 1)->endOfMonth()->toDateString(); // full month
        }


        // Get attendances for this month
        $attendances = Attendance::where('user_id', $userId)
            ->whereBetween('date', [$startOfMonth, $endDate])
            ->get()
            ->keyBy(function ($item) {
                return Carbon::parse($item->date)->format('Y-m-d');
            });

        // Get holidays (manual holidays)
        $holidays = Holiday::whereBetween('date', [$startOfMonth, $endDate])
            ->get()
            ->keyBy(function ($item) {
                return Carbon::parse($item->date)->format('Y-m-d');
            });

        $period = CarbonPeriod::create($startOfMonth, $endDate);
        $data = [];

       foreach ($period as $date) {
    $dateStr = $date->format('Y-m-d');

    // Sunday = holiday
    if ($date->isSunday()) {
        $data[] = [
            'date' => $dateStr,
            'status' => 'Weekly Off (Sunday)',
            'check_in' => null,
            'check_out' => null,
            'break' => null,
            'working_hours' => null,
        ];
        continue;
    }


            // Manual holiday
            if ($holidays->has($dateStr)) {
                $holiday = $holidays[$dateStr];
                $data[] = [
                    'date' => $dateStr,
                    'status' => 'Holiday - ' . ($holiday->name ?? 'Holiday'),
                    'check_in' => null,
                    'check_out' => null,
                    'break' => null,
                    'working_hours' => null,
                ];
                continue;
            }

            // Attendance exists
            if ($attendances->has($dateStr)) {
                $att = $attendances[$dateStr];
                if ($att->check_in && !$att->check_out) {
                    $status = 'Checkout Missed';
                } elseif ($att->check_in && $att->check_out) {
                    $status = 'Present';
                } else {
                    $status = 'Absent';
                }



                $data[] = [
                    'date' => $dateStr,
                    'status' => $status,
                    'check_in' => $att->check_in,
                    'check_out' => $att->check_out,
                    'break' => $att->break,
                    'working_hours' => $att->working_hours,
                ];
            } else {
                // No attendance record at all → Absent
                $data[] = [
                    'date' => $dateStr,
                    'status' => 'Absent',
                    'check_in' => null,
                    'check_out' => null,
                    'break' => null,
                    'working_hours' => null,
                ];
            }
        }

        // Today’s attendance (if exists)
        $todayAttendance = $attendances[$today] ?? null;

        // Totals
        $totalHoursToday = $todayAttendance->working_hours ?? 0;
        $totalHoursWeek = Attendance::where('user_id', $userId)
            ->whereBetween('date', [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()])
            ->sum('working_hours');
        $totalHoursMonth = Attendance::where('user_id', $userId)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('working_hours');




        return view('admin.attendance.index', compact(
            'data',
            'todayAttendance',
            'totalHoursToday',
            'totalHoursWeek',
            'totalHoursMonth'
        ));
    }


    public function checkIn()
    {
        $today = Carbon::today();
        $userId = Auth::id();

        $yesterdayRecord = Attendance::where('user_id', $userId)
            ->whereDate('date', Carbon::yesterday())
            ->whereNull('check_out')
            ->first();

        if ($yesterdayRecord) {
            $yesterdayRecord->update([
                'status' => 'Checkout Missed',
                'check_out' => null,
                'working_hours' => null,
            ]);
        }

        $exists = Attendance::where('user_id', $userId)
            ->whereDate('date', $today)
            ->first();

        if ($exists) {
            return redirect()->back()->with('error', 'You already checked in today.');
        }

        Attendance::create([
            'user_id' => $userId,
            'date' => $today,
            'check_in' => Carbon::now(),
            'status' => 'Present',
        ]);


        return redirect()->back()->with('success', 'Checked In successfully.');
    }

    public function checkOut()
    {
        $today = Carbon::today();
        $userId = Auth::id();

        $attendance = Attendance::where('user_id', $userId)
            ->whereDate('date', $today)
            ->first();

        if (!$attendance) {
            return redirect()->back()->with('error', 'You haven’t checked in yet.');
        }

        if ($attendance->check_out) {
            return redirect()->back()->with('error', 'You already checked out today.');
        }

        // Parse times
        $checkIn = Carbon::parse($attendance->check_in);
        $checkOut = Carbon::now();

        $workingHours = round($checkOut->diffInMinutes($checkIn) / 60, 2);


        // Calculate only the difference between check-in and check-out
        if ($checkOut->lessThan($checkIn)) {
            return redirect()->back()->with('error', 'Invalid checkout time.');
        }

        $workingMinutes = $checkOut->diffInMinutes($checkIn);
        $workingHours = round($workingMinutes / 60, 2); // example: 4.50 hrs

        $attendance->update([
            'check_out' => $checkOut,
            'working_hours' => round($checkOut->diffInMinutes($checkIn) / 60, 2),
            'status' => 'Present',
        ]);

        // dd($attendance->check_in, now());


        return redirect()->back()->with('success', 'Checked Out successfully.');
    }

}


