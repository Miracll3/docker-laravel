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
        <form id="phone-number-form" class="space-y-4">
            <div>
                <label for="quantity" class="block text-gray-700 text-sm font-bold mb-2">Quantity:</label>
                <input type="number" id="quantity" name="quantity" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required min="1">
            </div>
            <div>
                <label for="country_code" class="block text-gray-700 text-sm font-bold mb-2">Country Code:</label>
                <input type="text" id="country_code" name="country_code" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                <p id="country_code_help" class="text-gray-500 text-xs italic">Enter country code (e.g., US, UK, DE)</p>
            </div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full">Generate and Validate</button>
        </form>
        <div id="results" class="mt-6 text-gray-700">
            </div>
    </div>

    <script>
        const form = document.getElementById('phone-number-form');
        const resultsContainer = document.getElementById('results');

        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const quantity = document.getElementById('quantity').value;
            const countryCode = 'US';

            resultsContainer.innerHTML = '<p class="text-center text-gray-500">Loading...</p>'; // Clear previous results

            try {
                const response = await fetch('/generate-and-validate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' // Add CSRF token for Laravel
                    },
                    body: JSON.stringify({ quantity, country_code })
                });

                const data = await response.json();

                if (response.ok) {
                    displayResults(data);
                } else {
                    displayError(data.error || 'An error occurred.'); // Show error from backend or generic message
                }
            } catch (error) {
                displayError('Failed to fetch data: ' + error.message);
            }
        });

        function displayResults(data) {
            let resultsHtml = '<h2 class="text-lg font-semibold mb-2 text-green-600">Validation Results</h2>';
            resultsHtml += `<p>Out of ${data.quantity} numbers generated, ${data.valid_count} were found to be valid for the country, which calculates to ${data.valid_percentage}% valid results.</p>`;
            resultsHtml += '<ul class="list-disc list-inside mt-2">';
            data.results.forEach(result => {
                const isValid = result.is_valid ? '<span class="text-green-500">Valid</span>' : '<span class="text-red-500">Invalid</span>';
                resultsHtml += `<li>Number: ${result.phone_number}, Country Code: ${result.country_code}, Type: ${result.type}, Length Match: ${result.is_possible_number_length_match}, Validity: ${isValid}</li>`;
            });
            resultsHtml += '</ul>';
            resultsContainer.innerHTML = resultsHtml;
        }

        function displayError(errorMessage) {
            resultsContainer.innerHTML = `<p class="text-center text-red-500">${errorMessage}</p>`;
        }
    </script>
</body>
</html>