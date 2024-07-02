<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class VisitorController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request) : JsonResponse
    {

        try {
            
            // $visitorIp = $request->ip();
            $visitorIp = "102.89.44.102";

           if(filter_var($visitorIp, FILTER_VALIDATE_IP)) {

                $visitorName = trim($request->visitor_name, "\"'");

                $ipinfoToken = config('services.ipinfo.token');
                $detectVisitor = Http::get("https://ipinfo.io/$visitorIp?token=$ipinfoToken");
                $lngAndLat = explode(',', $detectVisitor['loc']);
                $lat =  $lngAndLat[0];
                $lon =  $lngAndLat[1];

                $openWeather = config('services.openweather.token');
                $visitorWeather = Http::get("https://api.openweathermap.org/data/2.5/weather?lat=$lat&lon=$lon&appid=$openWeather");
        
                $visitorCity = $detectVisitor['region'];
                $temperature = $visitorWeather['main']['temp'] - 273;

                return response()->json([
                    "client_ip" => $visitorIp,
                    "location" => $visitorCity,
                    "greeting" => "Hello, {$visitorName}!, the temperature is {$temperature} degrees Celcius in {$visitorCity}",
                ], 200);

            }else {
                return response()->json([
                    "error" => "Unable to access your ip address!",
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                "error" => "An error occurred make a request again!",
            ], 500);
        }

    }
}
