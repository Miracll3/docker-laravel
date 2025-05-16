<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Landing page test
     */
    public function test_the_application_returns_a_successful_phone_number_generator_landing(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200)->assertSee('Generate Phone Numbers');
    }
}
