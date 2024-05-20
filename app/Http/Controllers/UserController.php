<?php

namespace App\Http\Controllers;

use App\Models\PasswordResetToken;
use App\Models\Reservation;
use App\Models\ReservationForm;
use App\Models\ResetPassword;
use App\Models\Template;
use App\Models\User;
use App\Notifications\ResetPassword as ResetPasswordMail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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

        $u = User::where('email', $email);
        $user = $u->first();

        if ($request->password != "") {
            if ($user->password != null) {
                if (Hash::check($request->password, $user->password)) {
                    Log::info($request->password);
                    $status = 200;
                    $message = "Berhasil login";
    
                    $u->update([
                        'token' => Str::random(32),
                    ]);
                    $user = $u->first();
                } else {
                    $user = null;
                    $message = "Kombinasi email dan password tidak tepat";
                }
            } else {
                $user = null;
                $message = "Kamu harus login menggunakan Google";
            }
        } else {
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
        }

        return response()->json([
            'message' => $message,
            'user' => $user,
            'status' => $status
        ]);
    }
    public function register(Request $request) {
        $status = 405;
        $message = "Gagal membuat akun. Mohon coba kembali";
        $u = User::where('email', $request->email);
        $user = $u->first();

        if ($user == null) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'role' => "user",
                'requested_to_be_host' => false,
                'photo' => "https://host.pakkos.com/images/default_user.png"
            ]);
            $status = 200;
            $message = "Berhasil membuat akun";
        } else {
            $message = "Email telah digunakan. Mohon gunakan yang lainnya.";
        }

        return response()->json([
            'message' => $message,
            'user' => $user,
            'status' => $status
        ]);
    }
    public function forgetPassword(Request $request) {
        $u = User::where('email', $request->email);
        $user = $u->first();

        if ($user != null) {
            $token = Str::random(32);
            $saveData = ResetPassword::create([
                'user_id' => $user->id,
                'token' => $token,
                'has_used' => false,
            ]);

            $user->notify(new ResetPasswordMail([
                'token' => $token,
            ]));
        }

        return response()->json([
            ''
        ]);
    }
    public function resetPasswordToken($token) {
        $data = ResetPassword::where([
            ['token', $token],
            ['has_used', false]
        ])->first();

        return response()->json([
            'token' => $token,
            'data' => $data,
        ]);
    }
    public function resetPassword(Request $request) {
        $status = 403;
        $message = "Gagal mengubah password";

        $d = ResetPassword::where('token', $request->token);
        $data = $d->first();

        if ($data != null) {
            $minutesAgo = Carbon::parse($data->created_at)->diffInMinutes(
                Carbon::now()
            , false);
            
            if ($minutesAgo <= 85) {
                $message = "Berhasil mengubah password. Silahkan login menggunakan password baru";
                $status = 200;

                $u = User::where('id', $data->user_id);
                $u->update([
                    'password' => bcrypt($request->password)
                ]);

                $d->update(['has_used' => true]);
            }
        }

        return response()->json([
            'message' => $message,
            'status' => $status,
        ]);
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
        $record = $request->record;

        $reservation = Reservation::create([
            'user_id' => $request->user_id,
            'listing_id' => $request->listing_id,
        ]);

        foreach ($record as $type => $body) {
            $query = Template::where([
                ['type', $type],
                ['body', $body]
            ]);
            $temp = $query->first();
            
            $saveRecord = ReservationForm::create([
                'reservation_id' => $reservation->id,
                'template_id' => $temp->id,
                'answer' => "kosong"
            ]);

            $query->increment('count');
        }

        return response()->json([
            'message' => 'ok'
        ]);
    }
}
