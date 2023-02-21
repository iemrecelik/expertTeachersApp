<?php

namespace App\Library\MebBot;

use App\Library\MebBot\MebBot;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;


class DysWebBot extends MebBot
{
    private $receiverArr = [];

    private function replaceSpace($string)
    {
        $string = preg_replace("/\s+/", " ", $string);
        $string = trim($string);
        return $string;
    }

    public function getDocuments($date, $itemStatus)
    {
        /* tarihe göre veritabanınından evrak listesine çekme başla */
        $existDoc = \App\Models\Admin\DcDocuments::select('dc_number')->where('dc_date', strtotime($date))->get()->toArray();
        $existDoc = array_column($existDoc, 'dc_number');
        /* tarihe göre veritabanınından evrak listesine çekme bitiş*/

        $search = $itemStatus == 0 ? 'Büro Kayıt' : 'Onay Sonrası Gözden Geçirme';

        try {
            $this->browser->pause(1000)
                ->waitFor('section#team div.container div.row div.col-6:nth-child(2) .image-flip:nth-child(1)')
                ->mouseover('section#team div.container div.row div.col-6:nth-child(2) .image-flip:nth-child(1)')
                ->pause(1000)
                ->pressAndWaitFor('li#rptProjeler_ctl01_rptKullanicilar_ctl01_liItem a')
                ->pause(3000);

            $window = collect($this->browser->driver->getWindowHandles())->last();
            $this->browser->driver->switchTo()->window($window);

            $this->browser->click('div.solpanel div ul li ul li:nth-child(8)')
                ->pause(1000)
                ->waitFor('#form\:globalSearch')
                ->pause(1000)
                ->type('form\:globalSearch', $search)
                ->pause(2000)
                ->waitFor('#form\:evrakListesi_dataTable_data')
                /* ->pressAndWaitFor('#form\:oncekiKayitlariGetir_button')
                ->waitFor('#form\:evrakListesi_dataTable_data')
                ->pause(1000)
                ->pressAndWaitFor('#form\:oncekiKayitlariGetir_button')
                ->pause(1000)
                ->waitFor('#form\:evrakListesi_dataTable_data') */
                ->pause(3000);
                
            $trElements = $this->browser->elements('tbody#form\:evrakListesi_dataTable_data tr[role=row]');

            $arr = $this->getTableRowDocument(count($trElements), $itemStatus, $date, $search, $existDoc);

            if(count($arr) < 6) {
                $pageElements = $this->browser->elements('#form\:evrakListesi_dataTable_paginator_bottom span.ui-paginator-pages a');
                if(count($pageElements) > 1) {
                    for ($i=2; $i <= count($pageElements); $i++) { 
                        $this->browser->click('#form\:evrakListesi_dataTable_paginator_bottom span.ui-paginator-pages a:nth-child('.$i.')')
                            ->pause(3000);

                        $trElements = $this->browser->elements('tbody#form\:evrakListesi_dataTable_data tr[role=row]');

                        $arr = array_merge(
                            $arr, 
                            $this->getTableRowDocument(count($trElements), $itemStatus, $date, $search, $existDoc, count($arr))
                        );
                    }
                }
            }
        } catch (\Throwable $th) {
            $error = $th->getMessage();
            // dd($error);
            throw ValidationException::withMessages(
                ['row' => $error]
            );
        }

        return $arr;
    }

