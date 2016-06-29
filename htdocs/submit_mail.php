<?php
require_once 'swift/swift_required.php';

require_once 'models/pdo.php';
require_once 'models/pdo.cdgfss.php';

$transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, "ssl")
  ->setUsername('eac_system@school.cdgfss.edu.hk')
  ->setPassword('7fM5wm$mT[Z=T7+N');

$mailer = Swift_Mailer::newInstance($transport);

if (isset($_REQUEST['activity_id'])) {
  $activity_id = (int)($_REQUEST['activity_id']);
} else {
  exit("Invalid/Missing Activity ID: " . (isset($activity_id)?$activity_id:""));
}
 
$pdoObj = new cdgfss_pdo();
$listActivityStmt = $pdoObj->listActivity_Details($activity_id);
if ($listActivityStmt->rowCount() <= 0) {
  exit(); // this should NEVER happen
}
$activityDetails =  $listActivityStmt->fetchAll()[0];

$textbody = <<<"TEXTBODY"
-- Activity ID: $activity_id --
Teacher in charge	{$activityDetails['teacher']}
Participating Unit	{$activityDetails['unit']}
Name of Activity / Competition (ENG)	{$activityDetails['name_english']}
Name of Activity / Competition (CHI)	{$activityDetails['name_chinese']}
Date	{$activityDetails['date']}
Time	{$activityDetails['time']}
Partner Organization (ENG)	{$activityDetails['partner_name_english']}
Partner Organization (CHI)	{$activityDetails['partner_name_chinese']}
Destination/Route	{$activityDetails['destination']}



TEXTBODY;

$htmlbody = <<<"HTMLBODY"

<p><u>Activity ID: $activity_id</u></p>
<table border=1 id='mainTable'>
  <tr><td>Teacher in charge</td><td>{$activityDetails['teacher']}</td></tr>
  <tr><td>Participating Unit</td><td>{$activityDetails['unit']}</td></tr>
  <tr><td>Name of Activity / Competition (ENG)</td><td>{$activityDetails['name_english']}</td></tr>
  <tr><td>Name of Activity / Competition (CHI)</td><td>{$activityDetails['name_chinese']}</td></tr>
  <tr><td>Date</td><td>{$activityDetails['date']}</td></tr>
  <tr><td>Time</td><td>{$activityDetails['time']}</td></tr>
  <tr><td>Partner Organization (ENG)</td><td>{$activityDetails['partner_name_english']}</td></tr>
  <tr><td>Partner Organization (CHI)</td><td>{$activityDetails['partner_name_chinese']}</td></tr>
  <tr><td>Destination/Route</td><td>{$activityDetails['destination']}</td></tr>
</table>
HTMLBODY;

$pdoObj = new cdgfss_pdo();
$activityStudentHeading = $pdoObj->columns_Activity_AllStudents();
$activityStudents = $pdoObj->listActivity_AllStudents($activity_id);

$htmlbody .= "<br><hr><p><u>Students</u></p><table border=1 id='studentTable' style='td {border: thin solid red;}'><tr>";
$textbody .= "** Students **\r\n\r\n";
foreach ($activityStudentHeading as $field) {
  $htmlbody .= "<td>$field</td>";
  $textbody .= "$field	";
}
foreach ($activityStudents as $row) {
  $htmlbody .= "<tr>";
  $textbody .= "\r\n";
  foreach ($row as $field) {
    $htmlbody .= "<td>$field</td>";
    $textbody .= "$field	";
  }
  $htmlbody .= "</tr>";
  $textbody .= "\r\n";
}

$htmlbody .= "</tr>";
$htmlbody .= "</table>";

$message = Swift_Message::newInstance('Activity - ' . $activityDetails['name_english'] . '/' . $activityDetails['name_chinese'])
  ->setFrom(array('eac_system@school.cdgfss.edu.hk' => 'EAC System'))
  ->setTo(array('t15ys@school.cdgfss.edu.hk'))
  ->setBody($textbody)
  ->addPart($htmlbody, 'text/html')
  ;

$result = $mailer->send($message);

?>
