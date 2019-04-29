<?php

  $nav_selected = "PIPLANNING";
  $left_buttons = "YES";
  $left_selected = "CALCULATE";

  include("./nav.php");
  include("./db_connection.php");
  global $db;
   $db = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
   $db->set_charset("utf8");


  echo'<!--Copies in Bears custom stylesheet-->
  <link rel="stylesheet" type="text/css" href="styleCustom.css">';
  //Checks for ART Cookie, if it is not available it will update the cookie with a default value using the artCookie function
//initializes remaining variables
$program_increment="";
$art="";
$program_increment_menu='';
$program_increment_select='';
$duration = '';
$overhead_percentage = getOverheadPercentage();
$default_total = 56;

  //Checks for ART Cookie, if it is not available it will update the cookie with a default value using the artCookie function
  if(!isset($_COOKIE['artCookie'])){
    //established finds the value to use for the ART variable and sets the cookie
    $art_select = setArtCookie();
  } else {
    $art_select = $_COOKIE['artCookie'];
  };

  //checks if a team has been selected. If it has not then if finds the default team name and applies it to the team variable
  if(!isset($_COOKIE['teamSelectCookie'])){
  //sets the default team name
  $team = getDefaultTeamName($art_select);
  $selected_team = getTeamID($team);
  setcookie('teamSelectCookie', $selected_team );
  } else {
    $selected_team  = $_COOKIE['teamSelectCookie'];
  };

//finds the team id for the team name for the selected team script

//Function to build ART select menu. Updates selected default with the Cookie value
$art = buildArtMenu($art_select);

//uses the pi Select Now function to identify the PI ID within the current date and adds it to the pi id select variable for the default
$program_increment_select = piSelectNow();

//capturing the pi id cookie to use for the array and build the menu list
if(isset($_COOKIE['piCookie'])){
  $program_increment = $_COOKIE['piCookie'];
  $program_increment_menu = buildPi_idMenu($program_increment, true);
} else {
  //if a cookie is not found it uses the current PI for the select menu and adds it to the cookie
  $program_increment=$program_increment_select;
  setcookie('piCookie', $program_increment_select);
  $program_increment_menu = buildPi_idMenu($program_increment, true);
};
//assigning duration with a default value
$duration = 10;
//initializes the totalcapacity variable
$sql = "SELECT * FROM `capacity` WHERE program_increment='".$program_increment."' AND team_id='".$selected_team."'";
$result = $db->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if (isset($teamcapacity)  && !isset($_POST['restore'])  && !isset($_POST['submit0'])){
      $totalcapacity = $row["total"] ;
    }else{
      $totalcapacity = $row["total"];
    }
} else {

  if (!isset($teamcapacity)  && !isset($_POST['restore'])  && !isset($_POST['submit0'])){
    $totalcapacity = ($default_total*6);
  }else{
    $totalcapacity = $default_total*6;
  }
};
//Function for assigning the overhead percentage
$overhead_percentage = getOverheadPercentage();

  //Creates an array of the active sequences and iterations to use for loops that will build the tables and Javascripts for each iteration
  $sequenceArray = array();
  $iterationArray = array();
  if ($result = $db->query("SELECT sequence, iteration_id as iteration FROM `cadence` WHERE PI_id ='".$program_increment."';")) {
    $rows = array();
    while($row = $result->fetch_array()) {
      $sequenceArray[]=$row["sequence"];
      $iterationArray[]=$row["iteration"];
    }
  };
  $count_iteration = count($iterationArray);

?>

<!--
form for submitting data that will be prepopulated with data from the variables
-->
<div class="right-content" >
    <div class="container">
  <form  method="POST" id="PI_form" name="PI_form">
    <table id="form_table" class="container">
    <tr>
<div style="float: right; margin-right: 10px; text-align: center; font-size: 12px;">
              <div id="capacity-calc-bignum" name="totalcap"><?php echo $totalcapacity ?></div>
              <b>Total Capacity for the Program Increment</b>
            </div>
          </td>

