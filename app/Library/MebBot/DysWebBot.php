<?php

namespace App\Library\ExcelValidations;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Laravel\Dusk\Browser;
use Laravel\Dusk\Chrome\ChromeProcess;
use Laravel\Dusk\ElementResolver;

/**
 * Office File Chunk Proccess
 */
class DysWebBot
{
    private $browser;
    private $name;
    private $password;

    public function __construct($name, $password) 
    {
        $this->name = $name;
        $this->password = $password;

        $process = (new ChromeProcess)->toProcess();
        $process->start(null, [
            'SystemRoot' => 'C:\\WINDOWS',
            'TEMP' => 'C:\Users\MAHAVIR\AppData\Local\Temp',
        ]);
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
}