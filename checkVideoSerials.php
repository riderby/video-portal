<?php


$mysqli_hostname = "localhost";
$mysqli_user = "root";
$mysqli_password = "";
$mysqli_database = "stalker_db";
$conn = new mysqli($mysqli_hostname, $mysqli_user, $mysqli_password, $mysqli_database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$getUrl = "SELECT id, rtsp_url FROM news WHERE rtsp_url  NOT LIKE '%_&episode=_%'";
$result = $conn->query($getUrl);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {

        $url = $row['rtsp_url'];

        echo $outurl = str_replace("http://main.itv.by", "parser.local", $url) . "1\n<br>";

        $ch = curl_init($outurl);
        curl_setopt($ch, CURLOPT_USERAGENT, 'IE20');
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, '1');
        echo "<br>";
        echo $page = curl_exec($ch);
        echo "<br>";

    }
}
$conn->close();
