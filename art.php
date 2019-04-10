<?php


  $nav_selected = "PIPLANNING";
  $left_buttons = "YES";
  $left_selected = "STATUS";

  include("./nav.php");
 //pulls in db_connection.php where the Bears php functions are stored
 require_once("./db_connection.php");
 $art_select = 'PI-1905';
 $selected_team ='Agile Team 1000 1';
 $pi_id = 'PI-1905';
 buildCapacityJSON($art_select,$selected_team,$pi_id);
 //initializes remaining variables
 $art="";

 //devalts the variable using the piSelectNow function from db_connection.php
 $pi_id_select=piSelectNow();

 //Checks for ART Cookie, if it is not available it will update the cookie with a default value using the artCookie function
 if(!isset($_COOKIE['artCookie'])){
   //established finds the value to use for the ART cookie
   $art_select = setArtCookie();
 } else {
   $art_select = $_COOKIE['artCookie'];
 };

 //Function that uses json file to build ART select menu. Updates selected default with the Cookie value
 $art = buildArtMenu($art_select);

 //capturing the pi id cookie to use for the array and build the menu list
 if(isset($_COOKIE['piCookie'])){
   $pi_id = $_COOKIE['piCookie'];
   $pi_id_menu = buildPi_idMenu($pi_id);
 } else {
   $pi_id=$pi_id_select;
   setcookie("piCookie", $pi_id_select);
   $pi_id_menu = buildPi_idMenu($pi_id);
 };
 
 //Sets an array of the program increment values
 $pi_id_array=array($pi_id."-1", $pi_id."-2" ,$pi_id."-3" ,$pi_id."-4", $pi_id."-5",$pi_id."-6",$pi_id."-IP");
 ?>
 
 <!--
   form for submitting data that will be prepopulated with data from the variables
   -->
   <form method="POST" id="PI_form" name="PI_form">
   <table id="form_table" class="container">
       <tr>
           <td>
               <!--Base URL:-->
           </td>
           <td><input type="hidden" id="baseUrl" name="baseUrl" readonly="readonly"
                   value="<?php echo $base_url_out; ?>"></td>
       </tr>
       <tr>
           <td>Program Increment ID:</td>
           <td>
               <select id="PI_ID" name="pi_id" onchange="//sets pi_select to selected value
                                                         var pi_select = this.value;
                                                         //sets the selected value as the cookie
                                                         document.cookie = escape('piCookie') + '=' + escape(pi_select) ;
                                                         location.reload();
                                                         //updated console log for validation
                                                         console.log(getCookie('urlCookie'));
                                                         console.log(getCookie('piCookie'));
                                                         console.log(getCookie('artCookie'));
                                                         console.log(getCookie('teamCookie')); ">
                   <?php echo $pi_id_menu; ?>
               </select>
           </td>
       </tr>
       <tr>
           <td>Agile Release Train (ART):</td>
           <td>
               <select id="art" name="art" onchange="//sets art select to selected value
                                                     var art_select = this.value;
                                                     //sets the selected value as the cookie
                                                     document.cookie = escape('artCookie') + '=' + escape(art_select) ;
                                                     //updates the teams list
                                                     getTeams(art_select);
                                                     location.reload();
                                                     console.log(getCookie('urlCookie'));
                                                     console.log(getCookie('piCookie'));
                                                     console.log(getCookie('artCookie'));
                                                     console.log(getCookie('teamCookie')); ">
                   <option value="">-- Select --</option>
                   <?php echo $art; ?>
               </select>
           </td>
       </tr>
       <tr>
      <td>Agile Team:</td>
      <td>
        <select id="teams" onchange="
      //sets team_select to selected value
      var team_select = this.value;
      //sets the selected value as the cookie
      document.cookie = escape('teamSelectCookie') + '=' + escape(team_select);
      location.reload();">

      </select>
      </td>
    </tr>
   </table>
</form>
  <link rel="stylesheet" type="text/css" href="styleCustom.css">
<p>Iteration 1</p>
<table id="PI1" class="display" width="100%"></table>
<p>Iteration 2</p>
<table id="PI2" class="display" width="100%"></table>
<p>Iteration 3</p>
<table id="PI3" class="display" width="100%"></table>

<script>
   //assigning the artCookie to a variable
   var artCookie = getCookie('artCookie');
    //running the getTeams when the window is loaded using the cookie
    $( window ).on( "load", getTeams(artCookie) );
function getTeams(art_select){
  //gets values from JSON file
  $.getJSON('dataFiles/at_cache.json', function(data){
    //initializes an array to story the avilable teams
      var at_list = [];
      //for loop for adding team names to teams_list
      var x=data.length;
      for(var i=0; i < x ; i++){
        var parent = data[i].parent_name;
        if(parent == art_select){
          at_list.push(data[i].team_name);
        }
        //updates teams with the calculated list
        //document.getElementById('teams').value = at_list;
        var select = document.getElementById("teams");
        select.options.length = 0;
        for(index in at_list) {
          select.options[select.options.length] = new Option(at_list[index], index);
        }
      };
    });
  };

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
 console.log(getCookie('piCookie'));
 console.log(getCookie('artCookie'));
 console.log(getCookie('teamSelectCookie')); 

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

