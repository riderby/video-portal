<?php


$url = $_POST['linkKinoKong'];



$ch = curl_init($url);

curl_setopt($ch, CURLOPT_USERAGENT, 'IE20');
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, '1');

$page = curl_exec($ch);

$needVideoFormat = '.flv';
$pos = stripos($page, $needVideoFormat);


$needSD = '.mp4';
$checkSD = stripos($page, $needSD);

$needFLV = '.flv';
$checkFLV = stripos($page, $needFLV);


echo $videoSD = $end[0];

if ($checkSD == true) {


    $start = explode('"file":"http://', $page);
    $end = explode(".mp4", $start[1]);
    echo $videoSD = "http://" .  $end[0] . ".mp4";


}

if ($checkFLV == true) {


    $start = explode('"file":"http://', $page);
    $end = explode(".flv", $start[1]);
    echo $videoFLV = "http://" . $end[0] . ".flv";


}





