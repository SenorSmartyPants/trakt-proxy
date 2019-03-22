<?php 
include 'settings.php';

$pin_url = "https://trakt.tv/oauth/authorize?client_id=".$client_id."&redirect_uri=".$redirect_uri."&response_type=code";

if (isset($_GET["auth_pin"])) {
    $pin = trim($_GET["auth_pin"]);
    file_put_contents("tokens/pin",$pin);
    echo "PIN " . $pin . " is saved. Try watching a show to see if check in works.";
}
?>

<p>Click on name to set user currently watching.</p>

<p><a href="http://nanners:8123/setuser.php?user=senorsmartypants">SenorSmartyPants</a></p>

<p><a href="http://nanners:8123/setuser.php?user=heidilynne">HeidiLynne</a></p>

<p>
If not signed in using popcorn hour, or check-ins not working...
<a href="<?= $pin_url ?>">Authorize on Trakt</a></p>

Then paste that PIN in here <form><input name="auth_pin"><input type=submit></form>