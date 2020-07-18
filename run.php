<?php

require 'Finder.php';
require 'Logger.php';

$config = parse_ini_file('config.ini');
if (empty($config['logfile']) || empty($config['scandir'])) {
    die('Не настроена конфигурация: logfile - путь к файлу лога, scandir - корневая директория для поиска');
}

try {
    $logger = new RedundantData\Logger($config['logfile']);
    $finder = new RedundantData\Finder($logger);
    $finder->find($config['scandir']);
} catch (\Exception $e) {
    echo $e->getMessage();
}
