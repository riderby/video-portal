<?php
header("Content-Type: text/html; charset=utf-8");

$mysql_hostname = "localhost";
$mysql_user = "root";
$mysql_password = "";
$mysql_database = "zoom";
$bd = mysql_connect($mysql_hostname, $mysql_user, $mysql_password) or die("Error");
mysql_select_db($mysql_database, $bd) or die("Error");


$reader = new XMLReader();

if (!$reader->open("content.xml"))  { // xml size 600MB+
    die("Ошибка открытия файла '.content'");
}

while ($reader->read()) {


    //парсер таблицы actors
    if ($reader->nodeType == XMLReader::ELEMENT && $reader->name == 'actors_list') {

        while ($reader->nodeType !== XMLReader::END_ELEMENT) {
            $reader->read();

            if ($reader->nodeType == XMLReader::ELEMENT && $reader->name == 'person') {
                $actorId = $reader->getAttribute('id');
                $actorName = $reader->getAttribute('name');

                $sql = "INSERT INTO actors (id, name)
                    VALUES
                    ('$actorId',
                    '$actorName')";
                mysql_query($sql);

            }
        }
    }
    //работает


    $sql = mysql_query("SELECT count(id) from actors");
    $row = mysql_fetch_row($sql);
    $totalActors = $row[0];


    //парсер таблицы category
    if ($reader->nodeType == XMLReader::ELEMENT && $reader->name == 'category') {

        $categoryId = $reader->getAttribute('id');

        $sql = "INSERT INTO category (id) VALUES ('$categoryIdId')";
        mysql_query($sql);

    }
    //работает


    //парсер таблицы content
    if ($reader->nodeType == XMLReader::ELEMENT && $reader->name == 'content') {

        $contentId = $reader->getAttribute('id');
        $contentRating = $reader->getAttribute('rating');
        $contentPoster = $reader->getAttribute('poster');
        $contentCountry = $reader->getAttribute('country');
        $contentName = $reader->getAttribute('name');
        $contentSmallPoster = $reader->getAttribute('small_poster');
        $contentScore = $reader->getAttribute('score');
        $contentDescription = $reader->getAttribute('description');
        $contentYear = $reader->getAttribute('year');


        $sql = "INSERT INTO Content (id, rating, poster, country,
                        name, small_poster, score,
                        description, year)
                 VALUES
                ('$contentId', '$contentRating', '$contentPoster',
                '$contentCountry', '$contentName', '$contentSmallPoster',
                '$contentScore' , '$contentDescription', '$contentYear')";
        mysql_query($sql);
    }
    //работает




    if ($reader->nodeType == XMLReader::ELEMENT && $reader->name == 'content') {

        $contentId = $reader->getAttribute('id');

        while ($reader->nodeType !== XMLReader::END_ELEMENT) {
            $reader->read();

            if ($reader->nodeType == XMLReader::ELEMENT && $reader->name == 'actors_list') {

                while ($reader->nodeType !== XMLReader::END_ELEMENT) {
                    $reader->read();

                    if ($reader->nodeType == XMLReader::ELEMENT && $reader->name == 'types_list') {

                        while ($reader->nodeType !== XMLReader::END_ELEMENT) {
                            $reader->read();

                            if ($reader->nodeType == XMLReader::ELEMENT && $reader->name == 'type') {

                                $typesId = $reader->getAttribute('id');

                                $sql = "INSERT INTO content_types (content_id, type_id)
                    VALUES ('$contentId','$typesId')";
                                mysql_query($sql);

                            }

                        }
                    }
                }
            }
        }
    }



    if ($reader->nodeType == XMLReader::ELEMENT && $reader->name == 'content') {

        $contentId = $reader->getAttribute('id') . "\n";

        $doc = new DOMDocument('1.0', 'UTF-8');
        $xml = simplexml_import_dom($doc->importNode($reader->expand(), true));

        while ($reader->read() && $reader->nodeType !== XMLReader::END_ELEMENT) {


            $typesId = $xml->types_list->type->attributes()->id;


        }


        $sql = "INSERT INTO content_types (content_id, type_id)
                            VALUES ('$contentId','$typesId')";
        mysql_query($sql);


    }


    //парсер таблицы content_persons
    if ($reader->nodeType == XMLReader::ELEMENT && $reader->name == 'content') {

        $contentId = $reader->getAttribute('id');

        while ($reader->nodeType !== XMLReader::END_ELEMENT) {
            $reader->read();

            if ($reader->nodeType == XMLReader::ELEMENT && $reader->name == 'actors_list') {

                while ($reader->nodeType !== XMLReader::END_ELEMENT) {
                    $reader->read();

                    if ($reader->nodeType == XMLReader::ELEMENT && $reader->name == 'person') {

                        $personsId = $reader->getAttribute('id');

                        $sql = "INSERT INTO content_persons (content_id, person_id)
                            VALUES ('$contentId','$personsId')";
                        mysql_query($sql);

                    }
                }
            }
        }

    }
    //работает

    //content deirectors


    //парсер content_director
    if ($reader->nodeType == XMLReader::ELEMENT && $reader->name == 'content') {

        $contentId = $reader->getAttribute('id') . "\n";

        $doc = new DOMDocument('1.0', 'UTF-8');
        $xml = simplexml_import_dom($doc->importNode($reader->expand(), true));

        while ($reader->read() && $reader->nodeType !== XMLReader::END_ELEMENT) {


            $directorId = $xml->directors_list->person->attributes()->id;


        }


        $sql = "INSERT INTO content_directors (content_id, director_id)
                            VALUES ('$contentId','$directorId')";
        mysql_query($sql);


    }
    //работает


    //парсер таблицы content_directors

    if ($reader->nodeType == XMLReader::ELEMENT && $reader->name == 'content') {

        echo $contentId = $reader->getAttribute('id') . "\n";


        while ($reader->name === 'directors_list') {


            $reader->read();


            while ($reader->nodeType !== XMLReader::END_ELEMENT) {
                $reader->read();

                if ($reader->nodeType == XMLReader::ELEMENT && $reader->name == 'person') {

                    $personsId = $reader->getAttribute('id');

                    $sql = "INSERT INTO content_director (content_id, director_id)
                            VALUES ('$contentId','$personsId')";
                    mysql_query($sql);


                }
            }
        }
    }


    //парсер таблицы country
    if ($reader->nodeType == XMLReader::ELEMENT && $reader->name == 'country') {

        $countryId = $reader->getAttribute('id');
        $countryName = $reader->getAttribute('name');
        $countryCode = $reader->getAttribute('code');

        $sql = "INSERT INTO country (id, name, code)
                    VALUES ('$countryId', '$countryName', '$countryCode')";
        mysql_query($sql);
    }
    //работает


    //парсер таблицы directors
    if ($reader->nodeType == XMLReader::ELEMENT && $reader->name == 'directors_list') {

        while ($reader->nodeType !== XMLReader::END_ELEMENT) {
            $reader->read();

            if ($reader->nodeType == XMLReader::ELEMENT && $reader->name == 'person') {
                $directorId = $reader->getAttribute('id');
                $directorName = $reader->getAttribute('name');

                $sql = "INSERT INTO directors (id, name)
                            VALUES ('$directorId','$directorName')";
                mysql_query($sql);

            }
        }
        //работает
    }


    $sql = mysql_query("SELECT count(id) from directors");
    $row = mysql_fetch_row($sql);
    $totalDirector = $row[0];


    //парсер таблицы genres
    if ($reader->nodeType == XMLReader::ELEMENT && $reader->name == 'type') {

        $genresId = $reader->getAttribute('id');
        $genresNameLat = $reader->getAttribute('name_lat');
        $genresName = $reader->getAttribute('name');

        $sql = "INSERT INTO genres (id, name_lat, name_rus)
                    VALUES ('$genresId', '$genresNameLat', '$genresName')";
        mysql_query($sql);

    }
    //работает

    $sql = mysql_query("SELECT count(id) from genres");
    $row = mysql_fetch_row($sql);
    $totalGenres = $row[0];


    //парсер таблицы screenshot
    if ($reader->nodeType == XMLReader::ELEMENT && $reader->name == 'screenshots') {

        $episodeId = $reader->getAttribute('episode_id');

    }

    if ($reader->nodeType == XMLReader::ELEMENT && $reader->name == 'screenshot') {

        $screenUrl = $reader->getAttribute('url');
        $screenSize = $reader->getAttribute('size');


        $sql = "INSERT INTO screenshot (episode_id, url, size)
                    VALUES ('$episodeId', '$screenUrl', '$screenSize')";
        mysql_query($sql);
    }
    //работает


    //парсер таблицы episode
    if ($reader->nodeType == XMLReader::ELEMENT && $reader->name == 'episode') {

        $episodeId = $reader->getAttribute('id');
        $airDate = $reader->getAttribute('air_date');
        $duration = $reader->getAttribute('duration');
        $tvChannel = $reader->getAttribute('tv_channel');
        $parentId = $reader->getAttribute('parent_id');
        $published = $reader->getAttribute('published');
        $preview = $reader->getAttribute('preview');
        $episodeName = $reader->getAttribute('episode_name');
        $ageRating = $reader->getAttribute('age_rating');
        $catchup = $reader->getAttribute('catchup');
        $smil = $reader->getAttribute('smil');
        $expires = $reader->getAttribute('expires');
        $season = $reader->getAttribute('season');
        $episodeNum = $reader->getAttribute('episode_num');

        $sql = "INSERT INTO episode (id, air_date, duration, tv_channel, parent_id, published,
               preview, episode_name, age_rating, catchup, smil, expires, season, episode_num)
                VALUES ('$episodeId', '$airDate', '$duration', '$tvChannel', '$parentId', '$published', '$preview'
                , '$episodeName', '$ageRating', '$catchup', '$smil', '$expires', '$season', '$episodeNum')";

        mysql_query($sql);
    }
    //работает


}


echo "Insert was directors: " . $totalDirector . " items";
echo "<br>";
echo "Insert was genres: " . $totalGenres . " items";


$reader->close();


