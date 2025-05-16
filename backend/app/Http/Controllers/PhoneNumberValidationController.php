<?php

namespace App\Http\Controllers;

use App\Models\PhoneNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

class PhoneNumberValidationController extends Controller
{
    public function validatePhoneNumbers(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1',
            'country_code' => 'required|string|min:1|max:3',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $quantity = $request->input('quantity');
        $countryCode = $request->input('country_code');
        $results = [];
        $validCount = 0;

        $phoneUtil = PhoneNumberUtil::getInstance();

        for ($i = 0; $i < $quantity; $i++) {
            // Generate a random phone number (basic example -  use a library or more robust method in real application)
            $nationalNumber = rand(100000000, 999999999); // Example: 9-digit number
            try {
                $phoneNumber =$phoneUtil->parse($nationalNumber, $countryCode);
            } catch (\libphonenumber\NumberParseException $e) {
                // Handle phone number parsing errors
                $results[] = [
                    'phone_number' => $nationalNumber,
                    'country_code' => $countryCode,
                    'type' => 'Invalid',
                    'is_possible_number_length_match' => false,
                    'is_valid' => false,
                ];
                continue; // Skip to the next iteration
            }

            $isValid = $phoneUtil->isValidNumber($phoneNumber);
            $type = $phoneUtil->getNumberType($phoneNumber);
            $isPossibleLengthMatch = $phoneUtil->isPossibleNumber($phoneNumber);

            $phoneNumberString = $phoneUtil->format($phoneNumber, PhoneNumberFormat::E164); // Get E.164 format

             // Map the phone number type to a string
            $typeString = 'UNKNOWN';
            switch ($type) {
                case \libphonenumber\PhoneNumberType::FIXED_LINE:
                    $typeString = 'FIXED_LINE';
                    break;
                case \libphonenumber\PhoneNumberType::MOBILE:
                    $typeString = 'MOBILE';
                    break;
                case \libphonenumber\PhoneNumberType::FIXED_LINE_OR_MOBILE:
                    $typeString = 'FIXED_LINE_OR_MOBILE';
                    break;
                case \libphonenumber\PhoneNumberType::TOLL_FREE:
                    $typeString = 'TOLL_FREE';
                    break;
                case \libphonenumber\PhoneNumberType::PREMIUM_RATE:
                    $typeString = 'PREMIUM_RATE';
                    break;
                case \libphonenumber\PhoneNumberType::SHARED_COST:
                    $typeString = 'SHARED_COST';
                    break;
                case \libphonenumber\PhoneNumberType::VOIP:
                    $typeString = 'VOIP';
                    break;
                case \libphonenumber\PhoneNumberType::PERSONAL_NUMBER:
                    $typeString = 'PERSONAL_NUMBER';
                    break;
                case \libphonenumber\PhoneNumberType::PAGER:
                    $typeString = 'PAGER';
                    break;
                case \libphonenumber\PhoneNumberType::UAN:
                    $typeString = 'UAN';
                    break;
                case \libphonenumber\PhoneNumberType::VOICEMAIL:
                    $typeString = 'VOICEMAIL';
                    break;
            }
            // Store the phone number and validation results in MongoDB
            $phoneNumberModel = new PhoneNumber();
            $phoneNumberModel->phone_number = $phoneNumberString;
            $phoneNumberModel->country_code = $countryCode;
            $phoneNumberModel->type = $typeString;
            $phoneNumberModel->is_possible_number_length_match = $isPossibleLengthMatch;
            $phoneNumberModel->is_valid = $isValid;
            $phoneNumberModel->save();

            $results[] = [
                'phone_number' => $phoneNumberString,
                'country_code' => $countryCode,
                'type' => $typeString,
                'is_possible_number_length_match' => $isPossibleLengthMatch,
                'is_valid' => $isValid,
            ];

            if ($isValid) {
                $validCount++;
            }
        }

        $validPercentage = ($quantity > 0) ? ($validCount / $quantity) * 100 : 0;

        return response()->json([
            'quantity' => $quantity,
            'country_code' => $countryCode,
            'valid_count' => $validCount,
            'valid_percentage' => round($validPercentage, 2),
            'results' => $results,
        ]);
    }
}
