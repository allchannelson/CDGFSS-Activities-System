<!DOCTYPE html>
<html>
<head>
<?php 
  // (int) cast prevents invalid and non-numeric characters, prevents SQL injection
  // The above is old-implementation artifacts.  approval_submit() uses Prepared Statement so it won't be vulnerable to SQL injection.
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
    // This can be expanded to accept all sorts of codes.
    // One option is {main stage ##}{sub stage ###} = #####
    // Once basic 0/1 approval is setup, probably need to setup at least 2 stages, where Vice Principal looks at it, then Principal approves.
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
    // e-mail to author
    // e-mail to approver
  } else {
    echo ("System error.  Forward to technical support:<br><hr><br>");
    echo ("SQLSTATE error code: {$errorArray[0]}<br><hr><br>");
    echo ("Driver error code: {$errorArray[1]}<br><hr><br>");
    echo ("Driver error message: {$errorArray[2]}<br>");
  }
?>

