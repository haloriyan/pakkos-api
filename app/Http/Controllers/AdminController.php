<?php

namespace App\Http\Controllers;

use Str;
use App\Models\Admin;
use App\Models\Listing;
use App\Models\Reservation;
use App\Models\Template;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function login(Request $request) {
        $message = "Kombinasi email dan password tidak tepat";
        $data = Admin::where('email', $request->email);
        $user = $data->first();
        $status = 401;

        if ($user != null) {
            if (Hash::check($request->password, $user->password)) {
                $token = Str::random(32);
                $data->update([
                    'token' => $token
                ]);
                $user = $data->first();
                $message = "Berhasil login.";
                $status = 200;
            }
        } else {
            $message = "Kami tidak dapat menemukan akun Anda";
        }

        return response()->json([
            'message' => $message,
            'user' => $user,
            'status' => $status,
        ]);
    }
    public function dashboard() {
        $user_count = User::all(['id'])->count();
        $listing_count = Listing::all(['id'])->count();
        $reservation_count = Reservation::all(['id'])->count();

        $templatesRaw = Template::orderBy('type', 'DESC')->get();
        $templates = [];

        foreach ($templatesRaw as $temp) {
            $templates[$temp->type][] = $temp;
        }

        $listings = Listing::orderBy('created_at', 'DESC')->with(['user'])->take(5)->get();
        $users = User::orderBy('created_at', 'DESC')->take(5)->get();

        return response()->json([
            'user_count' => $user_count,
            'listing_count' => $listing_count,
            'reservation_count' => $reservation_count,
            'templates' => $templates,
            'listings' => $listings,
            'users' => $users,
        ]);
    }
    public function user(Request $request) {
        $filter = [];
        if ($request->q != "") {
            array_push($filter, ['name', 'LIKE', '%'.$request->q.'%']);
        }
        $users = User::where($filter)->orderBy('created_at', 'DESC')->paginate(25);

        return response()->json([
            'users' => $users,
        ]);
    }
    public function userAction($action, Request $request) {
        $data = User::where('id', $request->user_id);

        if ($action == "make_host") {
            $data->update(['role' => 'host']);
        }
        if ($action == "make_common_user") {
            $data->update(['role' => 'user']);
        }

        return response()->json(['message' => "ok"]);
    }
    public function reservation() {
        $reservations = Reservation::orderBy('created_at', 'DESC')->with(['listing', 'user', 'forms.template'])->paginate(25);

        return response()->json([
            'reservations' => $reservations,
        ]);
    }

    public function admin() {
        $admins = Admin::all();

        return response()->json([
            'admins' => $admins,
        ]);
    }
    public function update(Request $request) {
        $toUpdate = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->password != "") {
            $toUpdate['password'] = bcrypt($request->password);
        }

        $updateData = Admin::where('id', $request->admin_id)->update($toUpdate);
        
        return response()->json(['message' => "ok"]);
    }
    public function delete(Request $request) {
        $delete = Admin::where('id', $request->admin_id)->delete();
        return response()->json(['message' => "ok"]);
    }
    public function store(Request $request) {
        $saveData = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return response()->json(['message' => "ok"]);
    }
}
