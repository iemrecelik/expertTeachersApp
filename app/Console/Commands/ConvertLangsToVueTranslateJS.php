<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ConvertLangsToVueTranslateJS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature =  'make:vue-translate-js';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '
        Convert langs files to vue translate js file
    ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->converToVueTranslateJS();
    }

    protected function converToVueTranslateJS()
    {
        $langDirs = scandir(resource_path('lang/'));
        $langs = array_diff($langDirs, ['.', '..', 'translate.js']);

        $transArr = [];
        foreach ($langs as $lang) {
            
            $files   = glob(resource_path('lang/' . $lang . '/*.php'));
            $strings = [];

            foreach ($files as $file) {
                $name           = basename($file, '.php');
                $strings[$name] = require $file;
            }

            $transArr[$lang] = $strings;
        }

        $transContent = preg_replace(
            '/:(\w+)/', 
            '{${1}}', 
            json_encode($transArr)
        );

        $transContent = 'module.exports = '.$transContent;

        $translateJS = fopen(resource_path('lang/translate.js'), "w");
        fwrite($translateJS, $transContent);
        fclose($translateJS);

        $this->info('Add vue translate file successfully');
    }
}
