<?php
include 'settings.php';
include 'trakt.php';

$trakt = new Trakt($TRAKT_API_KEY,true);
$trakt->setAuth($username, $password);
echo "auth http status $trakt->httpStatus\n";

$response = $trakt->users($username,"watched/shows?page=1&limit=10&extended=min");
echo "http status code = $trakt->httpStatus\n";
var_dump($response);

// 200 is watching something
// 204 is not watching anything
//echo $response["show"]["title"]." ".$response["show"]["ids"]["tvdb"]." ".$response["episode"]["season"]." ".$response["episode"]["number"]."\n";
?>
