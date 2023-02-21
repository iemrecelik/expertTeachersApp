<?php

namespace App\Library\MebBot;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Laravel\Dusk\Browser;
use Laravel\Dusk\Chrome\ChromeProcess;
use Laravel\Dusk\ElementResolver;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;


class MebBot
{
    protected $browser;
    protected $process;
    protected $userName;
    protected $password;

    private function decryption($encryption)
    {   
        // Store the cipher method
        $ciphering = "AES-128-CTR";
        
        // Use OpenSSl encryption method
        $iv_length = openssl_cipher_iv_length($ciphering);
        $options = 0;

        // Non-NULL Initialization Vector for decryption
        $decryption_iv = '1029384756123456';
        
        // Store the decryption key
        $decryption_key = "kariyer_basamaklari";
        
        // Use openssl_decrypt() function to decrypt the data
        $decryption = openssl_decrypt ($encryption, $ciphering, 
                $decryption_key, $options, $decryption_iv);

        return $decryption;
    }

    public function __construct($userName = null, $password = null) 
    {
        set_time_limit(1800);

        $user = auth()->user();
        
        $this->userName = $userName ?? $user->mebbis_name;
        $this->password = $password ?? $this->decryption($user->mebbis_password);

        $this->process = (new ChromeProcess)->toProcess();
        $this->process->start(null, [
            'SystemRoot' => 'C:\\WINDOWS',
            'TEMP' => 'C:\Users\MAHAVIR\AppData\Local\Temp',
        ]);
        // $options = (new ChromeOptions)->addArguments(['--disable-gpu', '--headless']);
        $options = (new ChromeOptions)->addArguments(['--disable-gpu']);
        $options->setBinary("C:\Program Files\Google\Chrome\Application\chrome.exe");

        $options->setExperimentalOption('prefs', [
            // 'download.default_directory' => storage_path("bottemps")
            'download.default_directory' => Storage::path("public\bottemps")
        ]);

        $capabilities = DesiredCapabilities::chrome()->setCapability(ChromeOptions::CAPABILITY, $options);
        $driver = retry(5, function () use($capabilities) {
            return RemoteWebDriver::create('http://localhost:9515', $capabilities);
        }, 50);

        $browser = new Browser($driver);
        $browser->resize(1920, 1080);
        // $browser->maximize();
        
        $this->browser = $browser;

        $this->checkLogin();
    }

    private function checkLogin()
    {
        $this->browser->visit('https://mebbis.meb.gov.tr/')
                ->type('txtKullaniciAd', $this->userName)
                ->type('txtSifre', $this->password)
                ->press('Giriş');

        $errorLbl = $this->browser->element('div#divalert span#lblSorun');

        if($errorLbl) {
            throw ValidationException::withMessages(
                ['error' => $errorLbl->getText()]
            );
        }
    }

    protected function changeUrl($path)
    {
        $url = $this->browser->driver->getCommandExecutor()->getAddressOfRemoteServer();
        $uri = '/session/' . $this->browser->driver->getSessionID() . '/chromium/send_command';
        
        $body = [
            'cmd' => 'Page.setDownloadBehavior',
            'params' => ['behavior' => 'allow', 'downloadPath' => Storage::path($path)]
        ];

        (new \GuzzleHttp\Client())->post($url . $uri, ['body' => json_encode($body)]);
    }

    private function checkFileDownloaded($fileList)
    {
        $bool = true;

        foreach ($fileList as $val) {
            $downloaded = explode('.', $val);
            $downloaded = end($downloaded);

            if($downloaded == 'crdownload') {
                $bool = false;
            }
        }

        return $bool;
    }

    protected function moveFileAndAttachmentFiles($baseDocPath = 'public/bottemps')
	{
        /* Evrak taşıma başla */
        $orgFilePaths = Storage::files($baseDocPath);

        $downloaded = $this->checkFileDownloaded($orgFilePaths);

        if(!$downloaded) {
            sleep(1000);
            $this->moveFileAndAttachmentFiles();
        }

        $filePathNames['dc_file_path'] = $this->moveFile($orgFilePaths[0]);
        /* Evrak taşıma bitiş */

        /* Evrak eklerini taşıma başla */
        $attachmentDocPath = $baseDocPath.'/attachments';
        $attFilePaths = Storage::files($attachmentDocPath);

        $downloaded = $this->checkFileDownloaded($attFilePaths);

        if(!$downloaded) {
            sleep(1000);
            $this->moveFileAndAttachmentFiles();
        }

        foreach ($attFilePaths as $val) {
            $filePathNames['dc_att_file_path'][] = $this->moveFile($val);
        }
        /* Evrak eklerini taşıma bitiş */

        return $filePathNames;
	}

    private function moveFile($orgFilePath)
    {
        $orgFileName = basename($orgFilePath);

        $pathName = implode('/', [
			date('Y'),
			date('m'),
			date('d'),
			date('H'),
		]);

		$filePath = '/public/upload/images/raw';
        $file = str_replace('/', '\\', $orgFilePath);

        $destination = "$filePath/$pathName/$orgFileName";
        // $destination = "$filePath/$pathName/".uniqid()."$orgFileName";

        if (Storage::exists($destination)) {
            $orgFileName = uniqid()."$orgFileName";
            $destination = "$filePath/$pathName/$orgFileName";
        }

        Storage::move($file, $destination);

        return "/$pathName/$orgFileName";
    }

    public function __destruct()
    {
        $this->browser->quit();
        $this->process->stop();
    }
}