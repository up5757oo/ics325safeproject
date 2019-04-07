<?php

  $nav_selected = "PIPLANNING";
  $left_buttons = "YES";
  $left_selected = "STATUS";

  include("./nav.php");
  global $db;
  ?>
  <h3> Status </h3>

  <!--Copies in Bears custom stylesheet-->
  <link rel="stylesheet" type="text/css" href="styleCustom.css">
  
  <?php
  //pulls in db_connection.php where the Bears php functions are stored
  require_once("./db_connection.php");

  //pull BASE_URL from json file generated within db_connection.php
  $url_file = file_get_contents("dataFiles/url_cache.json");
  $url_json = json_decode($url_file, true);
  $x=count($url_json);
  for($i = 0; $i < $x; $i++){
    $base_url_out = $url_json[$i]['value'];
  }
  setcookie("urlCookie", $base_url_out);
  
  //initializes remaining variables
  $pi_id="";
  $art="";

  //devalts the variable using the piSelectNow function from db_connection.php
  $pi_id_select=piSelectNow();

  //Checks for ART Cookie, if it is not available it will update the cookie with a default value using the artCookie function
  if(!isset($_COOKIE['artCookie'])){
    //established finds the value to use for the ART cookie
    $art_select = setArtCookie();
  } else {
    $art_select = $_COOKIE['artCookie'];
  };

  //Function that uses json file to build ART select menu. Updates selected default with the Cookie value
  $art = buildArtMenu($art_select);

  //capturing the pi id cookie to use for the array and build the menu list
  if(isset($_COOKIE['piCookie'])){
    $pi_id = $_COOKIE['piCookie'];
    $pi_id_menu = buildPi_idMenu($pi_id);
  } else {
    $pi_id=$pi_id_select;
    setcookie("piCookie", $pi_id_select);
    $pi_id_menu = buildPi_idMenu($pi_id);
  };
  
  //Sets an array of the program increment values
  $pi_id_array=array($pi_id."-1", $pi_id."-2" ,$pi_id."-3" ,$pi_id."-4", $pi_id."-5",$pi_id."-6",$pi_id."-IP");
  ?>
  
  <!--
    form for submitting data that will be prepopulated with data from the variables
    -->
    <form method="POST" id="PI_form" name="PI_form">
    <table id="form_table" class="container">
        <tr>
            <td>
                <!--Base URL:-->
            </td>
            <td><input type="hidden" id="baseUrl" name="baseUrl" readonly="readonly"
                    value="<?php echo $base_url_out; ?>"></td>
        </tr>
        <tr>
            <td>Program Increment ID:</td>
            <td>
                <select id="PI_ID" name="pi_id" onchange="//sets pi_select to selected value
                                                          var pi_select = this.value;
                                                          //sets the selected value as the cookie
                                                          document.cookie = escape('piCookie') + '=' + escape(pi_select) ;
                                                          location.reload();
                                                          //updated console log for validation
                                                          console.log(getCookie('urlCookie'));
                                                          console.log(getCookie('piCookie'));
                                                          console.log(getCookie('artCookie'));
                                                          console.log(getCookie('teamCookie')); ">
                    <?php echo $pi_id_menu; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Agile Release Train (ART):</td>
            <td>
                <select id="art" name="art" onchange="//sets art select to selected value
                                                      var art_select = this.value;
                                                      //sets the selected value as the cookie
                                                      document.cookie = escape('artCookie') + '=' + escape(art_select) ;
                                                      //updates the teams list
                                                      getTeams(art_select);
                                                      location.reload();
                                                      console.log(getCookie('urlCookie'));
                                                      console.log(getCookie('piCookie'));
                                                      console.log(getCookie('artCookie'));
                                                      console.log(getCookie('teamCookie')); ">
                    <option value="">-- Select --</option>
                    <?php echo $art; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Names of Teams:</td>
            <td><input type="text" id="teams" size=100 name="teams" readonly="readonly" value="">
            </td>
        </tr>
    </table>
