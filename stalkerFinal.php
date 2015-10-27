<?php

header("Content-Type: text/html; charset=utf-8");


$sqlGetContent = mysql_query("Select  * from  Content");


if ($sqlGetContent == true) {

    while ($Ñontent = mysql_fetch_assoc($sqlGetContent)) {

        $Id = $Ñontent['id'];


        $sqlGetSeason = mysql_query("Select  season from  episode WHERE parent_id = $Id  GROUP BY season");


        while ($rowSeason = mysql_fetch_assoc($sqlGetSeason)) {

            echo "Season: " . $season = $rowSeason['season'] . "\n";


            $sqlGetEpizodies = mysql_query("SELECT count(episode_num) AS counterContent FROM episode  WHERE parent_id = $Id and season = $season");


            if ($sqlGetEpizodies == true) {

                $Epizodes = mysql_fetch_row($sqlGetEpizodies);


                echo("$Epizodes[0]");
                if ($Epizodes[0] > 1) {


                    $number = $Epizodes[0];
                    $arr = range(1, $Epizodes[0]);
                    $str = serialize($arr);
                    $string = $str;


                    $urlFilm = "http://main.itv.by/getsmil.php?season=" . trim($season) . "&content=" . trim($Id) . "&episode=";


                    $result = mysql_query("SELECT distinct content.id, name, description, screenshot.url,  content_types.type_id, year
                        FROM Content as content, screenshot, episode, content_types
                        WHERE screenshot.episode_id = episode.id
                        AND screenshot.size = '768x432'
                        AND content.id = episode.parent_id
                        AND content_types.content_id = content.id
                        AND content.id=$Id
                        GROUP BY content.id");

                    while ($final = mysql_fetch_assoc($result)) {

                        echo $Id;


                        $sqlCounterSeason = mysql_query("SELECT  count(season), season FROM  episode WHERE parent_id = $Id AND season = $season");


                        if ($sqlCounterSeason == true) {


                            $counter = mysql_fetch_assoc($sqlCounterSeason);


                            $counterSeason = $counter['season'];
                            $name = $final['name'];


                            if ($counterSeason != 0) {

                                echo $name = $final['name'] . " $counterSeason " . iconv("cp1251", "UTF-8", " ñåçîí");
                            }

                        }


                        $description = $final['description'];
                        $url = $final['url'];
                        $smilSelect = $final['smil'];
                        $typesId = $final['type_id'];
                        $videoYear = $final['year'];


                        $insert1 = mysql_query("INSERT INTO stalker_db.news (id, name, o_name, description, pic, rtsp_url, series , category_id, year, added)
                        VALUES ('$Id', '$name', '$name' , '$description', '$url', '$urlFilm', '$str' , '$typesId', '$videoYear', '$videoYear')");


                    }


                } else {


                    $result = mysql_query("SELECT content.id, name, description, screenshot.url, episode.smil, content_types.type_id, year
                        FROM Content as content, screenshot, episode, content_types
                        WHERE screenshot.episode_id = episode.id
                        AND screenshot.size = '768x432'
                        AND content.id = episode.parent_id
                        AND content_types.content_id = content.id
                        AND content.id=$Id");

                    while ($final = mysql_fetch_assoc($result)) {

                        echo $Id;

                        $name = $final['name'];
                        $description = $final['description'];
                        $url = $final['url'];
                        $smilSelect = $final['smil'];
                        $typesId = $final['type_id'];
                        $videoYear = $final['year'];

                    }

                    $urlFilm = "http://main.itv.by/getsmil.php?season=0&episode=0&content=" . $Id;

                    $insert2 = mysql_query("INSERT INTO stalker_db.news (id, name, o_name, description, pic, rtsp_url , category_id, year, added)
                        VALUES ('$Id', '$name', '$name', '$description', '$url', '$urlFilm' , '$typesId','$videoYear','$videoYear')");


                }

            }

        }

    }


}














