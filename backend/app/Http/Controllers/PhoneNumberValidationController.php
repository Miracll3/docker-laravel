<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberType;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\NumberParseException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PhoneNumberValidationController extends Controller
{
    public function validatePhoneNumbers(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1',
            'country_code' => 'required|string|size:2', // Expecting ISO 3166-1 alpha-2 like 'US'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $quantity = $request->input('quantity');
        $countryCode = strtoupper($request->input('country_code'));
        $results = [];
        $validCount = 0;

        $phoneUtil = PhoneNumberUtil::getInstance();

        for ($i = 0; $i < $quantity; $i++) {
            // Generate a random 9-digit number
            $exampleNumber = $phoneUtil->getExampleNumberForType($countryCode, PhoneNumberType::MOBILE);
            if ($exampleNumber) {
                $baseNationalNumber = $exampleNumber->getNationalNumber();
                $baseString = substr((string) $baseNationalNumber, 0, -3);
                $randomSuffix = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
                $nationalNumber = (int) ($baseString . $randomSuffix);
            } else {
                // fallback
                $nationalNumber = rand(100000000, 999999999);
            }

            try {
                $phoneNumber = $phoneUtil->parse($nationalNumber, $countryCode);
            } catch (NumberParseException $e) {
                $results[] = [
                    'phone_number' => $nationalNumber,
                    'country_code' => $countryCode,
                    'type' => 'Invalid',
                    'is_possible_number_length_match' => false,
                    'is_valid' => false,
                ];
                continue;
            }

            $isValid = $phoneUtil->isValidNumber($phoneNumber);
            $type = $phoneUtil->getNumberType($phoneNumber);
            $isPossibleLengthMatch = $phoneUtil->isPossibleNumber($phoneNumber);
            $phoneNumberString = $phoneUtil->format($phoneNumber, PhoneNumberFormat::E164);

            $typeString = match ($type) {
                PhoneNumberType::FIXED_LINE => 'FIXED_LINE',
                PhoneNumberType::MOBILE => 'MOBILE',
                PhoneNumberType::FIXED_LINE_OR_MOBILE => 'FIXED_LINE_OR_MOBILE',
                PhoneNumberType::TOLL_FREE => 'TOLL_FREE',
                PhoneNumberType::PREMIUM_RATE => 'PREMIUM_RATE',
                PhoneNumberType::SHARED_COST => 'SHARED_COST',
                PhoneNumberType::VOIP => 'VOIP',
                PhoneNumberType::PERSONAL_NUMBER => 'PERSONAL_NUMBER',
                PhoneNumberType::PAGER => 'PAGER',
                PhoneNumberType::UAN => 'UAN',
                PhoneNumberType::VOICEMAIL => 'VOICEMAIL',
                default => 'UNKNOWN',
            };

            try {
                DB::connection('mongodb')->table('phone_numbers')->insert([
                    'phone_number' => $phoneNumberString,
                    'country_code' => $countryCode,
                    'type' => $typeString,
                    'is_possible_number_length_match' => $isPossibleLengthMatch,
                    'is_valid' => $isValid,
                ]);
            } catch (\Exception $e) {
                Log::error('Database error: ' . $e->getMessage());
                return response()->json(['error' => 'Failed to save to database: ' . $e->getMessage()], 500); // Return a 500 error
            }

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
