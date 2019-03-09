<?php
include 'settings.php';
include 'trakt.php';

$trakt = new Trakt($TRAKT_API_KEY,true);
$trakt->username = $username;
$trakt->setAuthPIN($pin,$client_id,$client_secret, $redirect_uri);
echo "auth http status $trakt->httpStatus\n";

$response = $trakt->users($username,"watching");
echo "http status code = $trakt->httpStatus\n";
//var_dump($response);

// 200 is watching something
// 204 is not watching anything
echo $response["show"]["title"]." ".$response["show"]["ids"]["tvdb"]." ".$response["episode"]["season"]." ".$response["episode"]["number"]."\n";
?>
