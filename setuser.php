<?php 
if (isset($_GET["user"])) {
    $username = trim($_GET["user"]);
    file_put_contents("tokens/currentuser",$username);
    echo "user is now " . $username;
}
?>