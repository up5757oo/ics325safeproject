<?php
  if (isset($_POST['showNext'])) {
    $sequence++;
    echo '<script>console.log("Show Next: " + "'.$sequence.'");</script>';
    echo '<script>console.log("Program Increment: " + "'.$program_increment.'");</script>';
    
    $sql = "SELECT sequence, PI_id as program_increment, iteration_id as iteration 
            FROM `cadence`
            WHERE sequence='".$sequence."';";
    $result = $db->query($sql);
    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $program_increment = $row["program_increment"];
      $iteration = $row["iteration"];
      $sequence = $row["sequence"];
      $result->close();
    } else {
      $sql = "SELECT sequence, PI_id as program_increment, iteration_id as iteration 
              FROM `cadence`
              WHERE PI_id='".$program_increment."'
              ORDER BY sequence limit 1;";
      $result = $db->query($sql);
      if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $program_increment = $row["program_increment"];
        $iteration = $row["iteration"];
        $sequence = $row["sequence"];
        $result->close();
    }
  }
  echo '<script>console.log("Program Increment: " + "'.$iteration.'");</script>';
    $sql = "SELECT * FROM `capacity` where team_id='".$selected_team."' AND program_increment='".$program_increment."';";
    $result = $db->query($sql);
    if ($result->num_rows > 0) {
      $default_data = false;
      $default_total = ($row["iteration_1"] + $row["iteration_2"] + $row["iteration_3"] + $row["iteration_4"]+ $row["iteration_5"] + $row["iteration_6"] + $row["iteration_IP"]);
    } else {
      $default_data = true;
      $default_total = 0;

      $sql = "SELECT * FROM `membership` where team_name in (select team_name from trains_and_teams where team_id = '".$selected_team."');";
      $result = $db->query($sql);
      if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {

          if ($row["role"] == "SM") {
            $velType = "SCRUM_MASTER_ALLOCATION";
          } else if ($row["role"] == "PO") {
            $velType = "PRODUCT_OWNER_ALLOCATION";
          } else  {
            $velType = "AGILE_TEAM_MEMBER_ALLOCATION";
          }

          $sql2 = "SELECT * FROM `preferences` WHERE name='".$velType."';";
          $result2 = $db->query($sql2);

          if ($result2->num_rows > 0) {

              $row2 = $result2->fetch_assoc();
              $default_total += $row2["value"];

          }

      }
    }
    }
  }

  
        //function for using art data to create a json file for Capacity data
        function buildCapacityJSON($art,$team,$pi_id){
          $file = $pi_id."_".$art."_".$team."_cache.json";
          fopen("dataFiles/".urlencode($file), "w+");
          $db = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
          $db->set_charset("utf8");
           if ($result = $db->query("SELECT art_name, team_name, PI_id, iteration_id, last_name, first_name, role, value as velocity, '0' as Days_Off, ((duration * .8) * (value/100)) as Story_points FROM `membership`,  employees, preferences, cadence where
              (membership.polarion_id = employees.number)
              and role = 'SM'
              and name = 'SCRUM_MASTER_ALLOCATION'
              and art_name = '".$art."'
              and team_name = '".$team."'
              and PI_id = '".$pi_id."'
              union
              SELECT art_name, team_name, PI_id, iteration_id, last_name, first_name, role, value as velocity, '0' as Days_Off, ((duration * .8) * (value/100))  as Story_points FROM `membership`,  employees, preferences, cadence where
              (membership.polarion_id = employees.number)
              and role = 'PO'
              and name = 'PRODUCT_OWNER_ALLOCATION'
              and art_name = '".$art."'
              and team_name = '".$team."'
              and PI_id = '".$pi_id."'
              union
              SELECT art_name, team_name, PI_id, iteration_id, last_name, first_name, role, value as velocity, '0' as Days_Off, ((duration * .8) * (value/100)) as Story_points FROM `membership`,  employees, preferences, cadence where
              (membership.polarion_id = employees.number)
              and role = 'DEVELOPER'
              and name = 'AGILE_TEAM_MEMBER_ALLOCATION'
              and art_name = '".$art."'
              and team_name = '".$team."'
              and PI_id = '".$pi_id."'
              order by art_name, pi_id, iteration_id, velocity desc, role;")) {
                  $rows = array();
                  while($row = $result->fetch_array()) {
                      $rows[] = $row;
                  }
                  file_put_contents("dataFiles/".urlencode($file), json_encode($rows));
              }
              
          };   
  ?>