    private function getTableRowDocument($rowCount, $itemStatus, $date, $search, $existDoc, $limit = 1)
    {
        $arr = [];

        for ($i=1; $i <= $rowCount; $i++) {

            $boolDate = $this->checkDateSelected($i, $date, $existDoc);

            if(!$boolDate || $limit > 6) {
                continue;
            }

            if($itemStatus == '0') {
                $arr[($i-1)]['dc_item_status'] = "0";
                $arr[($i-1)]['dc_who_receiver'] = "Öğretmen Yetiştirme ve Geliştirme Kariyer Basamakları";
            }else {
                $arr[($i-1)]['dc_item_status'] = "1";
            }

            $sender = $this->browser->element('tbody#form\:evrakListesi_dataTable_data tr:nth-child('.$i.') td:nth-child(9)')->getText();
            $sender = explode('-', $sender);
            $arr[($i-1)]['dc_who_send'] = $sender[1];

            $numberAndDate = $this->browser->element('tbody#form\:evrakListesi_dataTable_data tr:nth-child('.$i.') td:nth-child(5)')->getText();
            $numberAndDate = $this->replaceSpace($numberAndDate);
            $numberAndDate = explode(' ', $numberAndDate);

            $arr[($i-1)]['dc_number'] = $numberAndDate[0];
            $arr[($i-1)]['dc_date'] = strtotime(str_replace('/', '-', $numberAndDate[1]));

            $subject = $this->browser->element('tbody#form\:evrakListesi_dataTable_data tr:nth-child('.$i.') td:nth-child(6)')->getText();
            $subject = str_replace($search, '', $subject);
            $subject = $this->replaceSpace($subject);
            $arr[($i-1)]['dc_subject'] = $subject;

            $receiver = $this->uploadFileAndAttachFiles($i);
            if($itemStatus == '1') {
                $arr[($i-1)]['dc_who_receiver'] = $receiver;
            }
            
            $filePathNames = $this->moveFileAndAttachmentFiles();

            foreach ($filePathNames as $key => $val) {
                if($key === 'dc_file_path') {
                    $arr[($i-1)][$key] = $val;
                }else {
                    $arr[($i-1)][$key] = $val;
                }
            }

            $limit++;
        }

        return $arr;
    }

    private function uploadFileAndAttachFiles($line)
    {
        $this->receiverArr = [];

        Storage::deleteDirectory('public/bottemps');
        $this->changeUrl('public\bottemps');

        $this->browser->pressAndWaitFor('tbody#form\:evrakListesi_dataTable_data tr:nth-child('.$line.') td:nth-child(6)')
            ->pause(2000);

        /* $this->browser->withinFrame('iframe#formspanel\:j_idt25', function($anotherIframe) {
            $anotherIframe->pressAndWaitFor('div#toolbarViewerRight button#download')
                ->pause(2000);
        }); */

        $this->browser->waitFor('iframe#gozdenGecirmeEkraniId');
        
        $this->browser->withinFrame('iframe#gozdenGecirmeEkraniId', function($iframe){
            /* Gönderilen Bilgisini çekme başla */
            $iframe->pressAndWaitFor('button#formspanel\:dagitimBilgileriBtn')
                ->pause(3000)
                ->pressAndWaitFor('div#formspanel\:form3\:tabViewOge ul li:nth-child(2)')
                ->pause(1000);

            $receiverEl = $iframe->elements('tbody#formspanel\:form3\:tabViewOge\:dagitimYapilmisBirimlerListesi_data tr');

            for ($i=1; $i <= count($receiverEl); $i++) {
                $this->receiverArr[] = $iframe->element('tbody#formspanel\:form3\:tabViewOge\:dagitimYapilmisBirimlerListesi_data tr:nth-child('.$i.') td:nth-child(2)')->getText();
            }

            $iframe->pressAndWaitFor('div#formspanel\:dialogPenceresi a[aria-label=Kapat]')->pause(1000);
            /* Gönderilen Bilgisini çekme bitiş */

            $this->changeUrl('public\bottemps\attachments');
            
            $attFileBtnElement = $iframe->element('button#formspanel\:ekListesiBtn[aria-disabled=false]');
            
            if($attFileBtnElement) {
                $iframe->pressAndWaitFor('button#formspanel\:ekListesiBtn')->pause(2000);

                $trAttachElements = $iframe->elements('tbody#formspanel\:ekListesi_data tr');

                for ($i=1; $i <= count($trAttachElements); $i++) {
                    $iframe->pressAndWaitFor('tbody#formspanel\:ekListesi_data tr:nth-child('.$i.') td:nth-child(4) button')
                        ->pause(1000);
                }

                $iframe->pressAndWaitFor('div#formspanel\:dialogPenceresi a[aria-label=Kapat]')->pause(1000);
            }

            $this->changeUrl('public\bottemps');

            $iframe->withinFrame('iframe#formspanel\:j_idt25', function($anotherIframe) {
                $anotherIframe->pressAndWaitFor('div#toolbarViewerRight button#download')
                    ->pause(2000);
            });
        });

        $this->browser->click('div#form\:islistesi2_Dialog a[aria-label=Kapat]')->pause(3000);
        
        return implode('|', $this->receiverArr);
    }

