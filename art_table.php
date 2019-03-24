<link rel="stylesheet" type="text/css" href="styleCustom.css">

<?php
$url=$base_url_out;
$teamlist  = urldecode($_COOKIE['teamCookie']);		// split teamCookie into arraylist called diff_team_names
$diff_team_names =  explode(",", $teamlist);
//checks for the PI ID in the request and the cookie to set the variable. If they are not available it is set to null
if(isset($_REQUEST['pi_id'])){
  $pi_id = $_REQUEST['pi_id'];
}
elseif(isset($_COOKIE['piCookie'])){
  $pi_id=$_COOKIE['piCookie'];
} 
else {
  $pi_id = '';
};

//Creates table headings
echo "<table id='table_load'><tr><thead class=\"table_head\"><tr>
              <th>No.</th>
              <th>Team Name</th>
              <th>".$pi_id . "-1</th>
              <th>".$pi_id . "-2</th>
              <th>".$pi_id . "-3</th>
              <th>".$pi_id . "-4</th>
              <th>".$pi_id . "-5</th>
              <th>".$pi_id . "-6</th>
              <th>".$pi_id . "-IP</th>
                  </tr>
            </thead>
      <tbody class=\"table_body\">";
$increment_pID = 1;					// Vars for incrementing the loop
$list_num = 1;						// Lists each table number in order

//creates the rest of the table, by rows of each team name
foreach ($diff_team_names as $value){
  $team_specific = $value;
  echo "<tr>";
  echo "<td>";
  echo ($list_num . ".");
  echo"</td>";
  echo"<td>";
  echo($team_specific);
  echo"</td>";
  // creates the URL with the variables
  for($x=1; $x<7; $x++){
    $gen_URL = $base_url_out . $pi_id . "-" . $increment_pID . "_" . urlencode($team_specific);
    echo "<td>";
    echo "<a href=$gen_URL title=$gen_URL>".$pi_id. "-" . $increment_pID ."</a>" ; // make gen_URL into an href
    echo "</td>";

      $increment_pID ++;	//increment the arraylist and get the next team name & create row

  // specific to ending of - ID
    if($increment_pID == 7){
      $gen_URL = $url . $pi_id . "-" . "IP" . "_" . urlencode($team_specific);
      echo "<td>";
      echo "<a href=$gen_URL title=$gen_URL>".$pi_id."-IP </a>";
      echo "</td>";
      echo "\n";
    }
  }
  echo "\n";
  $increment_pID = 1;
  $list_num ++;
  echo"</tr>";
};

