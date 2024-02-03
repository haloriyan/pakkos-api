<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FacilityController extends Controller
{
    public function getFeatured() {
        $facilities = Facility::where('is_featured', true)->get();

        return response()->json([
            'facilities' => $facilities,
        ]);
    }
    public function get() {
        $facilitiesRaw = Facility::orderBy('type', 'ASC')->get();
        $facilities = [];
        $types = [];

        foreach ($facilitiesRaw as $fac) {
            $facilities[$fac->type][] = $fac;
            if (!in_array($fac->type, $types)) {
                array_push($types, $fac->type);
            }
        }

        return response()->json([
            'facilities' => $facilities,
            'types' => $types,
        ]);
    }
    public function create(Request $request) {
        $icon = $request->file('icon');
        $iconFileName = $icon->getClientOriginalName();

        $saveData = Facility::create([
            'name' => $request->name,
            'type' => $request->type,
            'icon' => $iconFileName,
            'is_featured' => false,
        ]);

        $icon->storeAs('public/facility_icons', $iconFileName);

        return response()->json([
            'message' => "ok"
        ]);
    }
    public function delete(Request $request) {
        $data = Facility::where('id', $request->facility_id);
        $facility = $data->first();

        $deleteData = $data->delete();
        if ($facility->icon != null) {
            Storage::delete('public/facility_icons/' . $facility->icon);
        }

        return response()->json([
            'message' => "ok"
        ]);
    }
    public function featured(Request $request) {
        $data = Facility::where('id', $request->facility_id);
        $facility = $data->first();

        $data->update([
            'is_featured' => !$facility->is_featured,
        ]);

        return response()->json([
            'message' => "ok"
        ]);
    }
}
