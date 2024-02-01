<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home() {
        $listings = Listing::where('is_approved', true)->get();

        return response()->json([
            'listings' => $listings
        ]);
    }
    public function submitPertanyaan(Request $request) {
        // 
    }
}
