
<html>
<body>
<?php
$mysqli = new mysqli("localhost", "submit", "DWpZTf5hPf6JjPzq", "activity_prototype");

/* check connection */
if ($mysqli->connect_errno) {
    e(sprintf("Connect failed: %s\n", $mysqli->connect_error));
    exit();
}

/* change encoding */
if (!$mysqli->set_charset("utf8")) {
    e(sprintf("Error loading character set utf8: %s\n", $mysqli->error));
    exit();
} else {
    // printf("Current character set: %s\n", $mysqli->character_set_name());
}

// $query = "SELECT * FROM student LIMIT 10";

if (isset($_POST['checkboxArray'])) {
  echo("Submitted!<br>");
  foreach($_POST['checkboxArray'] as $thisCheckbox) {
      echo($thisCheckbox . "<br>");
  }
} else {
  echo("No Data!");
}
?>
</body>
</html>
