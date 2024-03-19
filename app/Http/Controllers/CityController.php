<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function get($id = NULL) {
        if ($id == NULL) {
            $res['cities'] = City::orderBy('name', 'ASC')->orderBy('created_at', 'DESC')->get();
        } else {
            $res['city'] = City::where('id', $id)->first();
        }

        return response()->json($res);
    }
    public function create(Request $request) {
        $saveData = City::create([
            'name' => $request->name,
            'priority' => 0,
            'is_default' => false,
        ]);

        return response()->json([
            'message' => "ok"
        ]);
    }
    public function delete(Request $request) {
        $data = City::where('id', $request->city_id);
        $delete = $data->delete();
        
        return response()->json([
            'message' => "ok"
        ]);
    }
    public function update(Request $request) {
        $data = City::where('id', $request->city_id);
        $updateData = $data->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'message' => "ok"
        ]);
    }
    public function priority(Request $request) {
        $data = City::where('id', $request->city_id);
        if ($request->action == "increase") {
            $data->increment('priority');
        } else {
            $data->decrement('priority');
        }

        return response()->json([
            'message' => "ok"
        ]);
    }
    public function default(Request $request) {
        $data = City::where('id', $request->city_id);
        $city = $data->first();

        $data->update(['is_default' => !$city->is_default]);
        
        return response()->json([
            'message' => "ok"
        ]);
    }
}
