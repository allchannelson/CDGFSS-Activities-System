
<html>
<body>
<?php
$mysqli = new mysqli("localhost", "submit", "DWpZTf5hPf6JjPzq", "activity_prototype");

/* check connection */
if ($mysqli->connect_errno) {
    e(sprintf("Connect failed: %s\n", $mysqli->connect_error));
    exit();
}

function myQuery($mysqli, $query, $msg = null) {
  if (mysqli_query($mysqli, $query)) {
    if (isset($msg)) {
      echo "$msg<br>";
    }
  } else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
  }
}

/* change encoding */
if (!$mysqli->set_charset("utf8")) {
    e(sprintf("Error loading character set utf8: %s\n", $mysqli->error));
    exit();
} else {
    // printf("Current character set: %s\n", $mysqli->character_set_name());
}

// $query = "SELECT * FROM student LIMIT 10";

// Input error checking required here, for all required boxes to be filled in.  Probably can implement JavaScript simple layer.

// ***
if (isset($_POST['activity_name_english'])) {
  $insertValue = $_POST['activity_name_english'];
  // INSERT INTO `activity` (`activity_index`, `activity_name_chinese`, `activity_name_english`) VALUES (NULL, NULL, 'Test');
  $query = "INSERT INTO `activity_prototype`.`activity` (`activity_name_english`) VALUES ('$insertValue')";
  myQuery($mysqli, $query, "Activity record created successfully!");
  $query = "SELECT last_insert_id();";
  myQuery($mysqli, $query, "Last ID query completed.");
  
}

if (isset($_POST['checkboxArray'])) {
  //echo("Submitted!<br>");
  foreach($_POST['checkboxArray'] as $thisCheckbox) {
    echo($thisCheckbox . "<br>");
  }
} else {
  echo("No Data!");
}
?>
</body>
</html>
