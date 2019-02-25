<?php 
include 'pin.php';

//"Popcornhour" v2 API key!
$TRAKT_API_KEY = "4af27c888f0ab8f3de79fd2e7f5149eac6fadb9b4736b1292f4cd1f0cdfb4ba1";

#just used for watching call
$username = "senorsmartypants";

$client_id = "4af27c888f0ab8f3de79fd2e7f5149eac6fadb9b4736b1292f4cd1f0cdfb4ba1";
$client_secret = "db7b3486de1b4c89f7583374086dc6458f8210bc8844e477b3383aa7544fa46d";
$redirect_uri = "urn:ietf:wg:oauth:2.0:oob";

$autocancel = true;
$includeduration = false;

//list of lower-case titles or IDs of shows/movies to not to check in with trakt when watching
$blacklist = array("the daily show","71256");
?>
