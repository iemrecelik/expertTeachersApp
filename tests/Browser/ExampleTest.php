<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ExampleTest extends DuskTestCase
{
    /**
     * A basic browser test example.
     *
     * @return void
     */
    public function testBasicExample()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('https://mebbis.meb.gov.tr/')
                    ->type('txtKullaniciAd', '61765236578')
                    ->type('txtSifre', '1079010790')
                    ->press('Giriş')
                    ->pause(7000)
                    ->assertSee('Laravel');
        });
    }
}
