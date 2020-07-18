<?php

namespace RedundantData;

class Finder
{
    const READ_LENGTH = 4096;

    protected $filesList = [];
    protected $logger;

    public function __construct($logger)
    {
        $this->logger = $logger;
    }

    public function find($searchPath)
    {
        if (!file_exists($searchPath)) {
            exit("Путь не существует\n");
        }
        $this->recursiveScandir($searchPath);
        $this->findRepeatedValues();
    }

    protected function recursiveScandir($dir)
    {
        $files = scandir($dir);

        foreach ($files as $key => $value) {
            $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
            if (!is_dir($path)) {
                $this->filesList[] = $path;
            } else if ($value != "." && $value != "..") {
                $this->recursiveScandir($path);
            }
        }
    }

    protected function findRepeatedValues()
    {
        $startIndex = 0;
        while (count($this->filesList) > $startIndex) {
            $startElementMarked = false;
            foreach ($this->filesList as $index => $currentFile) {
                if ($startIndex >= $index || !isset($this->filesList[$startIndex])) {
                    continue;
                }
                if ($this->filesIdentical($this->filesList[$startIndex], $currentFile)) {
                    if (!$startElementMarked) {
                        $this->logger->log($this->filesList[$startIndex] . "\n");
                        echo $this->filesList[$startIndex] . "\n";
                        $startElementMarked = true;
                    }
                    $this->logger->log($this->filesList[$index] . "\n");
                    echo $this->filesList[$index] . "\n";
                    unset($this->filesList[$index]);
                }
            }
            $startIndex++;
        }
    }


    /**
     * Взято с php.net
     * https://www.php.net/manual/ru/function.md5-file.php#94494
     */
    protected function filesIdentical($fn1, $fn2)
    {
        if(filetype($fn1) !== filetype($fn2)) {
            return FALSE;
        }

        if(filesize($fn1) !== filesize($fn2)) {
            return FALSE;
        }

        if(!$fp1 = fopen($fn1, 'rb')) {
            return FALSE;
        }

        if(!$fp2 = fopen($fn2, 'rb')) {
            fclose($fp1);
            return FALSE;
        }

        $same = TRUE;
        while (!feof($fp1) and !feof($fp2)) {
            if(fread($fp1, self::READ_LENGTH) !== fread($fp2, self::READ_LENGTH)) {
                $same = FALSE;
                break;
            }
        }

        if(feof($fp1) !== feof($fp2)) {
            $same = FALSE;
        }

        fclose($fp1);
        fclose($fp2);

        return $same;
    }
}
