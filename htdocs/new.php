<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<?php
$mysqli = new mysqli("localhost", "select_only", "xBX8swTSrGawmB5r", "activity_prototype");

/* check connection */
if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

/* change encoding */
if (!$mysqli->set_charset("utf8")) {
    printf("Error loading character set utf8: %s\n", $mysqli->error);
    exit();
} else {
    // printf("Current character set: %s\n", $mysqli->character_set_name());
}

function e($arg_1) {
   echo(htmlentities($arg_1));
}

$query = "SELECT * FROM student LIMIT 100";
?>

<?php

/* fetch associative array */
if ($result = $mysqli->query($query)) {
    while ($row = $result->fetch_assoc()) {
      echo("<input type='checkbox'>");
      // printf ("%s (%s)\n", $row["Name"], $row["CountryCode"]);
      // e($row['name_chinese']);
      // $row['student_index'], $row['student_number'], $row['name_chinese'], $row['name_english'], $row['gender'], $row['active']
      e(sprintf("Index: %d  Student ID: %s  %s  %s  %s  Active: %d",
        $row['student_index'], 
        $row['student_number'], 
        $row['name_chinese'], 
        $row['name_english'],
        $row['gender'], 
        $row['active']));
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
</body>
</html>