    private function checkDateSelected($line, $date, $existDoc)
    {
        $bool = false;

        $numberAndDate = $this->browser->element('tbody#form\:evrakListesi_dataTable_data tr:nth-child('.$line.') td:nth-child(5)')->getText();
        $numberAndDate = $this->replaceSpace($numberAndDate);
        $numberAndDate = explode(' ', $numberAndDate);
        $number = $numberAndDate[0];

        if (in_array($number, $existDoc)) {
            $bool = false;
        }else {
            $recordDt = $this->browser->element('tbody#form\:evrakListesi_dataTable_data tr:nth-child('.$line.') td:nth-child(8)')->getText();
            $recordDt = explode(' ', $recordDt);
            $recordDt = $recordDt[0];

            $bool = $date == $recordDt;
        }

        return $bool;
    } 
    
    public function getIncomeAndSendDocumentCount($date)
    {
        $searchDocArr = [
            'incoming' => 'Büro Kayıt',
            'send' => 'Onay Sonrası Gözden Geçirme'
        ];

        $count = $this->getDocumentCount($date, $searchDocArr);

        return [
            'incomingCount' => $count['incoming'],
            'sendCount' => $count['send'],
        ];
    }

    private function getDocumentCount($date, $searchDocArr)
    {
        try {
            $this->browser
                ->pause(1000)
                ->waitFor('section#team div.container div.row div.col-6:nth-child(2) .image-flip:nth-child(1)')
                ->mouseover('section#team div.container div.row div.col-6:nth-child(2) .image-flip:nth-child(1)')
                ->pause(1000)
                ->pressAndWaitFor('li#rptProjeler_ctl01_rptKullanicilar_ctl01_liItem a')
                ->pause(3000);

            $window = collect($this->browser->driver->getWindowHandles())->last();
            $this->browser->driver->switchTo()->window($window);

            $this->browser->click('div.solpanel div ul li ul li:nth-child(8)')
                ->pause(1000);

            foreach ($searchDocArr as $srKey => $srVal) {
                $rowCount[$srKey] = $this->getRowCount($srVal, $date);
            }

            return $rowCount;

        } catch (\Throwable $th) {
            $error = $th->getMessage();

            dd($error);

            throw ValidationException::withMessages(
                ['row' => $error]
            );
        }
    }

    private function getRowCount($search, $date)
    {
        $rowCount = 0;
        
        try {
            $this->browser->waitFor('#form\:globalSearch')
                ->pause(1000)
                ->type('form\:globalSearch', $search)
                // ->type('form\:globalSearch', 'Onay Sonrası')
                ->waitFor('#form\:evrakListesi_dataTable_data')
                ->pause(3000);

            $trElements = $this->browser->elements('tbody#form\:evrakListesi_dataTable_data tr[role=row]');

            // dd($trElements);

            for ($i=1; $i <= count($trElements); $i++) { 
                $boolDate = $this->checkDateSelected($i, $date);

                if($boolDate) {
                    $rowCount += 1;
                }
            }

            $pageElements = $this->browser->elements('#form\:evrakListesi_dataTable_paginator_bottom span.ui-paginator-pages a');

            if(count($pageElements) > 1) {
                for ($j=2; $j <= count($pageElements); $j++) { 
                    $this->browser->click('#form\:evrakListesi_dataTable_paginator_bottom span.ui-paginator-pages a:nth-child('.$i.')')
                        ->pause(3000);

                    $trElements = $this->browser->elements('tbody#form\:evrakListesi_dataTable_data tr[role=row]');

                    for ($k=1; $k <= count($trElements); $k++) { 
                        $boolDate = $this->checkDateSelected($k, $date);
        
                        if($boolDate) {
                            $rowCount += 1;
                        }
                    }
                }
            }

            return $rowCount;
        } catch (\Throwable $th) {
            $error = $th->getMessage();

            dd($error);

            throw ValidationException::withMessages(
                ['row' => $error]
            );
        }

    }

    public function __destruct()
    {
        parent::__destruct();
    }
}