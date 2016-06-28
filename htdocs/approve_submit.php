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
?>
Activity ID: <?=$activity_id?><br>
Approval Code: <?=$approval_code?><br>
<textarea disabled cols=60 rows=6><?=$_REQUEST['approval_comment']?></textarea>