if($_SERVER['REQUEST_METHOD'] == 'POST'){
  //removes initial table

    $generate_button=$_POST['generate_button'];
    $url=$base_url_out;
    $pi_id = $_REQUEST['pi_id'];
    $art = $_REQUEST['art'];
    $teams= $_REQUEST['teams'];

    $teamlist  = $teams;		// split teamlist into arraylist called diff_team_names
    $diff_team_names =  explode(",", $teamlist);

  if($generate_button==='JS Generate'){

  echo "<script>


  /**
  * Updates arrays to repoplate table
  */

  teamList_array = teamList.split(\",\");
  ID_iteration_array = [\"".$pi_id . "-1\", \"".$pi_id . "-2\", \"".$pi_id . "-3\", \"".$pi_id . "-4\", \"".$pi_id . "-5\", \"".$pi_id . "-6\", \"".$pi_id . "-IP\"];

  /**
  * Runs the functions that will generate the tables
  */
  /**
  * creating and initializing the variable used to build the table
  */
  var r = 0;
  var row_id = 0;
  var row = '';
  var c = 0;
  var link = baseURL;
  var table = document.createElement('table');
  var table_head = document.createElement('thead');
  var row = document.createElement('tr');
  var table_body = document.createElement('tbody');
  var th0 = document.createElement('th');
  var th1 = document.createElement('th');
  var th2 = document.createElement('th');
  var th3 = document.createElement('th');
  var th4 = document.createElement('th');
  var th5 = document.createElement('th');
  var th6 = document.createElement('th');
  var th_no = document.createElement('th');
  var th_name = document.createElement('th');

  //setting the table header variables
  th_no.appendChild(document.createTextNode('No.'));
  th_name.appendChild(document.createTextNode('Team Name'));
  th0.appendChild(document.createTextNode(ID_iteration_array[0]));
  th1.appendChild(document.createTextNode(ID_iteration_array[1]));
  th2.appendChild(document.createTextNode(ID_iteration_array[2]));
  th3.appendChild(document.createTextNode(ID_iteration_array[3]));
  th4.appendChild(document.createTextNode(ID_iteration_array[4]));
  th5.appendChild(document.createTextNode(ID_iteration_array[5]));
  th6.appendChild(document.createTextNode(ID_iteration_array[6]));

  //starting the table
  table_head.appendChild(th_no);
  table_head.appendChild(th_name);
  table_head.appendChild(th0);
  table_head.appendChild(th1);
  table_head.appendChild(th2);
  table_head.appendChild(th3);
  table_head.appendChild(th4);
  table_head.appendChild(th5);
  table_head.appendChild(th6);
  table.appendChild(table_head);

  /*
  * begins the loop that creates the table rows for each of the teams
  */
  for (r = 0; r < teamList_array.length; r++) {
    row_id = r + 1;
    c = 0;
    table_link = '';

        var row = document.createElement('tr');
        var data_1 = document.createElement('td');
        var data_2 = document.createElement('td');

        data_1.appendChild(document.createTextNode(row_id));
        data_2.appendChild(document.createTextNode(teamList_array[r]));
        row.appendChild(data_1);
        row.appendChild(data_2);

        for (c = 0; c < ID_iteration_array.length; c++) {
            var data_3 = document.createElement('td');
            var a = document.createElement('a');
            var href = document.createAttribute('href');
            var title = document.createAttribute('title');
            var link = baseURL + '?id=' + ID_iteration_array[c] + '_' + teamList_array[r] + '\"';
            href.value = link;
            title.value = link;
            a.setAttributeNode(href);
            a.setAttributeNode(title);
            data_3.appendChild(a);
            a.appendChild(document.createTextNode(ID_iteration_array[c]));
            row.appendChild(data_3);

    }
    table_body.appendChild(row);
  }
  table.appendChild(table_body);
  document.body.appendChild(table);
    </script>";

    }

  if($generate_button==='PHP Generate'){
    //removes existing table
    echo "<script>$('#table_load').remove();</script>";
    //Creates table headings
    echo "<table>
    <tr>
    <thead class=\"table_head\">
      <tr>
        <th>No.</th>
        <th>Team Name</th>
        <th>".$pi_id . "-1</th>
        <th>".$pi_id . "-2</th>
        <th>".$pi_id . "-3</th>
        <th>".$pi_id . "-4</th>
        <th>".$pi_id . "-5</th>
        <th>".$pi_id . "-6</th>
        <th>".$pi_id . "-IP</th>
      </tr>
    </thead>
   <tbody class=\"table_body\">";

    $increment_pID = 1;					// Vars for incrementing the loop
    $list_num = 1;						// Lists each table number in order


  ?>
  <?php	//creates the rest of the table, by rows of each team name

    foreach ($diff_team_names as $value){
      $team_specific = $value;
      echo "<tr>";
      echo "<td>";
      echo ($list_num . ".");
      echo"</td>";
      echo"<td>";
      echo($team_specific);
      echo"</td>";

      // creates the URL with the variables
      for($x=1; $x<7; $x++){
        $gen_URL = $url . "?id=" .$pi_id . "-" . $increment_pID . "_" . urlencode($team_specific);
        echo "<td>";
        echo "<a href=$gen_URL title=$gen_URL>".$pi_id. "-" . $increment_pID ."</a>" ; // make gen_URL into an href
        echo "</td>";

          $increment_pID ++;	//increment the arraylist and get the next team name & create row

      // specific to ending of - ID
        if($increment_pID == 7){
          $gen_URL = $url . "?id=". $pi_id . "-" . "IP" . "_" . urlencode($team_specific);
          echo "<td>";
          echo "<a href=$gen_URL title=$gen_URL>".$pi_id."-IP </a>";
          echo "</td>";
          echo "\n";
        }
      }
      echo "\n";
      $increment_pID = 1;
      $list_num ++;
      echo"</tr>";
    }
  }
  }

?>
