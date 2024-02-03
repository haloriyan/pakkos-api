<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PageController extends Controller
{
    public function home(Request $request) {
        $q = $request->q;
        $facilities = explode(",", base64_decode($request->facilities));
        $query = Listing::where('is_approved', true);
        if ($q != "null") {
            $query = $query->where('name', 'LIKE', '%'.$q.'%')
            ->orWhere('subdistrict', 'LIKE', '%'.$q.'%')
            ->orWhere('city', 'LIKE', '%'.$q.'%')
            ->orWhere('province', 'LIKE', '%'.$q.'%');
        }
        if ($facilities[0] != "") {
            foreach ($facilities as $facilityID) {
                $query = $query->whereHas('facilities_raw', function ($q) use ($facilityID) {
                    $q->where('facility_id', $facilityID);
                });
            }
        }
        $listings = $query->paginate(12);
        $featured_facilities = Facility::where('is_featured', true)->get();

        return response()->json([
            'listings' => $listings,
            'featured_facilities' => $featured_facilities,
        ]);
    }
    public function submitPertanyaan(Request $request) {
        // 
    }
}
