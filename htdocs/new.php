<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<hr>
<u>Activity</u><br>
Activity ENG Name: <input type="text" name="activity_name_english" length=100></input><br>
Date: <input type="text" length=40></input><br>
Teacher: <input type="text" length=40></input><br>
<hr>
<u>Students</u><br>
<form action="submit.php" method="post">
<?php
$mysqli = new mysqli("localhost", "select_only", "xBX8swTSrGawmB5r", "activity_prototype");

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

/* custom function for HTML output */
function e($arg_1) {
   echo(htmlentities($arg_1));
}

$query = "SELECT * FROM student LIMIT 10";
// $query = "SELECT * FROM student";

/* fetch associative array */
if ($result = $mysqli->query($query)) {
  
  while ($row = $result->fetch_assoc()) {
    echo(sprintf("<input type='checkbox' name='checkboxArray[]' value='%s | %s'>", $row['student_number'], $row['name_chinese']));
    // $row['student_index'], $row['student_number'], $row['name_chinese'], $row['name_english'], $row['gender'], $row['active']
    //e(sprintf("Index: %d  Student ID: %s  %s  %s  %s  Active: %d",
    e(sprintf("Index: %d  Student ID: %s  %s %s",
      $row['student_index'], 
      $row['student_number'], 
      $row['name_chinese'], 
      $row['name_english'],
      $row['gender'], 
      $row['active'])
     );
    echo("</input><br>");

  }
  echo("</checkbox>");

  /* free result set */
  $result->free();
}
//

/* close connection */
$mysqli->close();
?>
<input type="submit" value="Submit">
</form>
</body>
</html>
