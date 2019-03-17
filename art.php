<?php
$page_title = 'Agile Release Trains';
//include(PAGES_PATH . '/header.php');

require_once("./db_connection.php");

//pull BASE_URL from json file
$url_file = file_get_contents("dataFiles/url_cache.json");
$url_json = json_decode($url_file, true);
$x=count($url_json);
for($i = 0; $i < $x; $i++){
  $base_url_out = $url_json[$i]['value'];
}
//initializes remaining variables
$pi_id="";
$art="";
$at = '';
$generate_button='New';
//Uses setArtCookie function defined in db_connection.php to check for artCookie. It will set the Cookie if it is not found
setArtCookie();
//Uses buildArtMenu function defined in db_connection.php to set the art variable with the HTML for the ART select menu
$art = buildArtMenu();
$at = buildTeamMenu();

//uses PI ID json file to build program increment table with the current program intrement id identified through a sql query
$pi_id_file = file_get_contents("dataFiles/pi_id_cache.json");
$pi_id_json = json_decode($pi_id_file, true);
$x=count($pi_id_json);
$pi_id_now_query = "SELECT PI_id FROM cadence where DATE(NOW()) between start_date and end_date + 2";
$pi_id_select_results = mysqli_query($db, $pi_id_now_query);
if ($pi_id_select_results->num_rows > 0) {
  while($pi_id_now = $pi_id_select_results->fetch_assoc()) {
    $pi_id_select = $pi_id_now["PI_id"];
  }//end while
}//end if
$pi_id_menu='';
for($i = 0; $i < $x; $i++){
  $pi_id_item = $pi_id_json[$i]['PI_id'];
  if($pi_id_item===$pi_id_select){
    $pi_id_menu = $pi_id_menu.'<option value="'.$pi_id_item.'" selected>'.$pi_id_item.'</option>';
  } else{
    $pi_id_menu = $pi_id_menu.'<option value="'.$pi_id_item.'">'.$pi_id_item.'</option>';
  }
};
$pi_id_array=array($pi_id."-1", $pi_id."-2" ,$pi_id."-3" ,$pi_id."-4", $pi_id."-5",$pi_id."-6",$pi_id."-IP");
?>



<!--
form for submitting data that will be prepopulated with data from the variables
-->
<form  method="POST" id="PI_form" name="PI_form">
    <table id="form_table" class="container">
        <tr>
            <td>
                <!--Base URL:-->
            </td>
            <td>
                <input type="hidden" id="baseUrl" name="baseUrl" readonly="readonly" value="<?php
          echo $base_url_out;
          ?>">
            </td>
        </tr>
        <tr>
            <td>Program Increment ID:</td>
            <td>
                <select id="PI_ID" name="pi_id">
                    <?php

          echo $pi_id_menu;

          ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Agile Release Train (ART):</td>
            <td>
                <select id="art" name="art" onchange="
                //sets art select to selected value
                var art_select = this.value;
                //sets the selected value as the cookie
                document.cookie = escape('artCookie') + '=' + escape(art_select) ;
                //updates the teams list
                getTeams(art_select);
                ">
                <option value="">-- Select --</option>
                <?php echo $art; ?>
              </select>
            </td>
          </tr>
          <tr>
            <td>Teams Menu (AT):</td>
            <td>
                <select id="at" name="at">
                <option value="">-- Select --</option>
                <?php echo $at; ?>
              </select>
            </td>
          </tr>
          <tr>
            <td>Names of Teams:</td>
            <td><input type="text" id="teams" size=100 name="teams" readonly="readonly" value="">
          </td>
        </tr>
        <tr>
          <td><input type="submit" id="js_button" name="generate_button" class="button" value="JS Generate"></td>
          <td><input type="submit" id="php_button" name="generate_button" class="button" value="PHP Generate"></td>
          <td></td>
        </tr>
      </table>
    </form><br><br>
    <?php $db->close(); ?>
<script>
  //assigning the artCookie to a variable
  var artCookie = getCookie('artCookie'); 
  //running the getTeams when the window is loaded using the cookie
  $( window ).on( "load", getTeams(artCookie) );
  function getTeams(art_select){
    //gets values from JSON file
    $.getJSON('dataFiles/at_cache.json', function(data){
      //initializes an array to story the avilable teams
      var at_list = [];
      //for loop for adding team names to teams_list
      var x=data.length;
      for(var i=0; i < x ; i++){
        var parent = data[i].parent_name;
        if(parent == art_select){
          at_list.push(data[i].team_name);
        }
        //updates teams with the calculated list
        document.getElementById('teams').value = at_list;
        //sets teams as a cookie
        document.cookie = escape('teamCookie') + '=' + escape(at_list) ;
      };
    });
  };
  //function for capturing the cookie
  function getCookie(cookieName) {
    var name = cookieName + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
      var c = ca[i];
      while (c.charAt(0) == ' ') {
        c = c.substring(1);
      }
      if (c.indexOf(name) == 0) {
        return c.substring(name.length, c.length);
      }
    }
    return "";
  };
  </script>
