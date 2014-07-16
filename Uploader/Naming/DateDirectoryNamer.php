<?php

namespace Oneup\UploaderBundle\Uploader\Naming;

use Oneup\UploaderBundle\Uploader\File\FileInterface;

class DateDirectoryNamer implements NamerInterface
{
    /**
     * @var string
     */
    public $directoryPathDateFormat = 'Y/m/d';
    
    /**
     * @var Oneup\UploaderBundle\Uploader\File\FileInterface
     */
    public $fileNamer = null;
    
    public function __construct($directoryPathDateFormat = null, $fileNamer = null) {
        if(null !== $directoryPathDateFormat)
            $this->directoryPathDateFormat = $directoryPathDateFormat;
        
        if(null !== $fileNamer)
            $this->fileNamer = $fileNamer;
    }
    
    public function name(FileInterface $file)
    {
        $time = time();
        
        $directoryPath = date($this->directoryPathDateFormat, $time);
        $fileName = $this->fileNamer->name($file);
        
        return sprintf('%s/%s', $directoryPath, $fileName);
    }
}