</td></tr>

<div style="float: left; text-align: center; font-size: 12px;">

      <tr>
        <td>Agile Release Train:</td>
        <td>
          <select id="art" name="art" onchange="
          //sets art select to selected value
          var art_select = this.value;
          //sets the selected value as the cookie
          document.cookie = escape('artCookie') + '=' + escape(art_select) ;
          location.reload();
          ">
          <option value="">-- Select --</option>
          <?php echo $art; ?>
        </select>
      </td>
    </tr>
    <tr>
    <td>Program Increment (PI):</td>
    <td>
      <select id="PI_ID" name="pi_id" onchange="
      //sets pi_select to selected value
      var pi_select = this.value;
      //sets the selected value as the cookie
      document.cookie = escape('piCookie') + '=' + escape(pi_select) ;
      location.reload();">
      <?php echo $program_increment_menu; ?>
    </select>
  </td>
</tr>
<tr>
            <td>Names of Teams:</td>
            <td><select name="select-team" onchange="
            //sets team_select to selected value
            var team_select = this.value;
            //sets the selected value as the cookie
            document.cookie = escape('teamSelectCookie') + '=' + escape(team_select);
            location.reload();" >
              <?php

              $sql = "SELECT DISTINCT t.team_id, t.team_name FROM trains_and_teams t where t.parent_name = '".$art_select."';";
              //checks if there is a selected team in the cookie variable. If there is it will update the detault to the cookie value
              if(isset($_COOKIE['teamSelectCookie'])){
                $selected_team = $_COOKIE['teamSelectCookie'];
              }
              $result = $db->query($sql);

              if ($result->num_rows > 0) {

                  while ($row = $result->fetch_assoc()) {
                    if ( trim($selected_team) == trim($row["team_id"]) ) {
                      echo '<option value="'.$row["team_id"].'" selected>'.$row["team_name"].'</option>';
                    }else{
                      echo '<option value="'.$row["team_id"].'">'.$row["team_name"].'</option>';
                    }

                  }
              }
              ?>
            </select>
            </td>
        </tr>
<tr>
<td><input type="submit" id="php_button" name="generate_button" class="button" value="Generate"></td>
<td><input type="hidden" name="current-team-selected" value="<?php echo $selected_team; ?>"></td>
</tr>

</table>
</form><br>


<script>

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
    ("PI Cookie: " + getCookie('piCookie'));
    ("ART Cookie: "+getCookie('artCookie'));
    ("Team Cookie: " + getCookie('teamSelectCookie'));
  </script>
  <?php

  date_default_timezone_set('America/Chicago');
  echo '<div class="right-content" >
    <div class="container">

      <h3 style=" color: #01B0F1; font-weight: bold;">Capacity Calculations for the Agile Team</h3>';



  //Loop for displaying the series of Employee table & iteration calculation placeholder
  for($i = 0; $i < $count_iteration; $i++){
    creatTables($program_increment, $selected_team, $iterationArray[$i], $sequenceArray[$i], $overhead_percentage);
  };

  /*//updated sql so select values matched availabe column names
  $sql = "SELECT sequence, PI_id as program_increment, iteration_id as iteration , sequence
  FROM `cadence`
  WHERE PI_id in (SELECT  PI_id
  FROM `cadence`
  WHERE start_date <= DATE(NOW())
  AND end_date >= DATE(NOW())
  order by sequence);";
  $result = $db->query($sql);
  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $program_increment = $row["program_increment"];
    $iteration = $row["iteration"];
    $sequence = $row["sequence"];
    $result->close();
  } else {
    echo "No Available Iterations available for Today's date";
    $result->close();

    $sql = "SELECT *
        FROM
        (	SELECT MIN(start_date) as start_date, MAX(end_date) as end_date
          FROM cadence
          WHERE start_date <= DATE(NOW())
          OR end_date >= end_date >= DATE(NOW())
          GROUP BY program_increment
        ) as PI
        WHERE PI.start_date <= DATE(NOW())
        AND PI.end_date >= DATE(NOW());";
    $result = $db->query($sql);
    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $start_date = $row["start_date"];
      $end_date = $row["end_date"];
    } else {
      //echo "In-between Program Increments";
    }
    $result->close();
  }*/


  //echo '<script>('.$sequence.');</script>';
  //checks if there is a current team selected. If not it uses the artCookie to find the $selected_team
  //if ($_REQUEST['generate_button']) {
      //$selected_team = $_POST['current-team-selected'];

 // } ;
