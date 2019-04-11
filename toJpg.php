<?php // 利用libreoffice 与 swftools 将其他文件转为 swf 文件

$DB_DATABASE = $argv[1];
$DB_USERNAME = $argv[2];
$DB_PASSWORD = $argv[3];
$scr_file = $argv[4];
$other_allow_extension = ["doc", "ppt", "docx", "xlsx"]; //pdf直接转 swf 这些文件先转pdf

$filename = basename($scr_file);
$path = dirname($scr_file);

// 文件名以及拓展名分离数组
$info = preg_split("/\./", $filename);

$jpg_path = getcwd()."/storage/view/" . $info[0];

if(!file_exists($jpg_path)) {
    mkdir($jpg_path);
}

$extension = $info[1];
if(in_array($extension, $other_allow_extension)) {
    exec("/usr/bin/libreoffice --headless --invisible --convert-to pdf $scr_file --outdir $path");
} elseif ($extension != 'pdf') {
    die("");
}


exec("/usr/bin/convert $path/$info[0].pdf $jpg_path/1.jpg");

if(in_array($extension, $other_allow_extension)) {
    unlink($path . "/" . $info[0] . ".pdf");
}

$count = system("ls " . $jpg_path . " -l | grep \"^-\" | wc -l");

$conn = new PDO("mysql:host=localhost;dbname=$DB_DATABASE", $DB_USERNAME, $DB_PASSWORD);

$conn->exec("UPDATE `documents` SET page=$count WHERE filename='$filename'");
