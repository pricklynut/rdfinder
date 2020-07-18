<?php

namespace RedundantData;

class Logger
{
    protected $path;
    protected $file;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function log($string)
    {
        if (empty($this->file)) {
            $file = fopen($this->path, 'a+');
            if (!$file) {
                throw new \Exception('Нет прав на запись в указанную директорию');
            }
            $this->file = $file;
        }

        fwrite($this->file, $string);
    }
}
