<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Holiday;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    public function index()
    {
        $holidays = Holiday::orderBy('date', 'asc')->get();
        return view('admin.holidays.index', compact('holidays'));
    }

    public function create()
    {
        return view('admin.holidays.create');
    }
 public function store(Request $request)
{
    $request->validate([
        'title' => 'required',
        'date' => 'required|date|unique:holidays,date',
    ], [
        'date.unique' => 'A holiday already exists on this date!',
    ]);

    Holiday::create([
        'title' => $request->title,
        'date' => $request->date,
        'description' => $request->description,
        'status' => $request->status ?? 'active',
    ]);

    return response()->json(['message' => 'Holiday added successfully!']);
}
    public function update(Request $request, Holiday $holiday)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date|unique:holidays,date,' . $holiday->id,
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $holiday->update($validated);
        return redirect()->route('holidays.index')->with('success', 'Holiday updated successfully!');
    }

    public function destroy(Holiday $holiday)
    {
        $holiday->delete();
        return redirect()->route('holidays.index')->with('success', 'Holiday deleted successfully!');
    }
}


