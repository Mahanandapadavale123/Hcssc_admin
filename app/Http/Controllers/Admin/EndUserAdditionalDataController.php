<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Models\EndUserAdditionalData;
use Illuminate\Http\Request;

class EndUserAdditionalDataController extends Controller
{
    // Show all records
    public function index()
    {
        $records = EndUserAdditionalData::all();
        return view('admin.additionaldata.index',compact('records'));
    }

    // Store new record
    public function store(Request $request)
    {
        $validated = $request->validate([
            'section_code' => 'required|string',
            'section' => 'required|string',
            'data' => 'nullable|string',
        ]);

        $record = EndUserAdditionalData::create($validated);

        return response()->json(['message' => 'Data added successfully', 'data' => $record]);
    }

    // Show one record
    public function show($id)
    {
        $record = EndUserAdditionalData::findOrFail($id);
        return response()->json($record);
    }

    // Update record
    public function update(Request $request, $id)
    {
        $record = EndUserAdditionalData::findOrFail($id);

        $validated = $request->validate([
            'section_code' => 'sometimes|string',
            'section' => 'sometimes|string',
            'data' => 'nullable|string',
        ]);

        $record->update($validated);

        return response()->json(['message' => 'Data updated successfully', 'data' => $record]);
    }


}
