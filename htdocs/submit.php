
<html>
<body>
<?php
$dbname = 'activity_prototype';
$user = 'submit';
$password = 'DqcJ3WeWWYQBTG6r';
$dsn = "mysql:dbname=$dbname;host=localhost;charset=utf8";

function e($arg_1) {
   echo(htmlentities($arg_1));
}

/* check connection */
try {
  $pdo = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
  e(sprintf("Connection failed: %s\n", $e->getMessage()));
  exit();
}

function myQuery($pdo, $query, $msg = null) {
  try {
    $count = $pdo->exec($query);
    echo "$count rows affected.<br>";
    if (isset($msg)) {
      echo "$msg<br>";
    }
  } catch (PDFException $e){
    echo "Error: " . $e->getMessage();
  }
}

// ***
if (isset($_POST['activity']['name_english']) &&
    isset($_POST['activity']['name_chinese']) &&
    isset($_POST['activity']['teacher']) &&
    isset($_POST['activity']['date'])) {
  foreach ($_POST['activity'] as &$postItem) {
    $postItem = trim($postItem);
  }
  unset($postItem);
  $ane = $_POST['activity']['name_english'];
  $anc = $_POST['activity']['name_chinese'];
  $at  = $_POST['activity']['teacher'];
  $ad  = $_POST['activity']['date'];
  if ($_POST['activity']['name_english'] != "") {
    // Input error checking required here, for all required boxes to be filled in.  Probably can implement JavaScript simple layer.
    // INSERT INTO `activity` (`activity_index`, `activity_name_chinese`, `activity_name_english`) VALUES (NULL, NULL, 'Test');
    $query = "INSERT INTO `activity_prototype`.`activity` (`name_english`, `name_chinese`, `date`, `teacher`) VALUES ('$ane', '$anc', '$ad', '$at')";
    myQuery($pdo, $query, "Activity record created successfully!");
    
    $query = "SELECT last_insert_id();";
    $lastInsertID = $pdo->query($query)->fetch(PDO::FETCH_NUM)[0];
  }
} else {
  echo ("isset 0<br>");
}

if (isset($_POST['checkboxArray'])) {
  // echo("Submitted!<br>");
  $query = "INSERT into `activity_prototype`.`activity_student` (`activity_index`, `student_index`, `student_enrollment_year`) VALUES ($lastInsertID, :student_index, :student_enrollment_year)";
  $stmt = $pdo->prepare($query);
  foreach($_POST['checkboxArray'] as $thisCheckbox) {
    $studentFields = explode(",",$thisCheckbox);
    $stmt->execute(array(':student_index' => $studentFields[0], ':student_enrollment_year' => $studentFields[1]));
    // echo($thisCheckbox . "<br>");
  }
} else {
  echo("No Data!");
}
?>
</body>
</html>