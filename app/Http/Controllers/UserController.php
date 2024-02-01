<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function auth(Request $request) {
        $user = User::where('id', $request->user_id)->first();
        return response()->json([
            'user' => $user,
        ]);
    }
    public function login(Request $request) {
        $email = $request->email;
        $at = $request->at;
        $status = 405;
        $message = "Gagal login.";

        $user = User::where('email', $email)->first();

        if ($user == null) {
            $user = User::create([
                'name' => $request->name,
                'photo' => $request->photo,
                'email' => $email,
                'at' => $at,
                'role' => "user",
                'requested_to_be_host' => false,
            ]);
        } 

        $status = 200;
        $message = "Berhasil login";

        return response()->json([
            'message' => $message,
            'user' => $user,
        ], $status);
    }
    public function requestToBeHost(Request $request) {
        $data = User::where('id', $request->user_id);
        $data->update([
            'requested_to_be_host' => true,
        ]);
        $user = $data->first();

        return response()->json([
            'message' => "ok",
            'user' => $user,
        ]);
    }

    public function makeReservation(Request $request) {
        // 
    }
}
