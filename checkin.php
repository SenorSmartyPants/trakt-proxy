<?php
include 'settings.php';
include 'trakt.php';

// Ignore user aborts and allow the script
// to run forever
ignore_user_abort(true);
set_time_limit(0);

$time_start = microtime(true);

$debug = isset($_GET["debug"]);
$trakt = new Trakt($client_id,$debug);

//get querystring parameters
$title = array_key_exists('title', $_GET) ? $_GET['title'] : null;
$year = array_key_exists('year', $_GET) ? $_GET['year'] : null;
$imdb_id = array_key_exists('imdb_id', $_GET) ? $_GET['imdb_id'] : null;
$tvdb_id = array_key_exists('tvdb_id', $_GET) ? $_GET['tvdb_id'] : null;
$season = array_key_exists('season', $_GET) ? $_GET['season'] : null;
$episode = array_key_exists('episode', $_GET) ? $_GET['episode'] : null;
$episode_id = array_key_exists('episode_id', $_GET) ? $_GET['episode_id'] : null;

$users = explode(",", $username);
foreach($users as $user) {
    handleRequest($user);
}

if ($debug) {
  echo "<pre>";
  echo "$title\n";
  echo "checkin expires in $wait seconds\n";
  print_r($result);
  print_r($watching);
  echo "</pre>";
}

$time_end = microtime(true);
$time = $time_end - $time_start;

if ($debug) echo "Checkin took $time seconds\n";


function handleRequest($username)
{
  global $trakt, $debug;

  //blacklist required variables
  global $blacklist, $tvdb_id, $imdb_id, $title;

  global $pin,$client_id,$client_secret, $redirect_uri;
  $trakt->username = $username;
  $trakt->setAuthPIN($pin,$client_id,$client_secret, $redirect_uri);

  if ($debug) echo "auth http status $trakt->httpStatus\n";

  //cancel or do a new checkin
  if (isset($_GET["cancel"])) {
    $result = cancelCheckin(); 
  } else {
    //don't check in if on the black list
    if (!in_array(strtolower($title),$blacklist) 
      and !in_array($tvdb_id,$blacklist)
      and !in_array($imdb_id,$blacklist))
    {
      handleCheckin($username);
    }
  }

}

function handleCheckin($username)
{
  global $debug,$client_id,$client_secret, $redirect_uri, $autocancel;

  global $trakt, 
  $tvdb_id, 
  $imdb_id, 
  $title, 
  $year, 
  $season, 
  $episode, 
  $episode_id,
  $duration;


  $result = checkin();

  if ($debug) {
    echo "checkin http status: ";
    var_dump($trakt->httpStatus);
    #var_dump($trakt);
  }

  //error checking
  if ($trakt->httpStatus == 401) {
    //try to refresh token
    $trakt->refreshToken($client_id,$client_secret, $redirect_uri);
    
    //checkin to originally requested movie
    $result = checkin();
  } else if ($trakt->httpStatus == 409) {
    //already checked into something
    $now = new DateTime();
    $expires = new DateTime($result["expires_at"]);
    $wait = $expires->getTimestamp() - $now->getTimestamp();


    $watching = $trakt->users($username,"watching");
    // 200 is watching something
    if ($trakt->httpStatus == 200) {
      if ($debug) {
        var_dump($imdb_id);
        var_dump($watching["movie"]["ids"]["imdb"]); 
        var_dump(intval($tvdb_id));
        var_dump($watching["show"]["ids"]["tvdb"]); 
        var_dump(intval($season));
        var_dump($watching["episode"]["season"]);
        var_dump(intval($episode));
        var_dump($watching["episode"]["number"]); 
        var_dump(intval($episode_id));
        var_dump($watching["episode"]["ids"]["tvdb"]); 
      }
      //is it the same?
      //will have to update check when movies are added, because they will probably return tvdb ids and imdb for shows
      if (
          ((intval($tvdb_id) > 0 || intval($episode_id) > 0) && $watching["movie"] != null) || //watching a movie, but check into a show
          (intval($tvdb_id) > 0 && intval($episode_id) == 0 && // check into a show and ... 
            (
              intval($tvdb_id) != $watching["show"]["ids"]["tvdb"] || //different series
              intval($season) != $watching["episode"]["season"] || //different seasons
              intval($episode) != $watching["episode"]["number"] //different episode numbers
            )
          ) ||
          (intval($episode_id) > 0 && (intval($episode_id) != $watching["episode"]["ids"]["tvdb"])) || //watching a movie, but check into a show(with epi id)
          (strlen($imdb_id) > 0 && $watching["show"] != null) || // watching a show, but check into a movie
          (strlen($imdb_id) > 0 && $imdb_id != $watching["movie"]["ids"]["imdb"]) // different movies
      ) {
        //different
        if ($autocancel) {
          if ($debug) echo "attempting auto cancel\n";
          //cancel checkin
          cancelCheckin(); 
          //checkin to originally requested movie
          $result = checkin();
        }
      } else {
        if ($debug) echo "same, don't do anything\n";
      }
    // 204 is not watching anything
    } else if ($trakt->httpStatus == 204) {
        //checkin to originally requested movie
        $result = checkin();
    }
  }
}

function cancelCheckin() 
{
  global $trakt;
  return $trakt->checkinDelete();
}
function checkin()
{
  global $trakt, 
    $tvdb_id, 
    $imdb_id, 
    $title, 
    $year, 
    $season, 
    $episode, 
    $episode_id,
    $duration;

  if ($tvdb_id > 0 || $episode_id > 0) {
    $arrshow;
    $arrEpisode = array();
    //add elements if not null
    
    //always use episode id if provided, and nothing else
    if ($episode_id) {
      $arrEpisode["ids"] = array("tvdb" => "$episode_id");
    } else {
      $arrshow = array("title" => "$title", "year" => $year, "ids" => array("tvdb" => "$tvdb_id"));
      
      $arrEpisode["season"] = "$season";
      $arrEpisode["number"] = "$episode";
    }

    $watching = array();
    if ($arrshow) $watching["show"] = $arrshow;
    $watching["episode"] = $arrEpisode;

  } else {
    $watching = array(
          "movie" => array("title" => "$title", "year" => $year, "ids" => array("imdb" => "$imdb_id")) 
        );
  } 
  return $trakt->checkin($watching);
}
?>