</form>
<table id="PI_ID_table">
    <thead id="table_header">
        <tr id="header_row">
            <th>No.</th>
            <th>Team Name</th>
        </tr>
    </thead>
    <tbody id="table_body">
    </tbody>
</table>

<script>
    //assigning the artCookie to a variable
    var artCookie = getCookie('artCookie');
    //running the getTeams when the window is loaded using the cookie
    $(window).on("load", getTeams(artCookie));

    function getTeams(art_select) {
        //gets values from JSON file
        $.getJSON('dataFiles/at_cache.json', function (data) {
            //initializes an array to story the avilable teams
            var at_list = [];
            //for loop for adding team names to teams_list
            var x = data.length;
            for (var i = 0; i < x; i++) {
                var parent = data[i].parent_name;
                if (parent == art_select) {
                    at_list.push(data[i].team_name);
                }
                //updates teams with the calculated list
                document.getElementById('teams').value = at_list;
                //sets teams as a cookie
                document.cookie = escape('teamCookie') + '=' + escape(at_list);

            };
        });
    };
    //function for capturing the cookie
    function getCookie(cookieName) {
        var name = cookieName + "=";
        var decodedCookie = decodeURIComponent(document.cookie);
        var ca = decodedCookie.split(';');
        for (var i = 0; i < ca.length; i++) {
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

    //sets the variables with the cookie values
    var baseURL = getCookie('urlCookie');
    var teamList = getCookie('teamCookie');
    var pi_id = getCookie('piCookie');

    /**
     * Creates Arrays for each of the variables
     */
    var teamList_array = teamList.split(",");
    var ID_iteration_array = [pi_id.concat("-1"), pi_id.concat("-2"), pi_id.concat("-3"), pi_id.concat("-4"), pi_id.concat("-5"), pi_id.concat("-6"), pi_id.concat("-IP")];

    /**
     * Updating Table Header
     */
    function createHeader(ID_iteration_array) {
        var i = 0;
        var col_id = "c0";
        for (i = 0; i < ID_iteration_array.length; i++) {
            col_id = "c" + i;
            $('#' + col_id).remove();
            $('#header_row').append('<th id=' + col_id + '>' + ID_iteration_array[i] + '</th>');
        }
    };
    /**
     * function that uses the ID iteration and team list arrays to create the table rows
     */
    function createRows(ID_iteration_array, teamList_array) {
        /**
         * icreating and initializing the variable used by the function
         */
        var r = 0;
        var row_id = 0;
        var row_mod = row_id % 2;
        var c = 0;
        var link = baseURL;
        var table_link = '<td><td>';
        /**
         * removes any existing data in the table
         */
        $('#table_body').empty();
        /**
         * begins the loop that creates the table rows for each of the teams
         */
        for (r = 0; r < teamList_array.length; r++) {
            row_id = r + 1;
            row_mod = r % 2;
            c = 0;
            link = baseURL + '?id=' ;
            uri = ID_iteration_array[c] + '_' + teamList_array[r] + '"';
            var encodedUri = encodeURIComponent(uri);
            console.log(encodedUri);
            table_link = '<td ><a href = "' + link + encodedUri+'" title="'+link+encodedUri+'">' + ID_iteration_array[c] + '</a></td>';
            $('#table_body').append('<tr class="altrow" id="' + row_id + '" ><td>' + row_id + '</td><td>' + teamList_array[r] + '</td>');
                for (c = 0; c < ID_iteration_array.length; c++) {
                    $('#' + row_id).append(table_link);
                }
            $('#' + row_id).append('</tr>');
          }
        };

    /**
     * Initial run of functions to generate the default table
     */
    createHeader(ID_iteration_array);
    createRows(ID_iteration_array, teamList_array);

</script>

<?php
if(!isset($_COOKIE['teamCookie'])){
  //refreshed the page to update the cookie for the table values
  echo '<script>location.reload();</script>';
};

?>
  <?php 
  //closes the database and adds the footer
  $db->close(); 
  include("./footer.php"); 
  ?>
