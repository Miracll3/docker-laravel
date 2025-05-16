<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phone Number Generator</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@tailwindcss/browser@latest"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h1 class="text-2xl font-semibold mb-6 text-center text-gray-800">Generate Phone Numbers</h1>
        <form id="phone-number-form" class="space-y-4" method="POST"
            action="{{ route('phone.generateAndValidate') }}">
            <div>
                <label for="quantity" class="block text-gray-700 text-sm font-bold mb-2">Quantity:</label>
                <input type="number" id="quantity" name="quantity"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    required min="1" value="{{ old('quantity') }}">
            </div>
            <div>
                <label for="country_code" class="block text-gray-700 text-sm font-bold mb-2">Country Code:</label>
                <select id="country_code" name="country_code"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    required>
                    <option value="" disabled selected>Select a country</option>
                    @foreach ($countries as $code => $name)
                        <option value="{{ $code }}" {{ old('country_code') == $code ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full">
                Generate and Validate</button>
            @csrf
        </form>
        <div id="results" class="mt-6 text-gray-700">
            @if (isset($errors) && count($errors) > 0)
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Errors:</strong>
                    <span class="block sm:inline">{{ $errors->first() }}</span>
                </div>
            @endif

            @if (isset($error))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Error:</strong>
                    <span class="block sm:inline">{{ $error }}</span>
                </div>
            @endif

            @if (isset($data))
                <h2 class="text-lg font-semibold mb-2 text-green-600">Validation Results</h2>
                <p>Out of {{ $data['quantity'] }} numbers generated, {{ $data['valid_count'] }} were found
                    to be valid for the country, which calculates to
                    {{ round($data['valid_percentage'], 2) }}% valid results.</p>
                @foreach ($data['results'] as $result)
                    <ul class="list-disc list-inside mt-2">
                        <li>
                            Number: {{ $result['phone_number'] }},
                            Country Code: {{ $result['country_code'] }},
                            Type: {{ $result['type'] }},
                            Length Match: {{ $result['is_possible_number_length_match'] ? 'true' : 'false' }},
                            Validity:
                            @if ($result['is_valid'])
                                <span class="text-green-500">Valid</span>
                            @else
                                <span class="text-red-500">Invalid</span>
                            @endif
                        </li>
                    </ul>
                @endforeach
            @endif
        </div>
    </div>
</body>
</html>
