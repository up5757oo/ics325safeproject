<?php

  $nav_selected = "PIPLANNING";
  $left_buttons = "YES"; 
  $left_selected = "SUMMARY";

  include("./nav.php");
  include("./db_connection.php");
  include("./piplanning_capacity.php");
  global $db;

  ?>

  <link rel="stylesheet" type="text/css" href="styleCustom.css">
  <img src="images/work_in_progress.jpg" height = "100" width = "100"/>

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


  <br> * What is the capacity of each ART in the current PI (PI?)
  <br> * What is the cpacity of each TEAM in the current PI (PI)?
  <br> * What is capacity in each Iteration (I)?
  <br> * What is the capacity of the entire org (all ARTS) in the current PI and each of 6 Is?
  <br>
  <br> A datatable showing these numbers will be presented here.
  

<?php include("./footer.php"); ?>
