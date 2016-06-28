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
<script   src="https://code.jquery.com/jquery-3.0.0.min.js"   integrity="sha256-JmvOoLtYsmqlsWxa7mDSLMwa6dZ9rrIdtrrVYRnDRH0="   crossorigin="anonymous"></script>

<style type="text/css">
  table, td, th {
    border-collapse: collapse;
    border: thin solid black;
  }
  
  td {
    padding: .2em;
  }
  
  tr:nth-of-type(2n) {
    background-color: #DDD;
  }
  
  #mainTable td:nth-child(1) {
    padding-right: 1em;
  }
  
  #main {
    
  }
  
  #main button {
    float: left;
    margin-right: 7em;
    width: 80px;
  }
  
  hr {
    margin-left: 0;
    width: 40em;
  }
  
  #text_status {
    font-size: 90%;
    color: #666;
  }
  
</style>

</head>
<body>
<form method="post">
  <div id="main">
    <div>
    <button type="submit" formaction="approve_submit.php?approval_code=1">Accept</button>
    <button type="submit" formaction="approve_submit.php?approval_code=0">Reject</button>
    <input type="hidden" name="activity_id" value="<?=$activity_id?>" />
    </div>
    <div style="clear: both; padding-top: 1em;">
    <textarea id="textarea" name="approval_comment" placeholder="Comments" cols=60 rows=6 maxlength=300 onkeyup="limitText()" onchange="limitText()" oninput="limitText()"></textarea>
    <div id="text_status"></div>


    <script type="text/javascript">
      function limitText() {
        textAreaObj = document.getElementById("textarea");
        textMaxLength = textAreaObj.getAttribute("maxlength");
        textLength = textAreaObj.value.length;
        textStatus = document.getElementById("text_status");
        textStatus.innerHTML = (textMaxLength - textLength) + " characters remaining" ;
      };
      limitText();
    </script>
    </div>
    <br>
    <hr>
  </div>
</form>

<p><u>Activity</u></p>
<?php
require_once 'models/pdo.php';
require_once 'models/pdo.cdgfss.php';

$pdoObj = new cdgfss_pdo();
$listActivityStmt = $pdoObj->listActivity_Details($activity_id);
if ($listActivityStmt->rowCount() <= 0) {
  exit("Activity ID $activity_id - contains no data");
}
$activityDetails =  $listActivityStmt->fetchAll()[0];
// [0] is to retrieve the first row.  There should never be more than 1 row of data since activity_id is a unique primary key
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