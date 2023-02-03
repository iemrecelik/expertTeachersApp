<?php

namespace App\Library\MebBot;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Laravel\Dusk\Browser;
use Laravel\Dusk\Chrome\ChromeProcess;
use Laravel\Dusk\ElementResolver;
use Illuminate\Validation\ValidationException;


class MebbisBot
{
    private $browser;
    private $userName;
    private $password;

    public function __construct($userName, $password) 
    {
        $this->userName = $userName;
        $this->password = $password;

        $process = (new ChromeProcess)->toProcess();
        $process->start(null, [
            'SystemRoot' => 'C:\\WINDOWS',
            'TEMP' => 'C:\Users\MAHAVIR\AppData\Local\Temp',
        ]);
        // $options = (new ChromeOptions)->addArguments(['--disable-gpu', '--headless']);
        $options = (new ChromeOptions)->addArguments(['--disable-gpu']);
        $options->setBinary("C:\Program Files\Google\Chrome\Application\chrome.exe");

        $capabilities = DesiredCapabilities::chrome()->setCapability(ChromeOptions::CAPABILITY, $options);
        $driver = retry(5, function () use($capabilities) {
            return RemoteWebDriver::create('http://localhost:9515', $capabilities);
        }, 50);

        $browser = new Browser($driver);
        $browser->resize(1920, 1080);
        
        $this->browser = $browser;
    }

    public function getTeacherWithTcNo($tcNo)
    {
        try {
            $this->browser->visit('https://mebbis.meb.gov.tr/')
                ->type('txtKullaniciAd', $this->userName)
                ->type('txtSifre', $this->password)
                ->press('Giriş')
                ->waitFor('.image-flip:nth-child(1)')
                ->mouseover('.image-flip:nth-child(1)')
                ->pressAndWaitFor('a[title=MEBBİS-'.$this->userName.'K]')
                ->visit('https://mebbis.meb.gov.tr/EOzluk/PER00001.aspx')
                ->waitFor('input[name=txtTCKimlikNo]')
                ->type('txtTCKimlikNo', $tcNo)
                ->waitFor('input[name=btnAra]')
                ->click('input[name=btnAra]')
                ->pause(2000)
                // ->waitFor('table#gvPersonelAra')
                ->assertSee('MEBBİS - e-Personel MODÜLÜ');


            $tcNo           = $this->browser->text('table#gvPersonelAra tbody tr:nth-child(2) td:nth-child(2)');
            $name           = $this->browser->text('table#gvPersonelAra tbody tr:nth-child(2) td:nth-child(3)');
            $surname        = $this->browser->text('table#gvPersonelAra tbody tr:nth-child(2) td:nth-child(4)');
            $careerLadder   = $this->browser->text('table#gvPersonelAra tbody tr:nth-child(2) td:nth-child(7)');
            $birthDay       = $this->browser->text('table#gvPersonelAra tbody tr:nth-child(2) td:nth-child(9)');
            $gender         = $this->browser->text('table#gvPersonelAra tbody tr:nth-child(2) td:nth-child(22)');
        } catch (\Throwable $th) {
            $error = $this->browser->text('span#lblHata');

            throw ValidationException::withMessages(
                ['row' => $error]
            );
        }

        switch ($careerLadder) {
            case '':
                $careerLadder = -1;
                break;

            case ' ':
                $careerLadder = -1;
                break;
                
            case 'Uzman Öğretmen':
                $careerLadder = 1;
                break;

            case 'Başöğretmen':
                $careerLadder = 2;
                break;
            
            default:
                $careerLadder = -1;
                break;
        }
        
        $arr = [
            'thr_tc_no'           => $tcNo,
            'thr_name'            => \Transliterator::create('tr-title')->transliterate($name),
            'thr_surname'         => \Transliterator::create('tr-upper')->transliterate($surname),
            'thr_teacher_ladder'  => $careerLadder,
            'thr_birth_day'       => strtotime("01-01-$birthDay"),
            'thr_gender'          => $gender == 'Erkek' ? '0' : '1',
            'inst_id'             => 1
        ];

        return $arr;
    }

    public function localtest()
    {
        $url = $this->browser->driver->getCommandExecutor()->getAddressOfRemoteServer();
        $uri = '/session/' . $this->browser->driver->getSessionID() . '/chromium/send_command';
        
        $body = [
            'cmd' => 'Page.setDownloadBehavior',
            'params' => ['behavior' => 'allow', 'downloadPath' => storage_path('app/public/botdeneme')]
        ];

        (new \GuzzleHttp\Client())->post($url . $uri, ['body' => json_encode($body)]);
        
        $val = $this->browser->visit('http://10.8.41.38/admin/login')
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
            $this->browser->text('tbody tr:nth-child(1) td:nth-child(1)'),
            $this->browser->text('tbody tr:nth-child(1) td:nth-child(2)'),
            $this->browser->text('tbody tr:nth-child(1) td:nth-child(3)')
        ];

        // Storage::put('deneme_file.txt', implode(' ', $arr));

        $this->browser->press('Excel Olarak Çıkart')->pause(5000);


        $this->browser->type('thr_name', 'Emre')
                ->assertInputValue('thr_name', 'Emre')
                ->assertSeeIn('h2.display-4', 'Liste');


        $url = $this->browser->driver->getCommandExecutor()->getAddressOfRemoteServer();
        $uri = '/session/' . $this->browser->driver->getSessionID() . '/chromium/send_command';
        
        $body = [
            'cmd' => 'Page.setDownloadBehavior',
            'params' => ['behavior' => 'allow', 'downloadPath' => storage_path('app/public/botdeneme')]
        ];

        (new \GuzzleHttp\Client())->post($url . $uri, ['body' => json_encode($body)]);
       
    }
}