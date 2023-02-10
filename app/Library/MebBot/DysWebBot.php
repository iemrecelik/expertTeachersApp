<?php

namespace App\Library\MebBot;

use App\Library\MebBot\MebBot;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;


class DysWebBot extends MebBot
{
    private function replaceSpace($string)
    {
        $string = preg_replace("/\s+/", " ", $string);
        $string = trim($string);
        return $string;
    }

    public function getDocuments($date, $search, $itemStatus)
    {
        try {
            $this->browser->visit('https://mebbis.meb.gov.tr/')
                ->type('txtKullaniciAd', $this->userName)
                ->type('txtSifre', $this->password)
                ->press('Giriş')
                // ->waitFor('.image-flip:nth-child(1)')
                ->pause(1000)
                ->waitFor('section#team div.container div.row div.col-6:nth-child(2) .image-flip:nth-child(1)')
                ->mouseover('section#team div.container div.row div.col-6:nth-child(2) .image-flip:nth-child(1)')
                ->pause(1000)
                // ->pressAndWaitFor('a[title=DYS-'.$this->userName.' (DYS WEB)')
                ->pressAndWaitFor('li#rptProjeler_ctl01_rptKullanicilar_ctl01_liItem a')
                ->pause(3000);

            $window = collect($this->browser->driver->getWindowHandles())->last();
            $this->browser->driver->switchTo()->window($window);

            $this->browser->click('div.solpanel div ul li ul li:nth-child(8)')
                ->pause(1000)
                ->waitFor('#form\:globalSearch')
                ->pause(1000)
                ->type('form\:globalSearch', $search)
                /* ->pressAndWaitFor('#form\:oncekiKayitlariGetir_button')
                ->pause(1000) */
                ->waitFor('#form\:evrakListesi_dataTable_data')
                ->pause(3000);

            $trElements = $this->browser->elements('tbody#form\:evrakListesi_dataTable_data tr');

            $arr = $this->getTableRowDocument(count($trElements), $itemStatus, $date);

            $pageElements = $this->browser->elements('#form\:evrakListesi_dataTable_paginator_bottom span.ui-paginator-pages a');
            if(count($pageElements) > 1) {
                for ($i=2; $i <= count($pageElements); $i++) { 
                    $this->browser->click('#form\:evrakListesi_dataTable_paginator_bottom span.ui-paginator-pages a:nth-child('.$i.')')
                        ->pause(3000);

                    $trElements = $this->browser->elements('tbody#form\:evrakListesi_dataTable_data tr');

                    $arr = array_merge(
                        $arr, 
                        $this->getTableRowDocument(count($trElements), $itemStatus, $date)
                    );
                }
            }

        } catch (\Throwable $th) {
            $error = $th->getMessage();

            dd($error);

            throw ValidationException::withMessages(
                ['row' => $error]
            );
        }

        return $arr;
    }

    private function checkDateSelected($line, $date)
    {
        $recordDt = $this->browser->element('tbody#form\:evrakListesi_dataTable_data tr:nth-child('.$line.') td:nth-child(8)')->getText();
        $recordDt = explode(' ', $recordDt);
        $recordDt = $recordDt[0];

        return $date == $recordDt;
    } 

    private function getTableRowDocument($rowCount, $itemStatus, $date)
    {
        $arr = [];

        for ($i=1; $i <= $rowCount; $i++) {

            $boolDate = $this->checkDateSelected($i, $date);

            if(!$boolDate) {
                continue;
            }

            if($itemStatus == '0') {
                $arr[($i-1)]['dc_item_status'] = "0";
                $arr[($i-1)]['dc_who_send'] = 'Öğretmen Yetiştirme ve Geliştirme Kariyer Basamakları';
            }else {
                $arr[($i-1)]['dc_item_status'] = "1";
                $sender = $this->browser->element('tbody#form\:evrakListesi_dataTable_data tr:nth-child('.$i.') td:nth-child(9)')->getText();
                $sender = explode('-', $sender);
                $arr[($i-1)]['dc_who_send'] = $sender[1];
            }

            $numberAndDate = $this->browser->element('tbody#form\:evrakListesi_dataTable_data tr:nth-child('.$i.') td:nth-child(5)')->getText();
            $numberAndDate = $this->replaceSpace($numberAndDate);
            $numberAndDate = explode(' ', $numberAndDate);

            $arr[($i-1)]['dc_number'] = $numberAndDate[0];
            // $arr[($i-1)]['dc_date'] = strtotime($numberAndDate[1]);
            $arr[($i-1)]['dc_date'] = strtotime(str_replace('/', '-', $numberAndDate[1]));

            $subject = $this->browser->element('tbody#form\:evrakListesi_dataTable_data tr:nth-child('.$i.') td:nth-child(6)')->getText();
            $subject = str_replace('Büro Kayıt', '', $subject);
            $subject = $this->replaceSpace($subject);
            $arr[($i-1)]['dc_subject'] = $subject;

            $this->uploadFileAndAttachFiles($i);
            $filePathNames = $this->moveFileAndAttachmentFiles();

            foreach ($filePathNames as $key => $val) {
                if($key === 'dc_file_path') {
                    $arr[($i-1)][$key] = $val;
                }else {
                    $arr[($i-1)][$key][] = $val;
                }
            }

            // dd($arr);
        }

        return $arr;
    }

    private function uploadFileAndAttachFiles($line)
    {
        Storage::deleteDirectory('public/bottemps');
        $this->changeUrl('public\bottemps');

        $this->browser->pressAndWaitFor('tbody#form\:evrakListesi_dataTable_data tr:nth-child('.$line.') td:nth-child(6)')
            ->pause(2000);

        $this->browser->withinFrame('iframe#gozdenGecirmeEkraniId', function($iframe){
            $iframe->pause(2000)
                ->pressAndWaitFor('button#formspanel\:resmiBelgeKaydetId')
                ->pause(2000);

            $this->changeUrl('public\bottemps\attachments');

            $attFileBtnElement = $iframe->element('button#formspanel\:ekListesiBtn[aria-disabled=false]');

            if($attFileBtnElement) {
                $iframe->pressAndWaitFor('button#formspanel\:ekListesiBtn')->pause(2000);

                $trAttachElements = $iframe->elements('tbody#formspanel\:ekListesi_data tr');

                for ($i=1; $i <= count($trAttachElements); $i++) {
                    $iframe->pressAndWaitFor('tbody#formspanel\:ekListesi_data tr:nth-child('.$i.') td:nth-child(4) button')
                        ->pause(1000);
                }
            }
        });

        $this->browser->click('div#form\:islistesi2_Dialog a[aria-label=Kapat]')->pause(3000);
        
        return true;
    }
    
    public function getSendDocuments()
    {
        
    }

    public function __destruct()
    {
        parent::__destruct();
    }
}