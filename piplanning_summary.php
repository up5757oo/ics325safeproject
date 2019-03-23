<?php

  $nav_selected = "PIPLANNING";
  $left_buttons = "YES"; 
  $left_selected = "SUMMARY";


  include("./nav.php");
  global $db;

  ?>

  <img src="images/work_in_progress.jpg" height = "100" width = "100"/>
  <h3> Capacity Summary </h3>
  <br> * What is the capacity of each ART in the current PI (PI?)
  <br> * What is the cpacity of each TEAM in the current PI (PI)?
  <br> * What is capacity in each Iteration (I)?
  <br> * What is the capacity of the entire org (all ARTS) in the current PI and each of 6 Is?
  <br>
  <br> A datatable showing these numbers will be presented here.
  
  <?php 
    //Create array place holder for col1 & col2
    include("./db_connection.php");
    $col1= ['1'];
    $col2= ['2'];
    $header_name = 'col1Name';
    buildSummaryTable($header_name,$col1,$col2);
  ?>



<?php include("./footer.php"); ?>
