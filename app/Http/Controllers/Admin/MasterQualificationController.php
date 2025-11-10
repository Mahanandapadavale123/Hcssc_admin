<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Models\Admin\MasterQualifications;
use Illuminate\Http\Request;

class MasterQualificationController extends Controller
{

    public function index()
    {
        $qualifications = MasterQualifications::all();
        return view('admin.qualification.index', compact('qualifications'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'mq_name' => 'required|string|max:255',
            'mq_code' => 'required|string|max:255|unique:master_qualifications,mq_code',
            'mq_sub_section' => 'required|string|max:255',
            'status' => 'in:active,inactive'
        ]);

        $qualification = MasterQualifications::create($validated);

        return response()->json(['message' => 'Qualification added successfully', 'data' => $qualification]);
    }

    // Show single qualification
    public function show($id)
    {
        $qualification = MasterQualifications::findOrFail($id);
        return response()->json($qualification);
    }


    public function update(Request $request, $id)
    {
        $qualification = MasterQualifications::findOrFail($id);

        $validated = $request->validate([
            'mq_name' => 'sometimes|string|max:255',
            'mq_code' => 'sometimes|string|max:255|unique:master_qualifications,mq_code,' . $qualification->id,
            'mq_sub_section' => 'sometimes|string|max:255',
            'status' => 'in:active,inactive'
        ]);

        $qualification->update($validated);

        return response()->json(['message' => 'Qualification updated successfully', 'data' => $qualification]);
    }
}
