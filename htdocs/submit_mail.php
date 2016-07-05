<?php
// I remade this class as OO.
// After thinking about it for a while, the $message building sequence may have race conditions if more than one e-mail is being generated.
// Best to keep it instantiated so each e-mail is its own object.

require_once 'swift/swift_required.php';

require_once 'models/pdo.php';
require_once 'models/pdo.cdgfss.php';

class cdgfss_mail {
  
  private $transport;

  private $mailer;
  private $activity_id;
  private $pdoObj;
  private $textbody;
  private $htmlbody;
  private $message;
  private $activityDetails;
  private $alternateBGcolor = "style='background: #DDD'";

  public function __construct($input) {
    $this->init();
    $this->sendMail($input);
  }
  
  private function sendMail($input) {
    // $input = activity_id
    $this->setActivityId($input);
    $this->initDb();
    $this->generateActivityDetails();
    $this->generateStudentDetails();
    $this->generateMessage();
    $result = $this->mailer->send($this->message);
  }

  private function init() {
    $this->transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, "ssl")
      ->setUsername('eac_system@school.cdgfss.edu.hk')
      ->setPassword('7fM5wm$mT[Z=T7+N')
      ;
    $this->mailer = Swift_Mailer::newInstance($this->transport);
  }
  
  private function setActivityId($input) {
    $this->activity_id = (int)$input;
  }
  
  private function initDb() {
    $this->pdoObj = new cdgfss_pdo();
  }
  
  private function generateStudentDetails() {
    $activityStudentHeading = $this->pdoObj->columns_Activity_AllStudents();
    $activityStudents = $this->pdoObj->listActivity_AllStudents($this->activity_id);

    $this->htmlbody .= "<br><hr><p><u>Students</u></p><table border=1 id='studentTable' style='td {border: thin solid red;}'><tr>";
    $this->textbody .= "** Students **\r\n\r\n";
    foreach ($activityStudentHeading as $field) {
      $this->htmlbody .= "<td>{$field}</td>";
    $this->textbody .= "{$field}	";
    }
    $count = 0;
    foreach ($activityStudents as $row) {
      $this->htmlbody .= $count%2==1?"<tr>":"<tr {$this->alternateBGcolor}>";
      $this->textbody .= "\r\n";
      $count += 1;
      foreach ($row as $field) {
        $this->htmlbody .= "<td>{$field}</td>";
      $this->textbody .= "{$field}	";
      }
      $this->htmlbody .= "</tr>";
      $this->textbody .= "\r\n";
    }

    $this->htmlbody .= "</tr>";
    $this->htmlbody .= "</table>";
  }

  private function generateMessage() {
    $this->message = Swift_Message::newInstance('Activity - ' . $this->activityDetails['name_english'] . '/' . $this->activityDetails['name_chinese'])
      ->setFrom(array('eac_system@school.cdgfss.edu.hk' => 'EAC System'))
      ->setTo(array('t15ys@school.cdgfss.edu.hk'))
      ->setBody($this->textbody)
      ->addPart($this->htmlbody, 'text/html')
      ;
  }
  
  private function generateActivityDetails() {
    $listActivityStmt = $this->pdoObj->listActivity_Details($this->activity_id);
    if ($listActivityStmt->rowCount() <= 0) {
      exit(); // this can happen if the activity_id is invalid
    }
    $this->activityDetails = $listActivityStmt->fetchAll()[0];

    $this->textbody = <<<"TEXTBODY"
    -- Activity ID: $this->activity_id --
    Teacher in charge	{$this->activityDetails['teacher']}
    Participating Unit	{$this->activityDetails['unit']}
    Name of Activity / Competition (ENG)	{$this->activityDetails['name_english']}
    Name of Activity / Competition (CHI)	{$this->activityDetails['name_chinese']}
    Date	{$this->activityDetails['date']}
    Time	{$this->activityDetails['time']}
    Partner Organization (ENG)	{$this->activityDetails['partner_name_english']}
    Partner Organization (CHI)	{$this->activityDetails['partner_name_chinese']}
    Destination/Route	{$this->activityDetails['destination']}



TEXTBODY;
// Heredoc closing identifier cannot have any other characters other than the identifier and semi-colon.
// http://php.net/manual/en/language.types.string.php#language.types.string.syntax.heredoc

    $this->htmlbody = <<<"HTMLBODY"

    <p><u>Activity ID: $this->activity_id</u></p>
    <table border=1 id='mainTable'>
      <tr><td>Teacher in charge</td><td>{$this->activityDetails['teacher']}</td></tr>
      <tr $this->alternateBGcolor><td>Participating Unit</td><td>{$this->activityDetails['unit']}</td></tr>
      <tr><td>Name of Activity / Competition (ENG)</td><td>{$this->activityDetails['name_english']}</td></tr>
      <tr $this->alternateBGcolor><td>Name of Activity / Competition (CHI)</td><td>{$this->activityDetails['name_chinese']}</td></tr>
      <tr><td>Date</td><td>{$this->activityDetails['date']}</td></tr>
      <tr $this->alternateBGcolor><td>Time</td><td>{$this->activityDetails['time']}</td></tr>
      <tr><td>Partner Organization (ENG)</td><td>{$this->activityDetails['partner_name_english']}</td></tr>
      <tr $this->alternateBGcolor><td>Partner Organization (CHI)</td><td>{$this->activityDetails['partner_name_chinese']}</td></tr>
      <tr><td>Destination/Route</td><td>{$this->activityDetails['destination']}</td></tr>
    </table>
HTMLBODY;
// Heredoc closing identifier cannot have any other characters other than the identifier and semi-colon.
// http://php.net/manual/en/language.types.string.php#language.types.string.syntax.heredoc
  }
}
?>
