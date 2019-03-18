<?php

$nav_selected = "PIPLANNING";
$left_buttons = "YES";
$left_selected = "CAPACITY";

include("./nav.php");
global $db;
?>

<link rel="stylesheet" type="text/css" href="styleCustom.css">

<h3>Capacity Calculator</h3>

<?php
$page_title = 'Capacity Calculator<';

include("./db_connection.php");

//pull BASE_URL from json file
$url_file = file_get_contents("dataFiles/url_cache.json");
$url_json = json_decode($url_file, true);
$x=count($url_json);
for($i = 0; $i < $x; $i++){
  $base_url_out = $url_json[$i]['value'];
}
//initializes remaining variables
$pi_id="";
$art="";
$generate_button='New';
$pi_id_menu='';

//Function from db_connection that checks for ART Cookie, if it is not available it will update the cookie with a default value
setArtCookie();

//Function that uses json file to build ART select menu. Updates selected default with the Cookie value
$art = buildArtMenu();

//uses PI ID json file to build program increment table with the current program intrement id identified through a sql query
$pi_id_file = file_get_contents("dataFiles/pi_id_cache.json");
$pi_id_json = json_decode($pi_id_file, true);
$x=count($pi_id_json);
$pi_id_now_query = "SELECT PI_id FROM cadence where DATE(NOW()) between start_date and end_date + 2";
$pi_id_select_results = mysqli_query($db, $pi_id_now_query);
if ($pi_id_select_results->num_rows > 0) {
  while($pi_id_now = $pi_id_select_results->fetch_assoc()) {
    $pi_id_select = $pi_id_now["PI_id"];
  }//end while
}//end if
$pi_id_menu='';
for($i = 0; $i < $x; $i++){
  $pi_id_item = $pi_id_json[$i]['PI_id'];
  if($pi_id_item===$pi_id_select){
    $pi_id_menu = $pi_id_menu.'<option value="'.$pi_id_item.'" selected>'.$pi_id_item.'</option>';
  } else{
    $pi_id_menu = $pi_id_menu.'<option value="'.$pi_id_item.'">'.$pi_id_item.'</option>';
  }
};
$pi_id_array=array($pi_id."-1", $pi_id."-2" ,$pi_id."-3" ,$pi_id."-4", $pi_id."-5",$pi_id."-6",$pi_id."-IP");


$sql5 = "SELECT * FROM `cadence` WHERE PI_id='".$pi_id_select."';";
$result5 = $db->query($sql5);
if ($result5->num_rows > 0) {
    $row5 = $result5->fetch_assoc();
    $duration = $row5["duration"];
}
$sql6 = "SELECT * FROM `preferences` WHERE name='OVERHEAD_PERCENTAGE';";
$result6 = $db->query($sql6);
if ($result6->num_rows > 0) {
    $row6 = $result6->fetch_assoc();
    $overhead_percentage = $row6["value"];
}
?>

<!--
form for submitting data that will be prepopulated with data from the variables
-->
  <form  method="POST" id="PI_form" name="PI_form">
    <table id="form_table" class="container">
    <tr>
      <td>
      <!--Base URL:-->
      </td>
      <td>
      <input type="hidden" id="baseUrl" name="baseUrl" readonly="readonly" value="<?php
      echo $base_url_out;
      ?>">
      </td>
    </tr>
    <tr>
      <td>Agile Release Train:</td>
      <td>
      <select id="art" name="art" onchange="
      //sets art select to selected value
      var art_select = this.value;
      //sets the selected value as the cookie
      document.cookie = escape('artCookie') + '=' + escape(art_select) ;
      //updates the teams list
      getTeams(art_select);
      ">
      <option value="">-- Select --</option>
      <?php echo $art; ?>
      </select>
      </td>
    </tr>
    <tr>
      <td>Agile Team:</td>
      <td>
      <select id="teams"></select>
      </td>
      </tr>
      <td>Program Increment (PI):</td>
      <td>
      <select id="PI_ID" name="pi_id">
      <?php
      echo $pi_id_menu;
      ?>
      </select>
      </td>
    </tr>
    <tr>
      <td><!--input type="submit" id="js_button" name="generate_button" class="button" value="JS Generate"--></td>
      <td><input type="submit" id="php_button" name="generate_button" class="button" value="Generate"></td>
      <td></td>
    </tr>
    </table>
  </form><br>
    <?php //$db->close(); ?>
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
        //sets teams as a cookie
        //document.cookie = escape('teamCookie') + '=' + escape(at_list) ;
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
  </script>

<!-- TABLE GENERATION STUFF - FOR LATER -->


