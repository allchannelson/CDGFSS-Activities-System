<html>
<body>
<?php
// This file really should be using the pdo.cdgfss.php class instead of creating its own connections, but this file was made before the
// class was designed, so refactoring can be done later if necessary -- 15YS - 29062016

$dbname = 'activity_prototype';
$user = 'submit';
$password = 'DqcJ3WeWWYQBTG6r';
$dsn = "mysql:dbname=$dbname;host=localhost;charset=utf8";

require_once 'submit_mail.php';
$cdgfssEmail = new cdgfss_mail();
$email = 't15ys@school.cdgfss.edu.hk';

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
  } catch (PDOException $e){
    echo "Error: " . $e->getMessage();
  }
}

// *** isset checks are INSUFFICIENT!  They are all set but are ''
// I'm not sure if I can confirm the above anymore.  I don't know how I tested this initially.  -- 15YS - 29062016
if (isset($_POST['activity']['teacher']) &&
    isset($_POST['activity']['unit']) &&
    isset($_POST['activity']['name_english']) &&
    isset($_POST['activity']['name_chinese']) &&
    isset($_POST['activity']['date'])) {
  foreach ($_POST['activity'] as &$postItem) {
    $postItem = trim($postItem);
  }
  unset($postItem);
  $at  = $_POST['activity']['teacher'];
  $au  = $_POST['activity']['unit'];
  $ane = $_POST['activity']['name_english'];
  $anc = $_POST['activity']['name_chinese'];
  $ad  = $_POST['activity']['date'];
  if ($_POST['activity']['name_english'] != "") {
    $query = "INSERT INTO `activity_prototype`.`activity` (`teacher`, `unit`, `name_english`, `name_chinese`, `date`) VALUES (:at, :au, :ane, :anc, :ad)";
    $stmt = $pdo->prepare($query);
    // $stmt->setFetchMode($args);  // only if you need to change the FETCH mode.  Unnecessary for an INSERT
    $stmt->execute(array(':at' => $at, ':au' => $au, ':ane' => $ane, ':anc' => $anc, ':ad' => $ad));
    $count = $stmt->rowCount();
    echo "$count rows inserted.  Activity Record Submitted.<br>";
    // no SQL error checking.  If necessary, call $stmt->errorInfo() and check the returned array for errors.
    
    $query = "SELECT last_insert_id();";
    $lastInsertID = $pdo->query($query)->fetch(PDO::FETCH_NUM)[0];
  }
} else {
  echo ("Missing activity essential data!  Javascript validation bypassed.  Please disable Javascript blockers.");
  exit();
}

if (isset($_POST['checkboxArray'])) {
  $query = "INSERT into `activity_prototype`.`activity_student` (`activity_index`, `student_index`, `student_enrollment_year`) VALUES ($lastInsertID, :student_index, :student_enrollment_year)";
  $stmt = $pdo->prepare($query);
  
  // Since each student is a separate query execution, this takes a bit more than 30 seconds to finish if all of 2015's 903 students are added to an activity.
  // Will setup a progress bar to indicate this:
  // http://www.htmlgoodies.com/beyond/php/show-progress-report-for-long-running-php-scripts.html
  foreach($_POST['checkboxArray'] as $thisCheckbox) {
    $studentFields = explode(",",$thisCheckbox);
    $stmt->execute(array(':student_index' => $studentFields[0], ':student_enrollment_year' => $studentFields[1]));
  }

  echo "E-mail record sent to $email";
  $cdgfssEmail->sendMailWithAttachment($email, $lastInsertID);
} else {
  echo("No Students Entered!  Javascript validation bypassed.  Please disable Javascript blockers.");
  exit();
}
?>
</body>
</html>
