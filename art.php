<?php
$art_select = 'PI-1905';
$selected_team ='Agile Team 1000 1';
$pi_id = 'PI-1905';
buildCapacityJSON($art_select,$selected_team,$pi_id);

  $nav_selected = "PIPLANNING";
  $left_buttons = "YES";
  $left_selected = "STATUS";

  include("./nav.php");
  ?>
  <link rel="stylesheet" type="text/css" href="styleCustom.css">
<p>Iteration 1</p>
<table id="PI1" class="display" width="100%"></table>
<p>Iteration 2</p>
<table id="PI2" class="display" width="100%"></table>
<p>Iteration 3</p>
<table id="PI3" class="display" width="100%"></table>

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
      var pi1DataSet = [];
      var pi2DataSet = [];
      var pi3DataSet = [];
      var pi4DataSet = [];
      var pi5DataSet = [];
      var pi6DataSet = [];
      var piIPDataSet = [];
      
      //for loop for adding team names to teams_list
      var x=data.length;
      for(var i=0; i < x ; i++){
        var iterationNumber = data[i].iteration_id;
        if(iterationNumber == 'I-1905-1'){
          pi1DataSet.push(data[i]);
        }
        if(iterationNumber == 'I-1905-2'){
          pi2DataSet.push(data[i]);
        }    
        if(iterationNumber == 'I-1905-3'){
          pi3DataSet.push(data[i]);
        }     
        if(iterationNumber == 'I-1905-4'){
          pi4DataSet.push(data[i]);
        }
        if(iterationNumber == 'I-1905-5'){
          pi5DataSet.push(data[i]);
        }    
        if(iterationNumber == 'I-1905-6'){
          pi6DataSet.push(data[i]);
        } 
        if(iterationNumber == 'I-1905-IP'){
          piIPDataSet.push(data[i]);
        } 
        }
 console.log(pi1DataSet);
 console.log(pi2DataSet);
 console.log(pi3DataSet);
 console.log(pi4DataSet);
 console.log(pi5DataSet);
 console.log(pi6DataSet);
 console.log(piIPDataSet);

 $('#PI1').DataTable( {
        data: pi1DataSet,
        paging: false,
        searching: false,
        infoCallback: false,
        columns: [
            { title: "First Name", data: "first_name" },
            { title: "Last Name", data: "last_name"  },
            { title: "Role", data: "role"  },
            { title: "Velocity", data: "velocity"  },
            { title: "Days Off", data: "Days_Off"  },
            { title: "Story Points", data: "Story_points"  },
        ]
    } );
 

    $('#PI2').DataTable( {
        data: pi2DataSet,
        paging: false,
        searching: false,
        infoCallback: false,
        columns: [
            { title: "id", data: "iteration_id" },
            { title: "First Name", data: "first_name" },
            { title: "Last Name", data: "last_name"  },
            { title: "Role", data: "role"  },
            { title: "Velocity", data: "velocity"  },
            { title: "Days Off", data: "Days_Off" , edit: true },
            { title: "Story Points", data: "Story_points"  },
        ]
    } );
} );
});
  </script>
    <?php 
  include("./footer.php"); 
  ?>

