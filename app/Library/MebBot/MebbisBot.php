<?php

namespace App\Library\MebBot;

use App\Library\MebBot\MebBot;


class MebbisBot extends MebBot
{
    public function getTeacherWithTcNo($tcNo)
    {
        try {
            $this->browser/* ->visit('https://mebbis.meb.gov.tr/')
                ->type('txtKullaniciAd', $this->userName)
                ->type('txtSifre', $this->password)
                ->press('Giriş') */
                ->pause(1000)
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
        /* $contents = Storage::get($a);
        dd($contents); */

        /* $url = $this->browser->driver->getCommandExecutor()->getAddressOfRemoteServer();
        $uri = '/session/' . $this->browser->driver->getSessionID() . '/chromium/send_command';
        
        $body = [
            'cmd' => 'Page.setDownloadBehavior',
            'params' => ['behavior' => 'allow', 'downloadPath' => storage_path('app/public/botdeneme')]
        ];

        (new \GuzzleHttp\Client())->post($url . $uri, ['body' => json_encode($body)]); */
        
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
        // $el = $this->browser->element('#export-excel2');
        $el = $this->browser->element('#DataTables_Table_0 tbody tr:nth-child(1) td:nth-child(1)');

        /* $this->browser->quit();
        $this->process->stop(); */
        dd($el->getText());
        $this->browser->press('Excel Olarak Çıkart')->pause(3000);

        $this->browser->type('thr_name', 'Emre')
                ->assertInputValue('thr_name', 'Emre')
                ->assertSeeIn('h2.display-4', 'Liste');

        echo '<pre>';
        $arr2 = $this->browser->elements('table.dataTable tbody tr');

        $arr = $this->getTableRowDocument(count($arr2), '0');
        $this->moveFile();
        dd($arr);
        die;
    }

    public function getTableRowDocument($rowCount, $itemStatus)
    {
        $arr = [];

        for ($i=1; $i <= $rowCount; $i++) {
            $arr[($i-1)]['name'] = $this->browser->element('table.dataTable tbody tr:nth-child('.$i.') td:nth-child(2)')->getText();
        }

        return $arr;
    }

    public function __destruct()
    {
        parent::__destruct();
    }
}