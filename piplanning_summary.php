<style>
  .floatLeft { width: 48%; float: left; }
  .floatRight {width: 48%; float: right; }
</style>

<?php

  $nav_selected = "PIPLANNING";
  $left_buttons = "YES";
  $left_selected = "SUMMARY";

  include("./nav.php");
  include("./db_connection.php");
  global $db;

  ?>

  <link rel="stylesheet" type="text/css" href="styleCustom.css">

<!--  _______________________________________________________________________ -->

  <h3> Bear's Capacity Summary </h3>
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
      document.cookie = escape('piCookie') + '=' + escape(pi_select) ;">
      <?php echo $pi_id_menu; ?>
    </select>
  </td>
</tr>

<!-- END PI_STUFF -->

<?php include("./footer.php"); ?>

  <?php

    //Agile Release Trains Table
    $sql = "SELECT DISTINCT parent_name
    FROM trains_and_teams
    WHERE type='AT'
    ORDER BY parent_name";

    $result = $db->query($sql);

    echo "<table class='floatLeft'>";
    echo "<th style='text-align: center; background-color: grey'; colspan='2'>Agile Release Trains</th>";
    echo "<tr>";
    echo "<th>Agile Release Train</th>";
    echo "<th>Total Capacity for PI (Story Points)</th>";

    if ($result->num_rows > 0) {
      // output data of each row
      while($row = $result->fetch_assoc()) {
          echo '<tr>';
          foreach($row as $key=>$value) {
            echo '<td>'.$row["parent_name"].'</td>';
          }

          $sql2 = "SELECT SUM(total)
          FROM capacity
          WHERE (team_name='Agile Team 1000 1' OR team_name ='Agile Team 1000 6') AND program_increment='".$pi_id."'";
          $result2 = $db->query($sql2);

          while($row2 = $result2->fetch_assoc()) {
              foreach($row2 as $key=>$value) {
                echo '<td>'.$row2["SUM(total)"].'</td>';
              }
            }

          echo '</tr>';
      }
    } 
    echo "</table>";

      //Agile Teams Table
      $topArtQuery = "SELECT DISTINCT parent_name 
      FROM trains_and_teams 
      WHERE type='AT' 
      ORDER BY parent_name 
      LIMIT 1";

      $topArtValue = $db->query($topArtQuery);

      if ($topArtValue->num_rows > 0) {
        while($row = $topArtValue->fetch_assoc()) {
            foreach($row as $key=>$value) {
              $topArtOutput = $row["parent_name"];
            }
        }
      } 

      $sql = "SELECT DISTINCT team_name
      FROM trains_and_teams
      WHERE parent_name='".$topArtOutput."'
      ORDER BY team_name";
      $result = $db->query($sql);

    echo "<table class='floatRight'>";
    echo "<th style='text-align: center; background-color: grey'; colspan='2'>Agile Teams</th>";
    echo "<tr>";
    echo "<th>Agile Train</th>";
    echo "<th>Total Capacity for PI (Story Points)</th>";

    if ($result->num_rows > 0){
      // output data of each row
      while($row = $result->fetch_assoc()) {
          echo '<tr>';
          foreach($row as $key=>$value) {
            echo '<td>'.$row["team_name"].'</td>';
          }
          
          $sql2 = "SELECT SUM(total)
          FROM capacity
          WHERE team_name ='".$row["team_name"]."' AND program_increment='".$pi_id."'";
          $result2 = $db->query($sql2);

      while($row2 = $result2->fetch_assoc()) {
          foreach($row2 as $key=>$value) {
            echo '<td>'.$row2["SUM(total)"].'</td>';
          }
          
        }
          echo '</tr>';
      }
    } 
  
    echo "</table>";
  ?>
  