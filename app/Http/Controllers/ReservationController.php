<?php

namespace App\Http\Controllers;

use App\Models\ReservationInvoice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ReservationController extends Controller
{
    public function mine(Request $request) {
        $user = User::where('token', $request->token)->first();
        $rsvps = ReservationInvoice::where('user_id', $user->id)->with(['listing'])->orderBy('updated_at', 'DESC')->get();

        return response()->json([
            'transactions' => $rsvps,
        ]);
    }
    public function showAddress(Request $request) {
        $cond = $request->inv_code == "" ? [
            ['user_id', $request->user_id],
            ['listing_id', $request->listing_id],
        ] : [['payment_code', $request->inv_code]];
        
        $inv = ReservationInvoice::where($cond);
        $invoice = $inv->with(['listing.user', 'listing.facilities'])->first();

        if ($invoice == "") {
            $invoice = ReservationInvoice::create([
                'user_id' => $request->user_id,
                'listing_id' => $request->listing_id,
                'payment_code' => Str::random(12),
                'payment_method' => null,
                'payment_amount' =>  0,
                'payment_status' => null,
                'payment_payloads' => null,
            ]);
        } else {
            // Check payment
            if ($invoice->payment_payloads != null) {
                $serverKey = env('MIDTRANS_SERVER_KEY_' . env('MIDTRANS_MODE'));
                $payloads = json_decode($invoice->payment_payloads, false);
                
                if ($payloads->status_code[0] == "2") {
                    $response = Http::withBasicAuth($serverKey, '')->get('https://api.sandbox.midtrans.com/v2/'.$payloads->order_id.'/status')->body();
                    $response = json_decode($response, false);

                    $inv->update([
                        'payment_status' => strtoupper($response->transaction_status),
                        'payment_payloads' => json_encode($response),
                    ]);
                } else {
                    // 
                }
            }

            // Get facilities
            $invoice->listing->facilities_display = ListingController::getFacilities($invoice->listing);
        }

        return response()->json([
            'invoice' => $invoice,
        ]);
    }
    public function pay(Request $request) {
        $serverKey = env('MIDTRANS_SERVER_KEY_' . env('MIDTRANS_MODE'));
        $inv = ReservationInvoice::where('payment_code', $request->inv_code);
        $invoice = $inv->first();
        $status = 200;
        $message = "Berhasil memproses pembayaran";

        $amount = strtolower($request->paket['name']) == "single" ? 10000 : 25000;
        
        $payloads = [
            'payment_type' => $request->method,
            'transaction_details' => [
                'order_id' => $invoice->payment_code,
                'gross_amount' => $amount,
            ],
        ];

        if ($request->method == "bank_transfer") {
            $payloads['bank_transfer'] = [
                'bank' => $request->channel,
            ];
        } else if ($request->method == "card") {
            // 
        } else if ($request->method == "gopay") {
            $payloads['gopay'] = [
                'enable_callback' => false,
            ];
        }

        $response = Http::withBasicAuth($serverKey, '')->post('https://api.sandbox.midtrans.com/v2/charge', $payloads)->body();
        $response = json_decode($response, false);

        if ($response->status_code[0] == "2") {
            // Update payment detail if successfully charged
            $inv->update([
                'payment_payloads' => json_encode($response),
                'payment_status' => "PENDING",
                'payment_type' => $request->method,
                'payment_amount' => $amount,
            ]);
        } else {
            $status = 406;
            $message = "Gagal memproses pembayaran. Mohon gunakan metode pembayaran lainnya";
            Log::info(json_encode($response));
        }

        return response()->json([
            'res' => $response,
            'method' => $request->method,
            'status' => $status,
            'message' => $message,
        ]);
    }
    public function refreshPayment(Request $request) {
        $inv = ReservationInvoice::where('payment_code', $request->inv_code);
        $invoice = $inv->first();

        $inv->update([
            'payment_status' => null,
            'payment_payloads' => null,
            'payment_type' => null,
        ]);
    }
}
