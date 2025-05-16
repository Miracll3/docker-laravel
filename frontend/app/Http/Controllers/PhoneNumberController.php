<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class PhoneNumberController extends Controller
{
    public function index()
    {
        return view('phone.index');
    }

    /**
     * Generate and validate phone numbers via the microservice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateAndValidate(Request $request)
    {
        // Validate the request data
        // $validator = Validator::make($request->all(), [
        //     'quantity' => 'required|integer|min:1',
        //     // 'country_code' => 'required|string|min:1|max:3', // Basic validation, the microservice will handle more
        // ]);

        // if ($validator->fails()) {
        //     return response()->json(['errors' => $validator->errors()], 422);
        // }

        $quantity = $request->input('quantity');
        $countryCode = $request->input('country_code');

        // Make a request to the microservice (Section 3)
        $microserviceResponse = Http::post('http://localhost:9000/api/validate-phone-numbers', [
            'quantity' => $quantity,
            'country_code' => $countryCode,
        ]);

        // Handle microservice errors
        if ($microserviceResponse->failed()) {
            $errorMessage = $microserviceResponse->status() === 422
                ? 'The microservice rejected the data: ' . json_encode($microserviceResponse->json()['errors'])
                : 'Failed to communicate with the microservice: ' . $microserviceResponse->status() . ' - ' . $microserviceResponse->body();

            return response()->json(['error' => $errorMessage], 500); // Use 500 for server errors.
        }
        // Return the response from the microservice
        return response()->json($microserviceResponse->json());
    }
}