/*
  if (isset($_POST['showNext'])) {
    $sequence++;
    echo '<script>("Show Next: " + "'.$sequence.'");</script>';
    echo '<script>("Program Increment: " + "'.$program_increment.'");</script>';

    $sql = "SELECT sequence, PI_id as program_increment, iteration_id as iteration
            FROM `cadence`
            WHERE sequence='".$sequence."';";
    $result = $db->query($sql);
    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $program_increment = $row["program_increment"];
      $iteration = $row["iteration"];
      $sequence = $row["sequence"];
      $result->close();
    } else {
      $sql = "SELECT sequence, PI_id as program_increment, iteration_id as iteration
              FROM `cadence`
              WHERE PI_id='".$program_increment."'
              ORDER BY sequence limit 1;";
      $result = $db->query($sql);
      if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $program_increment = $row["program_increment"];
        $iteration = $row["iteration"];
        $sequence = $row["sequence"];
        $result->close();
    }
  }
  ///////////////////////////////////////////////////////////////////////////////////////////////////////
  echo '<script>("Program Increment: " + "'.$iteration.'");</script>';
    $sql = "SELECT * FROM `capacity` where team_id='".$selected_team."' AND program_increment='".$program_increment."';";
    $result = $db->query($sql);
    if ($result->num_rows > 0) {
      $default_data = false;
      $default_total = ($row["iteration_1"] + $row["iteration_2"] + $row["iteration_3"] + $row["iteration_4"]+ $row["iteration_5"] + $row["iteration_6"] + $row["iteration_P"]);
    } else {
      $default_data = true;

      $sql = "SELECT * FROM `membership` where team_name = (select team_name from trains_and_teams where team_id = '".$selected_team."' and art_name = '".$art_name."' LIMIT 1) ;";
      $result = $db->query($sql);
      if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {

          if ($row["role"] == "SM") {
            $velType = "SCRUM_MASTER_ALLOCATION";
          } else if ($row["role"] == "PO") {
            $velType = "PRODUCT_OWNER_ALLOCATION";
          } else  {
            $velType = "AGILE_TEAM_MEMBER_ALLOCATION";
          }

          $sql2 = "SELECT * FROM `preferences` WHERE name='".$velType."';";
          $result2 = $db->query($sql2);

          if ($result2->num_rows > 0) {

              $row2 = $result2->fetch_assoc();
              $default_total += $row2["value"];

          }
        }
      }
    }
  }
  if (isset($_POST['select-team'])) {
    $selected_team = $_POST['select-team'];
    //$default_total = 56;
    $sql = "SELECT * FROM `capacity` where team_id='".$selected_team."' AND program_increment='".$program_increment."';";
    $result = $db->query($sql);
    if ($result->num_rows > 0) {
    } else {
      $default_data = true;
      //$default_total = ($defaul_total * 5) + 28;
      if(isset($_COOKIE['artCookie'])){
        $art_name=$_COOKIE['artCookie'];
      } else {
        $art_name = setArtCookie();
      }

      $sql = "SELECT * FROM `membership` where team_name = (select team_name from trains_and_teams where team_id = '".$selected_team."' and art_name = '".$art_name."' LIMIT 1);";
      $result = $db->query($sql);
      if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {

          if ($row["role"] == "SM") {
            $velType = "SCRUM_MASTER_ALLOCATION";
          } else if ($row["role"] == "PO") {
            $velType = "PRODUCT_OWNER_ALLOCATION";
          } else  {
            $velType = "AGILE_TEAM_MEMBER_ALLOCATION";
          }

          $sql2 = "SELECT * FROM `preferences` WHERE name='".$velType."';";
          $result2 = $db->query($sql2);

          if ($result2->num_rows > 0) {

              $row2 = $result2->fetch_assoc();
              $default_total += $row2["value"];

          }
        }
      }
    }
  }
  if (!isset($_POST['select-team']) && !isset($_POST['current-team-selected'])) {
    $sql = "SELECT team_id FROM `capacity` where program_increment='".$program_increment."' LIMIT 1;";
    $result = $db->query($sql);

    if ($result->num_rows > 0) {

        $row = $result->fetch_assoc();
        $selected_team = $row["team_id"];
    }
  }

  $sql5 = "SELECT * FROM `cadence` WHERE PI_id='".$program_increment."';";
  $result5 = $db->query($sql5);
  if ($result5->num_rows > 0) {
      $row5 = $result5->fetch_assoc();
      $duration = $row5["duration"];
  }
  $sql6 = "SELECT * FROM `preferences` WHERE name='OVERHEAD_PERCENTAGE';";
  $result6 = $db->query($sql6);
  if ($result6->num_rows > 0) {
      $row6 = $result6->fetch_assoc();
      $overhead_percentage = $row6["value"];
  }


  if (isset($_POST['submit0'])) {
    $iterationcapacity = 0;
    for ($x=0; $x < count($_POST['rownum']); $x++){
      $teamcapacity[$_POST['rownum'][$x]] = round(($duration-$_POST['daysoff'][$x])*((100-$overhead_percentage)/100)*($_POST['velocity'][$x]/100));
      $iterationcapacity += $teamcapacity[$_POST['rownum'][$x]];
      $daysoff[$_POST['rownum'][$x]] = $_POST['daysoff'][$x];
      $velocity[$_POST['rownum'][$x]] = $_POST['velocity'][$x];
    }
    $sqliter = "UPDATE `capacity` SET iteration_".substr($iteration, -1)."='".$iterationcapacity."' WHERE program_increment='".$program_increment."' AND team_id='".$selected_team."';";
    $result_iter = $db->query($sqliter);
    $sqlinc = "SELECT (iteration_1 + iteration_2 + iteration_3 + iteration_4 + iteration_5 + iteration_6) as new_total FROM `capacity` WHERE program_increment='".$program_increment."' AND team_id='".$selected_team."';";
    $result_inc = $db->query($sqlinc);
    if ($result_inc->num_rows > 0) {
        $rowinc = $result_inc->fetch_assoc();
        $pi_capacity = $rowinc["new_total"];
      }
    $sqlup = "UPDATE `capacity` SET total='$pi_capacity' WHERE program_increment='".$program_increment."' AND team_id='".$selected_team."';";
    $result_up = $db->query($sqlup);

    // keep velocity and days off value changes
    $iterationcapacity = 0;
    for ($x=0; $x < count($_POST['rownum']); $x++){
      $teamcapacity[$_POST['rownum'][$x]] = round(($duration-$_POST['daysoff'][$x])*((100-$overhead_percentage)/100)*($_POST['velocity'][$x]/100));
      $iterationcapacity += $teamcapacity[$_POST['rownum'][$x]];
      $daysoff[$_POST['rownum'][$x]] = $_POST['daysoff'][$x];
      $velocity[$_POST['rownum'][$x]] = $_POST['velocity'][$x];
    }
  }

*/


?>
      <div id="capacity-footnote">
        Note 1: Closed Iterations will NOT be shown.  The capacity cannot be changed for such iterations.  Show only the active iterations.<br/>
        Note 2: This page can be reached in two ways:
        <ul>
          <li>Capacity > Calculate</li>
          <li>Capacity > Summary > By clicking on one of the numbers</li>
        </ul>
      </div>

      </td>
      </tr>
      </table>

    </div>
    </div>


  <?php
  $db->close();

?>
<?php include("./footer.php"); ?>
