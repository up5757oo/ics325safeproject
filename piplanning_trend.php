<?php

  $nav_selected = "PIPLANNING";
  $left_buttons = "YES"; 
  $left_selected = "TREND";

  include("./nav.php");
  include("./db_connection.php");
  global $db;

  ?>
<style>
  .floatLeft { width: 48%; float: left; }
  .floatRight {width: 48%; float: right; }
</style>
<link rel="stylesheet" type="text/css" href="styleCustom.css">
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<!--  _______________________________________________________________________ -->
  
  <h3> Bear's Capacity Trend Graph </h3>
  <?php

// PI_ID STUFF
//uses the pi Select Now function to identify the PI ID within the current date and adds it to the pi id select variable for the default
$pi_id_select = piSelectNow();

//capturing the pi id cookie to use for the array and BUILD the menu list
if(isset($_COOKIE['piCookie'])){
  $pi_id = $_COOKIE['piCookie'];
  $pi_id_menu = buildPi_idMenu($pi_id);
} else {
  $pi_id=$pi_id_select;
  $pi_id_menu = buildPi_idMenu($pi_id);
};

?>
<!-- Builds the Pi Drop down to get information for following display tables -->
<form  method="POST" id="PI_form" name="PI_form">
    <table id="form_table" class="container">
<tr>
    <td>Program Increment (PI):</td>
    <td>
      <select id="PI_ID" name="pi_id" onchange="
      //sets pi_select to selected value
      var pi_select = this.value;
      //sets the selected value as the cookie
      document.cookie = escape('piCookie') + '=' + escape(pi_select) ;
      location.reload();
      ">
      <?php echo $pi_id_menu; ?>
    </select>
  </td>
</tr>
<tr>
<td><div id="artPieChart" class="floatLeft" style="width: 500px; height: 500px;"><div></td>
<td><div id="teamPieChart" class="floatRight" style="width: 500px; height: 500px;"><div></td>
</tr>

</table>

  <?php
  buildARTChart($pi_id);
  if(isset($_COOKIE['teamTableCookie'])){
    $pi_id = $_COOKIE['piCookie'];
    $team = $_COOKIE['teamTableCookie'];
    buildTeamChart($pi_id, $team);
  } else {
    '';
  };

  include("./footer.php");

  function buildARTChart($pi_id){
    $db = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_DATABASE);
    $db->set_charset("utf8");
    $sql = "SELECT DISTINCT cap.program_increment, art.parent_name, sum(cap.total) as total
    FROM capacity cap, trains_and_teams art
    WHERE art.team_id = cap.team_id
    AND program_increment='".$pi_id."'
    GROUP BY cap.program_increment, art.parent_name
    ORDER BY cap.program_increment, art.parent_name";

    $result = $db->query($sql);
    echo '<script type="text/javascript">
    google.charts.load(\'current\', {\'packages\':[\'corechart\']});
    google.charts.setOnLoadCallback(drawChart);
    function drawChart() {';
      $artData='[\'Agile Release Train\', \'Total Capacity for PI (Story Points)\']';
      if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
          $artData = $artData.',[\''.$row["parent_name"].'\','.$row["total"].']';
        }
      };
      echo 'var data = google.visualization.arrayToDataTable(['.$artData.']);';
      echo 'var options = {
        title: \'Agile Release Trains for '.$pi_id.'\',
        colors: [\'#6699CC\', \'#003366\', \'#C0C0C0\', \'#000044\', \'#31659C\', \'#639ACE\']
      };

      var chart = new google.visualization.ColumnChart(document.getElementById(\'artPieChart\'));
      function selectHandler() {
        var selectedItem = chart.getSelection()[0];
        if (selectedItem) {
          var art_update = data.getValue(selectedItem.row, 0);
          document.cookie = escape(\'teamTableCookie\') + \'=\' + escape(art_update); 
          location.reload();
        }
      }

      google.visualization.events.addListener(chart, \'select\', selectHandler);
      chart.draw(data, options);
    }
    
  </script>';
  
//--------------------------------------------------------------------------------------

   $sql2 = "SELECT DISTINCT cap.program_increment, sum(cap.total) as final_total
   FROM capacity cap, trains_and_teams art
   WHERE art.team_id = cap.team_id
   AND program_increment='".$pi_id."'
   GROUP BY cap.program_increment
   ORDER BY cap.program_increment, art.team_name;";
   $result2 = $db->query($sql2);
   $final_total ='';
   if ($result2->num_rows > 0) {
       while($row = $result2->fetch_assoc()) {
       $final_total = $row["final_total"];
       }
     }
     if($final_total > 0){
        echo '<div class= "floatLeft">Final Total for '.$pi_id.': '.$final_total.'</div>';
     }
;

   //Returns first alphabetical ART
   $topArtQuery = "SELECT DISTINCT parent_name
   FROM trains_and_teams
   WHERE type='AT'
   ORDER BY parent_name
   LIMIT 1";
   $topArtValue = $db->query($topArtQuery);
   if ($topArtValue->num_rows > 0) {
     while($row = $topArtValue->fetch_assoc()) {
         foreach($row as $key=>$value) {
            setcookie("teamTableCookie", $row["parent_name"]);
         }
     }
   }
};


//Function for building Team Chart
function buildTeamChart($pi_id, $parent_name){
    $db = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_DATABASE);
    $db->set_charset("utf8");
    $sql = "SELECT DISTINCT cap.program_increment, art.team_name, sum(cap.total) as total
    FROM capacity cap, trains_and_teams art
    WHERE art.team_id = cap.team_id
    AND art.parent_name ='".$parent_name."'
    AND program_increment='".$pi_id."'
    GROUP BY cap.program_increment, art.team_name
    ORDER BY cap.program_increment, art.team_name";
   $result = $db->query($sql);

   echo '<script type="text/javascript">
    google.charts.load(\'current\', {\'packages\':[\'corechart\']});
    google.charts.setOnLoadCallback(drawChart);
    function drawChart() {';
      $teamData='[\'Agile Team\', \'Total Capacity for PI (Story Points)\']';
      if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
          $teamData = $teamData.',[\''.$row["team_name"].'\','.$row["total"].']';
        }
      };
      echo 'var data2 = google.visualization.arrayToDataTable(['.$teamData.']);';
      echo 'var options2 = {
        title: \'Agile Teams for '.$parent_name.' in '.$pi_id.'\',
        colors: [\'#6699CC\', \'#003366\', \'#C0C0C0\', \'#000044\', \'#31659C\', \'#639ACE\']
      };
      
      var chart2 = new google.visualization.ColumnChart(document.getElementById(\'teamPieChart\'));

      chart2.draw(data2, options2);
    }
  </script>
  ';
  
   //--------------------------------------------------------------
   $sql2 = "SELECT DISTINCT cap.program_increment, sum(cap.total) as final_total
   FROM capacity cap, trains_and_teams art
   WHERE art.team_id = cap.team_id
   AND art.parent_name ='".$parent_name."'
   AND program_increment='".$pi_id."'
   GROUP BY cap.program_increment
   ORDER BY cap.program_increment, art.team_name;";
   $result2 = $db->query($sql2);
   $final_total ='';
   if ($result2->num_rows > 0) {
       while($row = $result2->fetch_assoc()) {
       $final_total = $row["final_total"];
       }
     }
     if($final_total > 0){
        echo '<div class= "floatRight">Final Total for '.$parent_name.' '.$pi_id.': '.$final_total.'</div>';
     }
};
  ?>