<form method="post" action="#" id="maincap">
      <table id="info" cellpadding="2px" cellspacing="0" border="0" class="capacity-table"
             width="100%" style="width: 100%; clear: both; font-size: 15px; margin: 8px 0 15px 0">

          <thead>
          <tr id="capacity-table-first-row">

              <th id="capacity-table-td">Last Name</th>
              <th id="capacity-table-td">First Name</th>
              <th id="capacity-table-td">Role</th>
              <th id="capacity-table-td">% Velocity Available</th>
              <th id="capacity-table-td">Days Off <br/><p style="font-size: 9px;">(Vacation / Holidays / Sick Days)</p></th>
              <th id="capacity-table-td">Story Points</th>

          </tr>
          </thead>
          <tbody>

          <?php
          $selected_team="805 Agile Team";
          $sql = "SELECT last_name, first_name, employee_name, role FROM `membership`
                  NATURAL JOIN `employees`
                  WHERE team_name='".$selected_team."';";

          $result = $db->query($sql);

          if ($result->num_rows > 0) {
            // output data of each
            $rownum = 0;
            while ($row = $result->fetch_assoc()) {

              if ($row["role"] == "Scrum Master (SM)") {
                $velocityType = "SCRUM_MASTER_ALLOCATION";
              } else if ($row["role"] == "Product Owner (PO)") {
                $velocityType = "PRODUCT_OWNER_ALLOCATION";
              } else  {
                $velocityType = "AGILE_TEAM_MEMBER_ALLOCATION";
              }

              $sql2 = "SELECT * FROM `preferences` WHERE name='".$velocityType."';";
              $result2 = $db->query($sql2);

              if ($result2->num_rows > 0) {

                  $row2 = $result2->fetch_assoc();

              }
              if (isset($teamcapacity[$rownum]) && !isset($_POST['restore']) && isset($_POST['submit0'])){
                $storypts = $teamcapacity[$rownum];
              }else{
                $storypts = round(($duration-0)*((100-$overhead_percentage)/100)*($row2["value"]/100));
              }
              $valueForJS = $row2["value"];
              if (isset($daysoff[$rownum]) && !isset($_POST['restore'])  && isset($_POST['submit0'])){
                $doff = $daysoff[$rownum];
              } else {
                $doff = 0;
              }
              if (isset($velocity[$rownum]) && !isset($_POST['restore']) && isset($_POST['submit0'])){
                $vel = $velocity[$rownum];
              } else {
                $vel = $row2["value"];
              }

                  echo
                  "<tr>
                      <td id='capacity-table-td' style='font-weight:500;'>" . $row["last_name"] . "</td>
                      <td id='capacity-table-td' style='font-weight:500;'>" . $row["first_name"] . "</td>
                      <td id='capacity-table-td' style='font-weight:500;'>" . $row["role"] . "</td>
                      <td id='capacity-table-td' style='font-weight:500; text-align: center;'><input id='autoin' class='capacity-text-input' type='text' name='velocity[]' value='" . $vel . "' submit='autoLoad();' /> %</td>
                      <td id='capacity-table-td' style='font-weight:500; text-align: center;'><input id='autoin2' class='capacity-text-input' type='text' name='daysoff[]' value='".$doff."' submit='autoLoad();' /></td>
                      <td id='capacity-table-td' style='font-weight:500; text-align: center;  background: #e9e9e9;'><input id='story' class='capacity-text-input' type='text' name='storypoints[]' value='".$storypts."' readonly='readonly' style='border: 0;  background: #e9e9e9;' />&nbsp;pts</td>
                      <input type='hidden' name='rownum[]' value='".$rownum."'/>
                  </tr>";
                  $rownum++;
              }
          } else {
            echo "<tr><td colspan='6' id='capacity-table-td'  style='text-align: center; font-weight: bold; padding: 20px 0 20px 0'>";
              print "NO TEAM MEMBERS ASSIGNED TO TEAM \"".$selected_team."\"";
              echo "</td></tr>";
          }

          //$result->close();
          ?>

          </tbody>

          <tfoot>

          </tfoot>

      </table>
      <input type="submit" id="capacity-button-blue" name="submit0" value="Submit">
      <input type="submit" id="capacity-button-blue" name="restore" value="Restore Defaults">
      <input type="submit" id="capacity-button-blue" name="showNext" value="Show Next iteration_id">
      <input type="hidden" name="current-team-selected" value="<?php echo $selected_team; ?>">
      <input type="hidden" name="current-sequence" value="<?php echo $sequence; ?>">
      </form>

      </td>
      </tr>
      </table>

    </div>
    </div>

    <script type="text/javascript">

        $(document).ready(function () {

            $('#info').DataTable({
                paging: false,
                searching: false,
                infoCallback: false
            });

        });

        function autoForm() {
          document.getElementById('maincap').submit();
        }

        function autoLoad() {
          var velocity = $("input[name='velocity[]']")
              .map(function(){return $(this).val();}).get();
          var daysoff = $("input[name='daysoff[]']")
              .map(function(){return $(this).val();}).get();
          var rownum = $("input[name='rownum[]']")
              .map(function(){return $(this).val();}).get();

          var overhead = "<?php echo $overhead_percentage ?>";
          var duration = "<?php echo $duration ?>";
          var value = "<?php echo $valueForJS ?>";
          var totalcap_old = "<?php echo $totalcapacity ?>";
          var icap_old = "<?php echo $icapacity ?>";
          var icap = 0;

          for (var i in rownum) {
              var storypts = Math.round( ( duration - daysoff[i] ) * ( ( 100-overhead ) / 100 ) * ( velocity[i] / 100 ) );
              $("input[name='storypoints[]']").eq(i).val(storypts);
              icap += storypts;
          }

          document.getElementsByName("icap")[0].innerHTML = icap;
          var capdiff = icap - icap_old;
          var tcap = parseInt(capdiff) + parseInt(totalcap_old);
          document.getElementsByName("totalcap")[0].innerHTML = tcap;
        }


    </script>

<?php include("./footer.php"); ?>
