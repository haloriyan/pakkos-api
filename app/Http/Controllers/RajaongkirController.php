<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RajaongkirController extends Controller
{
    public $instance;

    public function __construct()
    {
        $this->instance = Http::withHeaders([
            'key' => env('RAJAONGKIR_KEY')
        ]);
    }

    public function province() {
        $response = $this->instance->get('https://pro.rajaongkir.com/api/province')->body();
        $response = json_decode($response, FALSE);
        
        return response()->json($response->rajaongkir->results);
    }
    public function city($provinceID) {
        $response = $this->instance->get('https://pro.rajaongkir.com/api/city?province='.$provinceID)->body();
        $response = json_decode($response, FALSE);
        
        return response()->json($response->rajaongkir->results);
    }
    public function district($provinceID, $cityID) {
        $response = $this->instance->get('https://pro.rajaongkir.com/api/subdistrict?city='.$cityID)->body();
        $response = json_decode($response, FALSE);
        
        return response()->json($response->rajaongkir->results);
    }
}
