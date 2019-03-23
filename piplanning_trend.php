<?php

  $nav_selected = "PIPLANNING";
  $left_buttons = "YES"; 
  $left_selected = "TREND";

  include("./nav.php");
  include("./db_connection.php");
  global $db;

  ?>

<link rel="stylesheet" type="text/css" href="styleCustom.css">
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
      document.cookie = escape('piCookie') + '=' + escape(pi_select) ;">
      <?php echo $pi_id_menu; ?>
    </select>
  </td>
</tr>

<!-- END PI_STUFF -->
  <br> * What is the capacity of each ART in the past Program Increment (PI)?
  <br> * How is the trend looking?
  <br> * What is the total capacity of all ARTs at each PI?
  <br> * We will show a comparison / trend and summary on this page.
  
<?php include("./footer.php"); ?>
