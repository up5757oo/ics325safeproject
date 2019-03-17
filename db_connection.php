<?php
/**
*   Database connection PHP Page
*   Bears
 */

 DEFINE('DATABASE_HOST', 'localhost');
 DEFINE('DATABASE_DATABASE', 'ics325safedb');
 DEFINE('DATABASE_USER', 'root');
 DEFINE('DATABASE_PASSWORD', '');

 $db = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_DATABASE);
 $db->set_charset("utf8");
 $hour = 3600;
 $day = (24*$hour);

 //checks sql connection was successful, returns error is connection fails
 if ($db->connect_errno) {
     printf("Connect failed: %s\n", $mysqli->connect_error);
     exit();
    };//database connect check
    //checks the timestamp is over 24 hours old for the pi id cache file before proceeding
    if (filemtime('dataFiles/pi_id_cache.json') < time()-$day) {
        //places the pi_id data into the cache file
        if ($result = $db->query("SELECT DISTINCT PI_id FROM cadence ORDER BY start_date")) {
            $rows = array();
            while($row = $result->fetch_array()) {
                $rows[] = $row;
            }
            file_put_contents('dataFiles/pi_id_cache.json', json_encode($rows));
        }
    };//ends pi id json update

    //checks the timestamp is over 24 hours old for the art cache file before proceeding
    if (filemtime('dataFiles/art_cache.json') < time()-$day) {
        //places the art data into the cache file
        if ($result = $db->query("SELECT DISTINCT parent_name FROM trains_and_teams where type = 'AT' ORDER BY parent_name")) {
            $rows = array();
            while($row = $result->fetch_array()) {
                $rows[] = $row;
            }
            file_put_contents('dataFiles/art_cache.json', json_encode($rows));
        }
    };//ends art json update

    //checks the timestamp is over 24 hours old for the at cache file before proceeding
    if (filemtime('dataFiles/at_cache.json') < time()-$day) {
        //places the art data into the cache file
        if ($result = $db->query("SELECT DISTINCT parent_name, team_name FROM trains_and_teams where type = 'AT' ORDER BY parent_name, team_name")) {
        $rows = array();
        while($row = $result->fetch_array()) {
            $rows[] = $row;
        }
        file_put_contents('dataFiles/at_cache.json', json_encode($rows));
    }
};//ends at cache update

//checks the timestamp is over 24 hours old for the at cache file before proceeding
if (filemtime('dataFiles/url_cache.json') < time()-$day) {
    //places the art data into the cache file
    if ($result = $db->query("SELECT value FROM preferences WHERE name='BASE_URL'")) {
        $rows = array();
        while($row = $result->fetch_array()) {
            $rows[] = $row;
        }
        file_put_contents('dataFiles/url_cache.json', json_encode($rows));
    }
};//ends url json update

function setArtCookie(){
    if( !isset($_COOKIE['artCookie'])){
        //checks the preference table for a Default ART
        $db = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_DATABASE);
        $db->set_charset("utf8");
        $art_default_query = "SELECT value FROM preferences WHERE name='DEFAULT_ART' ORDER BY value LIMIT 1";
        $art_default_results = mysqli_query($db, $art_default_query);
        if ($art_default_results->num_rows > 0) {
          while($art_default = $art_default_results->fetch_assoc()) {
            setcookie("artCookie", $art_default["value"]);
          }//end while
        }//end preference search if
        else {
          //if a Default ART was not found, it checks the first value for the ART
          $art_default_query = "SELECT DISTINCT parent_name FROM trains_and_teams where type = 'AT' ORDER BY parent_name LIMIT 1";
          $art_default_results = mysqli_query($db, $art_default_query);
          //starts loop to check the results and update the cookie if results are returned
          if ($art_default_results->num_rows > 0) {
            while($art_default = $art_default_results->fetch_assoc()) {
              setcookie("artCookie", $art_default["parent_name"]);
            }//end while
          }//end preference search if
        }
      } //end cookie check
      
      //adds the cookie to the art selected variable
};

function buildArtMenu(){
    //initializes the art variable
    $art="";
    //initializes the selected ART variable
    $art_select = "";
    //sets the selected ART with the Cookie
    $art_select = $_COOKIE['artCookie'];
    //uses json file to build ART select menu. Updates selected default with the Cookie value
    $art_file = file_get_contents("dataFiles/art_cache.json");
    $art_json = json_decode($art_file, true);
    $x=count($art_json);
    for($i = 0; $i < $x; $i++){
        $art_item = $art_json[$i]['parent_name'];
        //checks if the ART should selected
        if($art_item===$art_select){
            $art = $art.'<option value="'.$art_item.'" selected>'.$art_item.'</option>';
        } else{
            $art = $art.'<option value="'.$art_item.'">'.$art_item.'</option>';
        }
    } return $art;
};

function buildTeamMenu(){
    //initializes variables
    $artCookie = '';
    $at_menu = '';
    $db = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_DATABASE);
    $db->set_charset("utf8");
    //checks for the artCookie before proceeding
        if(isset($_COOKIE['artCookie'])){
            $artCookie = $_COOKIE['artCookie'];
            $at_query = "SELECT DISTINCT team_name FROM trains_and_teams where type = 'AT' and parent_name='".$artCookie."' order by team_name";
            $at_menu_results = mysqli_query($db, $at_query);
            if ($at_menu_results->num_rows > 0) {
                while($at_item = $at_menu_results->fetch_assoc()) {
                    $at_menu = $at_menu.'<option value="'.$at_item.'">'.$at_item.'</option>';
                }//end while
            }//end if 
        } return $at_menu;
    };
?>
