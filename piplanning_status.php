<?php

  $nav_selected = "PIPLANNING";
  $left_buttons = "YES"; 
  $left_selected = "STATUS";

  include("./nav.php");
  global $db;

  ?>

  <style>
  h3{
    background-color: navy;
    color: white;

  }

  </style>

  <h3> Status </h3>

  <?php include("./art.php"); ?>
  <?php include("./art_table.php"); ?>
  <?php include("./footer.php"); ?>