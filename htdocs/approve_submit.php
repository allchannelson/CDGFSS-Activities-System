<!DOCTYPE html>
<html>
<head>
<?php 
  // (int) cast prevents invalid and non-numeric characters, prevents SQL injection
  if (isset($_REQUEST['activity_id'])) {
    $activity_id = (int)($_REQUEST['activity_id']);
  }
  if (isset($_REQUEST['approval_code'])) {
    $approval_code = (int)($_REQUEST['approval_code']);
  }
  if (isset($activity_id) && isset($approval_code)) {
    
  } else {
    exit("Missing or Invalid Data Received:<br>\$activity_id: " . (isset($activity_id)?$activity_id:"")  . "<br>\$approval_code: " . (isset($approval_code)?$approval_code:""));
  }

  require_once 'models/pdo.php';
  require_once 'models/pdo.cdgfss.php';

  $cdgfssDB = new cdgfss_pdo();
  $approval_comment = $_REQUEST['approval_comment'];
  $approval_code_meaning = "";
  
  switch($approval_code) {
    case 0:
      $approval_code_meaning = "Reject";
      break;
    case 1:
      $approval_code_meaning = "Approve";
      break;
    default:
      $approval_code_meaning = "Unknown Approval Code";
  }

?>
Activity ID: <?=$activity_id?><br>
Approval Code: <?=$approval_code?> (<?=$approval_code_meaning?>)<br>
Comments:<br>
<textarea disabled cols=60 rows=6><?=$approval_comment?></textarea><br>
<?php 
  $stmt = $cdgfssDB->approval_submit($activity_id, $approval_code, $approval_comment);
  $errorArray = $stmt->errorInfo();
  // see http://www.unix.org.ua/orelly/java-ent/jenut/ch08_06.htm for full list of error codes
  if ($errorArray[0] == "00000") {
    echo ("$approval_code_meaning Submitted Successfully.");
  } else {
    echo ("SQLSTATE error code: {$errorArray[0]}<br>");
    echo ("Driver error code: {$errorArray[1]}<br>");
    echo ("Driver error message: {$errorArray[2]}<br>");
  }
?>

