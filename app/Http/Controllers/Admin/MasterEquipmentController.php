<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Models\Admin\MasterEquipment;
use Illuminate\Http\Request;

class MasterEquipmentController extends Controller
{
    // Show all equipment
    public function index()
    {
        $equipments = MasterEquipment::all();
        return  view('admin.equipment.index', compact('equipments'));
    }

    // Store new equipment
    public function store(Request $request)
    {
        $validated = $request->validate([
            'qual_code' => 'required|string|max:255',
            'equipmentName' => 'required|string|max:255',
            'quantityRequired' => 'required|string',
            'status' => 'in:active,inactive'
        ]);

        $equipment = MasterEquipment::create($validated);

        return response()->json(['message' => 'Equipment added successfully', 'data' => $equipment]);
    }

    // Show single equipment
    public function show($id)
    {
        $equipment = MasterEquipment::findOrFail($id);
        return response()->json($equipment);
    }

    // Update equipment
    public function update(Request $request, $id)
    {
        $equipment = MasterEquipment::findOrFail($id);

        $validated = $request->validate([
            'qual_code' => 'sometimes|string|max:255',
            'equipmentName' => 'sometimes|string|max:255',
            'quantityRequired' => 'sometimes|string',
            'status' => 'in:active,inactive'
        ]);

        $equipment->update($validated);

        return response()->json(['message' => 'Equipment updated successfully', 'data' => $equipment]);
    }
}
