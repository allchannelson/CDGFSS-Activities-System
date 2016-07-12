<?php
// I remade this class as OO.
// After thinking about it for a while, the $message building sequence may have race conditions if more than one e-mail is being generated.
// Best to keep it instantiated so each e-mail is its own object.

require_once 'swift/swift_required.php';

require_once 'models/pdo.php';
require_once 'models/pdo.cdgfss.php';
// require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';
require_once 'php/PHPExcel/Classes/PHPExcel.php';

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

  public function __construct() {
    $this->init();
  }
  
  private function init() {
    $this->transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, "ssl")
      ->setUsername('eac_system@school.cdgfss.edu.hk')
      ->setPassword('7fM5wm$mT[Z=T7+N')
      ;
    $this->mailer = Swift_Mailer::newInstance($this->transport);
  }
  
  public function sendMail($email, $activity_id) {
    $this->sendMailInit($email, $activity_id);
    $result = $this->mailer->send($this->message);
  }
  
  public function TEST_sendMailPlainTextAttachment($email, $activity_id) {
    $this->sendMailInit($email, $activity_id);
    $this->generateAttachment('Hello World!', 'text.txt', 'text/plain');
    $result = $this->mailer->send($this->message);
  }
  
  public function TEST_sendMailXLSXAttachment($email, $activity_id) {
    $this->sendMailInit($email, $activity_id);
    $this->generateTestAttachment();
    $result = $this->mailer->send($this->message);
  }
  
  public function sendMailWithAttachment($email, $activity_id, $attachmentData) {
    $this->sendMailInit($email, $activity_id);
    $this->generateAttachment($attachmentData);
    $result = $this->mailer->send($this->message);
  }
  
  private function sendMailInit($email, $activity_id) {
    $this->setActivityId($activity_id);
    $this->initDb();
    $this->generateActivityDetails();
    $this->generateStudentDetails();
    $this->generateMessage($email);
  }

  private function setActivityId($activity_id) {
    // I'm not going to do thorough error checking.  activity_id is setup as an auto-incrementing integer in the database
    // PDO calls are using prepared statements, so this (int) cast isn't going to add much in terms of preventing SQL injection.
    // If someone changes the activity_id to non-integers, then it'll cause problems... but that's a pretty major change and
    // will probably break a ton of other things
    $this->activity_id = (int)$activity_id;
  }
  
  private function initDb() {
    $this->pdoObj = new cdgfss_pdo();
  }
  
  private function generateAttachment($inputData, $inputFileName = 'download.xlsx', $inputContentType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
    $this->message->attach(Swift_Attachment::newInstance()
      ->setFilename($inputFileName)
      ->setContentType($inputContentType)
      ->setBody($inputData)
    );
  }
  
  private function generateTestAttachment() {
    $objPHPExcel = new PHPExcel();
    
    // Test Data is directly pulled from the PHPExcel examples, so I'm not going to bother changing them.

    // Set document properties
    $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
                   ->setLastModifiedBy("Maarten Balliauw")
                   ->setTitle("Office 2007 XLSX Test Document")
                   ->setSubject("Office 2007 XLSX Test Document")
                   ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                   ->setKeywords("office 2007 openxml php")
                   ->setCategory("Test result file");


    // Add some data
    $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'Hello')
                ->setCellValue('B2', 'world!')
                ->setCellValue('C1', 'Hello')
                ->setCellValue('D2', 'world!');

    // Miscellaneous glyphs, UTF-8
    $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A4', 'Miscellaneous glyphs')
                ->setCellValue('A5', 'éàèùâêîôûëïüÿäöüç');

    // Rename worksheet
    $objPHPExcel->getActiveSheet()->setTitle('Simple');

    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    ob_start();
    $objWriter->save('php://output');
    $data = ob_get_contents();
    ob_end_clean();
    
    $this->message->attach(Swift_Attachment::newInstance()
      ->setFilename('test.xlsx')
      ->setContentType('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
      ->setBody($data)
    );
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

  private function generateMessage($emailInput) {
    $this->message = Swift_Message::newInstance('Activity - ' . $this->activityDetails['name_english'] . '/' . $this->activityDetails['name_chinese'])
      ->setFrom(array('eac_system@school.cdgfss.edu.hk' => 'EAC System'))
      ->setTo(array($emailInput))
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
