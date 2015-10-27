<?php


function getContent($season, $episode, $content)
{


    $whitespaces = " ";


    if ($season != $whitespaces && $episode != $whitespaces && $content != $whitespaces) {

        $sql = mysql_query("SELECT smil FROM episode WHERE season = $season AND episode_num = $episode AND parent_id = $content");

        if ($sql == true) {
            while ($row = mysql_fetch_assoc($sql)) {
                echo "from sql: " . $smil = $row['smil'] . "\n";

            }
        }
    }


    if (!empty($smil)) {


        $content = file_get_contents($smil);
        $pos = strpos($content, 'sign="LQ"');
        $content = substr($content, $pos);
        $pos = strpos($content, 'streamer="');
        $content = substr($content, $pos);
        $pos = strpos($content, 'bitrate');
        $content = substr($content, 0, $pos);
        $urlServerEnd = strpos($content, 'file="/video');
        $urlServer = substr($content, 10, $urlServerEnd);
        $pos = strpos($urlServer, 'http');
        $urlServer = substr($urlServer, $pos);
        $pos = strpos($content, 'streamer="');
        $content = substr($content, $pos);
        $pos = strpos($urlServer, '"');
        $urlServer = substr($urlServer, 0, $pos);
        $pos = strpos($content, 'file="');
        $content = substr($content, $pos);
        $pos = strpos($content, 'file');
        $content = substr($content, $pos);
        $pos = strpos($content, '" ');
        $content = substr($content, 6, -9);

        $fullUrl = "$urlServer$content";


    }
}

$season = trim($_GET['season']);
$episode = trim($_GET['episode']);
$content = trim($_GET['content']);

getContent($season, $episode, $content);


$sql = mysql_query("SELECT content.id, name, description, screenshot.url, episode.smil
FROM content, screenshot, episode
WHERE screenshot.id = content.id
AND screenshot.size =  '768x432'
AND content.id = episode.id
LIMIT 10");


$sqlGetCount = mysql_query("SELECT count(episode_num) AS counterContent FROM episode  WHERE parent_id = $content");

if ($sqlGetCount == true) {
    $row = mysql_fetch_row($sqlGetCount);


    if ($row[0] > 1) {

        $fullUrl = "$urlServer$content";
    } elseif ($row[0] == 1) {


        $fullUrl = "$urlServer$content";


    }


}







