<?php

namespace Tests\Unit;

use App\Http\Controllers\PhoneNumberValidationController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class PhoneNumberValidationTest extends TestCase
{
    // use RefreshDatabase;

    /**
     * Test phone number validation with valid data.
     *
     * @return void
     */
    public function testValidPhoneNumberValidation()
    {
        $controller = new PhoneNumberValidationController();
        $request = new Request([
            'quantity' => 2,
            'country_code' => 'US',
        ]);

        $response = $controller->validatePhoneNumbers($request);

        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->getData(true);

        $this->assertArrayHasKey('quantity', $data);
        $this->assertArrayHasKey('country_code', $data);
        $this->assertArrayHasKey('valid_count', $data);
        $this->assertArrayHasKey('valid_percentage', $data);
        $this->assertArrayHasKey('results', $data);

        $this->assertCount(2, $data['results']);
        foreach ($data['results'] as $result) {
            $this->assertArrayHasKey('phone_number', $result);
            $this->assertArrayHasKey('country_code', $result);
            $this->assertArrayHasKey('type', $result);
            $this->assertArrayHasKey('is_possible_number_length_match', $result);
            $this->assertArrayHasKey('is_valid', $result);
        }
    }

    /**
     * Test phone number validation with invalid data.
     *
     * @return void
     */
    public function testInvalidPhoneNumberValidation()
    {
        $controller = new PhoneNumberValidationController();
        $request = new Request([
            'quantity' => -1, // Invalid quantity
            'country_code' => 'USA', // Invalid country code
        ]);

        $response = $controller->validatePhoneNumbers($request);

        $this->assertEquals(422, $response->getStatusCode()); // Expect a 422 status code for validation error
        $data = $response->getData(true);
        $this->assertArrayHasKey('errors', $data);
    }

      /**
     * Test phone number validation with empty data.
     *
     * @return void
     */
    public function testEmptyPhoneNumberValidation()
    {
        $controller = new PhoneNumberValidationController();
        $request = new Request([
            'quantity' => '',
            'country_code' => '',
        ]);

        $response = $controller->validatePhoneNumbers($request);

        $this->assertEquals(422, $response->getStatusCode()); // Expect a 422 status code for validation error
        $data = $response->getData(true);
        $this->assertArrayHasKey('errors', $data);
    }
}
