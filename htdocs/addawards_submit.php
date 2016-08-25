<html>
<body>
<pre>
<?php 
  // DEBUG
  var_dump($_REQUEST);
?>
</pre>
<pre>
<?php
require_once 'models/pdo.php';
require_once 'models/pdo.cdgfss.php';
$cdgfssDB = new cdgfss_pdo();
// http://stackoverflow.com/a/20480796/3715973
// INSERT INTO `awards` (`awards_name`) VALUES ('Test Award')
  // ON DUPLICATE KEY UPDATE awards_id=LAST_INSERT_ID(awards_id), `awards_name`='Test Award';
// SELECT LAST_INSERT_ID();

// NOTE:  autoincrement will increase when the DUPLICATE is triggered.  awards_id will not be sequential.  This is expected and documented.
// http://stackoverflow.com/questions/7087869/mysql-insert-on-duplicate-update-adds-one-to-the-autoincrement
// from report outputs, assuming somewhere around 20,000 IDs used per year (2015-16 had ~7800, very generous estimate)
// it will take about 100,000 years to exhaust all the IDs (INT(10) is used as primary key), assuming the IDs are not consumed by a loop bug

// CHANGED to BIGINT.  20 digits.  It should no longer be able to burn through all IDs.  That is the # of unique "awards".

// Procedure:
// INSERT and retrieve the IDs for the award names.
// INSERT into activity_student using retrieved IDs.  Do NOT burn through the IDs by calling the INSERT ... ON DUPLICATE SQL.

// The pdo object here pulls a pdo object from the cdgfssDB class, so any changes to login credentials and initialization will also take effect
$pdo = $cdgfssDB->getSubmitPdo();

foreach($_REQUEST['awardsName'] as $awardsName) {
  $query = "INSERT INTO `awards` (`awards_name`) VALUES (:awardsName)
              ON DUPLICATE KEY UPDATE awards_id=LAST_INSERT_ID(awards_id), `awards_name`=:awardsName";
  $stmt = $pdo->prepare($query);
  $stmt->execute(array(':awardsName' => $awardsName));
  $count = $stmt->rowCount();
  
  $awardsNameArr[$awardsName] = $pdo->lastInsertId();
}

foreach($_REQUEST['awards'] as $awardStudent => $award) {
  $studentArr = explode(",", $awardStudent);
  var_dump($studentArr);
  $awardId = $awardsNameArr[$award];
  echo("\nAward ID: $awardId\n\n");
  // $studentArr[0]: Activity ID
  // $studentArr[1]: Student ID
  // $studentArr[2]: Student Enrolled Year
}


?>
</pre>
</body>
</html>
