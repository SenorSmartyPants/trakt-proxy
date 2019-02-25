<?php
include 'settings.php';

$ch = curl_init();

#$trakturl = "https://private-anon-339caeb58-trakt.apiary-proxy.com/";
$trakturl = "https://api.trakt.tv/";
#$trakturl = "https://private-anon-339caeb58-trakt.apiary-mock.com/";

curl_setopt($ch, CURLOPT_URL, $trakturl + "auth/login");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);

curl_setopt($ch, CURLOPT_POST, TRUE);

$post = array("login" => "$username", 
              "password" => "$password");
$json = json_encode($post);
var_dump($json);

curl_setopt($ch, CURLOPT_POSTFIELDS, $json);

curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Content-Type: application/json",
    "trakt-api-version: 2",
    "trakt-api-key: $TRAKT_API_KEY"
    ));


$response = curl_exec($ch);
$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "http status code = $http_status\n";
var_dump($response);

$traktusertoken = json_decode($response);

echo "file store output trakt-user-token\n";
file_put_contents("tokens/trakt-user-token",$traktusertoken);
var_dump(file_get_contents("tokens/trakt-user-token"));

#phpinfo();
?>
