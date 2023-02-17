<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;


use Storage;

class ExampleTest extends DuskTestCase
{
    /**
     * A basic browser test example.
     *
     * @return void
     */
    public function testBasicExample()
    {
        /* $this->browse(function (Browser $browser) {
            $browser->visit('https://mebbis.meb.gov.tr/')
                    ->type('txtKullaniciAd', '61765236578')
                    ->type('txtSifre', '1079010790')
                    ->press('Giriş')
                    ->pause(1000)
                    ->mouseover('.image-flip:nth-child(1)')
                    ->pause(500)
                    ->click('a[title=MEBBİS-İSMAİLEMRE]')
                    ->pause(2000)
                    ->visit('https://mebbis.meb.gov.tr/EOzluk/PER00001.aspx')
                    ->pause(1000)
                    ->type('txtTCKimlikNo', '16127709610')
                    ->pause(1000)
                    ->click('input[name=btnAra]')
                    ->pause(2000)
                    ->assertSee('e-personel MODÜLÜ');
        }); */

        $this->browse(function (Browser $browser) {

            /* $url = $browser->driver->getCommandExecutor()->getAddressOfRemoteServer();
            $uri = '/session/' . $browser->driver->getSessionID() . '/chromium/send_command';
            
            $body = [
                'cmd' => 'Page.setDownloadBehavior',
                'params' => ['behavior' => 'allow', 'downloadPath' => storage_path('app/public/botdeneme')]
            ];

            (new \GuzzleHttp\Client())->post($url . $uri, ['body' => json_encode($body)]); */


            /* $download_path = storage_path('app/public/botdeneme');
            $url = $browser->driver->getCommandExecutor()->getAddressOfRemoteServer();
            $uri = '/session/' . $browser->driver->getSessionID() . '/chromium/send_command';
            $body = [
                'cmd' => 'Page.setDownloadBehavior',
                'params' => ['behavior' => 'allow', 'downloadPath' => $download_path]
            ];
            (new \GuzzleHttp\Client())->post($url . $uri, ['body' => json_encode($body)]); */


            /* $command = new \Facebook\WebDriver\Remote\CustomWebDriverCommand(
                $browser->driver->getSessionID(),
                "/session/:sessionId/chromium/send_command",
                "POST",
                [
                    "cmd" => "Browser.setDownloadBehavior",
                    "params" => ["behavior" => "allow", "downloadPath" => storage_path('app/public/botdeneme')]
                ]
            );
            $browser->driver->getCommandExecutor()->execute($command); */


            $val = $browser->visit('http://10.8.41.38/admin/login')
                ->type('email', 'ismailemre.celik@meb.gov.tr')
                ->type('password', '12345678')
                ->press('Giriş Yap')
                ->pause(1000)
                // ->click('aside.main-sidebar div.sidebar ul[data-widget="treeview"] li.data-menu-open:nth-child(4)')
                // ->pause(1000)
                // ->click('aside.main-sidebar div.sidebar ul li:nth-child(4) ul li:nth-child(2) a')
                // ->pause(2000)
                // ->value('tbody tr:first-child td:first-child');
                ->visit('http://10.8.41.38/admin/teachers')
                ->pause(1000);
                // ->click('aside.main-sidebar div.sidebar ul li:nth-child(4) ul li:nth-child(2) a')
                // ->storeSource('deneme');


            $arr = [
                $browser->text('tbody tr:nth-child(1) td:nth-child(1)'),
                $browser->text('tbody tr:nth-child(1) td:nth-child(2)'),
                $browser->text('tbody tr:nth-child(1) td:nth-child(3)')
            ];


            $browser->press('Excel Olarak Çıkart')->pause(4000);

            /* $url = $browser->driver->getCommandExecutor()->getAddressOfRemoteServer();
            $uri = '/session/' . $browser->driver->getSessionID() . '/chromium/send_command';
            
            $body = [
                'cmd' => 'Page.setDownloadBehavior',
                'params' => ['behavior' => 'allow', 'downloadPath' => storage_path('app/public/botdeneme')]
            ];

            (new \GuzzleHttp\Client())->post($url . $uri, ['body' => json_encode($body)]); */

            // Storage::put('deneme_file.txt', implode(' ', $arr));


            $browser->type('thr_name', 'Emre')
                    ->assertInputValue('thr_name', 'Emre')
                    ->assertSeeIn('h2.display-4', 'Liste');
        });

    }
}
