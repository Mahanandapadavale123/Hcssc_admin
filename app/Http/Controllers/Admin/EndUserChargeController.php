<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Models\Admin\EndUserCharges;
use Illuminate\Http\Request;

class EndUserChargeController extends Controller
{

    public function index()
    {
        $charges = EndUserCharges::all();
        return view('admin.usercharges.index', compact('charges'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_type' => 'nullable|string|max:255',
            'payment_type' => 'required|in:initial_payment,final_payment',
            'category' => 'nullable|string|max:255',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'status' => 'in:active,inactive',
        ]);

        $charge = EndUserCharges::create($validated);

        return response()->json(['message' => 'Charge added successfully', 'data' => $charge]);
    }

       public function show($id)
    {
        $charge = EndUserCharges::findOrFail($id);
        return response()->json($charge);
    }


    public function update(Request $request, $id)
    {
        $charge = EndUserCharges::findOrFail($id);

        $validated = $request->validate([
            'user_type' => 'nullable|string|max:255',
            'payment_type' => 'sometimes|in:initial_payment,final_payment',
            'category' => 'nullable|string|max:255',
            'description' => 'sometimes|string|max:255',
            'amount' => 'sometimes|numeric|min:0',
            'status' => 'in:active,inactive',
        ]);

        $charge->update($validated);

        return response()->json(['message' => 'Charge updated successfully', 'data' => $charge]);
    }


    public function destroy($id)
    {
        $charge = EndUserCharges::findOrFail($id);
        $charge->delete();

        return response()->json(['message' => 'Charge deleted successfully']);
    }
}
