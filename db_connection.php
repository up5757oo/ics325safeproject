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
?>
