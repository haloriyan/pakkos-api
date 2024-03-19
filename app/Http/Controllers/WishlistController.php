<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function get(Request $request) {
        $wishlists = Wishlist::where('user_id', $request->user_id)->with(['listing'])->get();

        return response()->json([
            'wishlists' => $wishlists
        ]);
    }
    public function put(Request $request) {
        $data = Wishlist::where([
            ['user_id', $request->user_id],
            ['listing_id', $request->listing_id],
        ]);

        if ($data->get(['id'])->count() > 0) {
            $data->delete();
        } else {
            $saveData = Wishlist::create([
                'user_id' => $request->user_id,
                'listing_id' => $request->listing_id,
            ]);
        }

        return response()->json([
            'message' => "ok"
        ]);
    }
}
