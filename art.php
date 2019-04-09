<?php

  $nav_selected = "PIPLANNING";
  $left_buttons = "YES";
  $left_selected = "STATUS";

  include("./nav.php");
  ?>
  <link rel="stylesheet" type="text/css" href="styleCustom.css">

<table id="PI1" class="display" width="100%"></table>
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
 

$(document).ready(function() {
  $.getJSON('dataFiles/PI-1905_ART-500_Agile+Team+1000+1_cache.json', function(data){
      //initializes an array to story the avilable teams
      var jsonDataSet = [];
      //for loop for adding team names to teams_list
      var x=data.length;
      for(var i=0; i < x ; i++){
          jsonDataSet.push(data[i]);
        }
 console.log(jsonDataSet);
    $('#PI1').DataTable( {
        data: jsonDataSet,
        columns: [
            { title: "First Name" },
            { title: "Last Name" },
            { title: "Velocity" },
            { title: "Days Off." },
            { title: "Story Points" },
        ]
    } );
} );
});
  </script>
    <?php 
  include("./footer.php"); 
  ?>

