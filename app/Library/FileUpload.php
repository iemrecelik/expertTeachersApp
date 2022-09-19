<?php

namespace App\Library;

use Illuminate\Http\UploadedFile;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;


/**
 * File upload proccess
 */
class FileUpload
{
	protected $file;
	protected $hashFileName;
	protected $guessExtension;
	protected $filters;
	protected $savePath = [];
	
	function __construct(Array $config = null)
	{
		if (is_array($config)) {
			
			$this->setConfig(
				$config['file'], 
				$config['filters'], 
				$config['extension'], 
				$config['rawFileName']
			);
		}
	}
	private function udfControl($extension, $originalName)
	{
		$pattern = "/^.*\.(udf)$/i";
		preg_match($pattern, $originalName, $orjExtension);
		
		if(empty($extension)) {
			$extension = $orjExtension[1] ?? $extension;
		}

		return $extension;
	}

	public function setConfig(UploadedFile $file, Array $filters = null, String $extension = null, Bool $rawFileName = false)
	{
		$extension = $this->udfControl($extension, $file->getClientOriginalName());

		$this->file = $file;
		$this->filters = $filters;
		
		$this->hashFileName = $rawFileName ? 
			pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) : 
			explode('.', $file->hashName())[0];

		$this->guessExtension = $extension ?? $file->guessExtension();
	}

	public function saveFile()
	{
		// Storage::put($this->savePathAndFileName('raw'), $this->file);

		$savePathAndFileName = $this->savePathAndFileName("raw");

		Storage::putFileAs($savePathAndFileName['pathName'], $this->file, $savePathAndFileName['fileName']);

		return $this;
	}

	public function saveImg()
	{
		if($this->filters !== null){

			$savePathAndFileName = $this->savePathAndFileName();
			
			foreach ($this->filters as $keyFilt => $valFilt) {
				
				$img = $this->performFilters($valFilt);

				Storage::put(
					str_replace('*filtName*', $keyFilt, $savePathAndFileName['pathAndFileName']), 
					$img->encode()
				);
			}

		}else{
			$img = Image::make($this->file);

			$savePathAndFileName = $this->savePathAndFileName("raw");

			Storage::put($savePathAndFileName['pathAndFileName'], $img->encode());
		}

		return $this;
	}

	public function performFilters($filter)
	{
		$img = Image::make($this->file);

		foreach ($filter as $key => $val) {
			
			if (is_array($val)) 
				$img->$key(...$val);
			else
				$img->$key($val);
		}

		return $img;
	}

	public function savePathAndFileName($filtName = '*filtName*')
	{
		// $fileName = $this->hashFileName.'.'.$this->guessExtension;
		
		/* $pathName = implode('/', [
			date('Y'),
			date('m'),
			date('d'),
			date('H'),
		]); */
		// $imagesPath = '/public/upload/images/'.$filtName;
		// dump($imagesPath);

		$pathName = implode('/', [
			date('Y'),
			date('m'),
			date('d'),
			date('H'),
		]);
		$imagesPath = $this->getImagesPath($filtName);
		$fileName = $this->getFileName();

		$pathAndFileName = "{$imagesPath}/{$pathName}/{$fileName}";

		// dump($pathAndFileName);die;

		$this->setSavePath("/{$pathName}/{$fileName}");

		return [
			'fileName' => $fileName,
			'pathName' => "{$imagesPath}/{$pathName}/",
			'pathAndFileName' => $pathAndFileName,
		];
	}

	private function getImagesPath($filtName = '*filtName*')
	{	
		return '/public/upload/images/'.$filtName;
	}

	private function getFileName()
	{
		return $this->hashFileName.'.'.$this->guessExtension;
	}

    /**
     * @return mixed
     */
    public function getSavePath()
    {
        return $this->savePath;
    }

    /**
     * @param mixed $savePath
     *
     * @return self
     */
    public function setSavePath($savePath)
    {
        $this->savePath[] = $savePath;

        return $this;
    }

    /**
     * @param mixed $file
     *
     * @return self
     */
    public function setFile(UploadedFile $file)
    {
        $this->file = $file;

        return $this;
    }
}