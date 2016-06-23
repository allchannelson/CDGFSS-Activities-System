<!DOCTYPE html>
<html>
<head>
<?php 
  if (isset($_REQUEST['activity_id'])) {
    $activity_id = (int)($_REQUEST['activity_id']);  // (int) cast prevents invalid and non-numeric characters, probably will prevent SQL injection
  } else {
    exit("Missing or Invalid Activity ID");
  }
?>
<style type="text/css">
  table, td, th {
    border-collapse: collapse;
    border: thin solid black;
  }
  
  td {
    padding: .2em;
  }
  
  #mainTable td:nth-child(1) {
    padding-right: 1em;
  }
</style>
</head>
<body>

<p><u>Activity</u></p>
<?php
require_once 'models/pdo.php';
require_once 'models/pdo.cdgfss.php';

$pdoObj = new cdgfss_pdo();
if ($pdoObj->listActivity_Details($activity_id)->rowCount() <= 0) {
  exit("Activity ID $activity_id - contains no data");
}
$activityDetails =  $pdoObj->listActivity_Details($activity_id)->fetchAll()[0];
?>
<table id="mainTable">
  <tr><td>Teacher in charge</td><td><?=$activityDetails['teacher']?></td></tr>
  <tr><td>Participating Unit</td><td><?=$activityDetails['unit']?></td></tr>
  <tr><td>Name of Activity / Competition (ENG)</td><td><?=$activityDetails['name_english']?></td></tr>
  <tr><td>Name of Activity / Competition (CHI)</td><td><?=$activityDetails['name_chinese']?></td></tr>
  <tr><td>Date</td><td><?=$activityDetails['date']?></td></tr>
  <tr><td>Time</td><td><?=$activityDetails['time']?></td></tr>
  <tr><td>Partner Organization (ENG)</td><td><?=$activityDetails['partner_name_english']?></td></tr>
  <tr><td>Partner Organization (CHI)</td><td><?=$activityDetails['partner_name_chinese']?></td></tr>
  <tr><td>Destination/Route</td><td><?=$activityDetails['destination']?></td></tr>
</table>

<p><u>Students</u></p>
<?php
$pdoObj = new cdgfss_pdo();
$activityStudentHeading = $pdoObj->columns_Activity_AllStudents();
$activityStudents = $pdoObj->listActivity_AllStudents($activity_id);
?>
<table id="studentTable">
  <tr>
    <?php foreach ($activityStudentHeading as $field): ?>
    <td><?= $field ?></td>
    <?php endforeach ?>
  </tr>
  <?php foreach ($activityStudents as $row): ?>
  <tr>
    <?php foreach ($row as $field): ?>
    <td><?= $field ?></td>
    <?php endforeach ?>
  </tr>
  <?php endforeach ?>
</table>

</body>
</html>