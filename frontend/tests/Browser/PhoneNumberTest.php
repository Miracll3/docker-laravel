<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PhoneNumberTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function testExample(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->type('quantity', '5')
                    ->type('country_code', 'US')
                    ->press('Generate and Validate')
                    ->waitForText('Validation Results')
                    ->assertSee('Out of 5 numbers generated')
                    ->assertSee('Country Code: US');
                    //->assertSee('Valid') //Could be unreliable.
        });
    }
}
