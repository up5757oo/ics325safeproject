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
//$pi_id_select=piSelectNow();
$pi_id_menu = buildPi_idMenu($pi_id_select);
$pi_id_menu='';
for($i = 0; $i < $x; $i++){
  $pi_id_item = $pi_id_json[$i]['PI_id'];
  if($pi_id_item===$pi_id_select){
    $pi_id_menu = $pi_id_menu.'<option value="'.$pi_id_item.'" selected>'.$pi_id_item.'</option>';
  } else{
    $pi_id_menu = $pi_id_menu.'<option value="'.$pi_id_item.'">'.$pi_id_item.'</option>';
  }
};

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
        <td></td>
        <td>
          <input type="hidden" id="baseUrl" name="baseUrl" readonly="readonly" value="<?php echo $base_url_out; ?>">
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
        <select id="teams" onchange="
      //sets team_select to selected value
      var team_select = this.value;
      //sets the selected value as the cookie
      document.cookie = escape('teamSelectCookie') + '=' + escape(team_select);"></select>
      </td>
    </tr>
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
<tr>
  <td><input type="submit" id="php_button" name="generate_button" class="button" value="Generate"></td>
  <td></td>
</tr>
</table>
</form><br>

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

<?php
//capturing the pi id cookie to use for the array
if(isset($_COOKIE['piCookie'])){
  $pi_id = $_COOKIE['piCookie'];
} else {
  $pi_id=$pi_id_select;
};
//Defaulting the selected team, this will need to be updated once the table have additional values available
$selected_team='805 Agile Team';
  /* Code for capturing the selected team that will need to be used later
  if(isset($_COOKIE['teamSelectCookie'])){
  $selected_team = $_COOKIE['teamSelectCookie'];
  } else {
    $selected_team='Team TBD';
  };
  */

  //settting up the pi id array for the Iteration # display
  $pi_id_array=array($pi_id."-1", $pi_id."-2" ,$pi_id."-3" ,$pi_id."-4", $pi_id."-5",$pi_id."-6",$pi_id."-IP");
  $count_piid = count($pi_id_array);
  //Loop for displaying the series of Employee table
  for($i = 0; $i < $count_piid; $i++){
    echo '<h4>Iteration # '.$pi_id_array[$i].'</h4>';
    buildEmployeeTable($selected_team,$duration,$overhead_percentage);
  };

//$result->close();
?>

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
<?php 
//$db->close(); 
include("./footer.php"); 
?>
