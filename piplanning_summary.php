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
      document.cookie = escape('piCookie') + '=' + escape(pi_select) ;
      location.reload();
        ">
      <?php echo $pi_id_menu; ?>
    </select>
  </td>
</tr>

<!-- END PI_STUFF -->

  <?php
  buildARTTable($pi_id);

  include("./footer.php");

  ?>
