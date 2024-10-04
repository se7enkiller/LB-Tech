<?php

require_once __DIR__ . '/../autoloader.php';

use Classes\Csv;

if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
    echo 'Ошибка при загрузке файла.';
    return;
}

$fileTmpPath = $_FILES['csv_file']['tmp_name'];

$fileType = $_FILES['csv_file']['type'];
$allowedTypes = ['text/csv', 'application/vnd.ms-excel'];

if (in_array($fileType, $allowedTypes, true)) {
    $csvHandler = new Csv();
    $csvData = $csvHandler->read($fileTmpPath);

    echo '<pre>';
    print_r($csvData);
    echo '</pre>';
} else {
    echo 'Неверный тип файла. Пожалуйста, загрузите CSV файл.';
}
