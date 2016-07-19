<?php
// I remade this class as OO.
// After thinking about it for a while, the $message building sequence may have race conditions if more than one e-mail is being generated.
// Best to keep it instantiated so each e-mail is its own object.

// ** Notes on Attachment functions **
// They have general names, but are actually very specific.  generateAttachments() is a generic method that depends on initializations done in
// generateActivityDetails() and generateStudentDetails(), which stores the data within the object's private variables

// I really should have made my own class to store the activity related data instead of attaching it to this mail class as private variables...
// In its current form it is going to be ugly if new types of data are needed

require_once 'swift/swift_required.php';

require_once 'models/pdo.php';
require_once 'models/pdo.cdgfss.php';
// require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';
require_once 'php/PHPExcel/Classes/PHPExcel.php';

class cdgfss_mail {
  
  private $transport;
  private $mailer;
  private $activity_id;
  private $email;
  private $pdoObj;
  private $textbody;
  private $htmlbody;
  private $excelObj;
  private $activityStudentHeading;
  private $activityStudents; // this is a PDOStatement object.  be careful of using a foreach on this because it will move the cursor, best to not use this at all.
  private $activityStudentsArray;  // this is a fetchAll() of the above PDO Statement object.  Perform foreach loops on this variable instead.
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
  
  public function sendMail($email, $activity_id) {
    $this->sendMailInit($email, $activity_id);
    $result = $this->mailer->send($this->message);
  }
  
  public function sendMailWithAttachment($email, $activity_id) {
    $this->sendMailInit($email, $activity_id);
    $this->generateExcelObject();
    $this->generateAttachments();
    $result = $this->mailer->send($this->message);
  }
  
  private function sendMailInit($email, $activity_id) {
    $this->setActivityId($activity_id);
    $this->setEmail($email);
    $this->initDb();
    $this->generateActivityDetails();
    $this->generateStudentDetails();
    $this->generateMessage();
  }

  private function setActivityId($activity_id) {
    // I'm not going to do thorough error checking.  activity_id is setup as an auto-incrementing integer in the database
    // PDO calls are using prepared statements, so this (int) cast isn't going to add much in terms of preventing SQL injection.
    // If someone changes the activity_id to non-integers, then it'll cause problems... but that's a pretty major change and
    // will probably break a ton of other things
    $this->activity_id = (int)$activity_id;
  }
  
  private function setEmail($email) {
    $this->email = $email;
  }

  private function initDb() {
    $this->pdoObj = new cdgfss_pdo();
  }
  
  private function generateExcelObject() {
    $this->excelObj = new PHPExcel();
    
    $this->excelObj->getProperties()->setCreator("CDGFSS")
                   ->setLastModifiedBy("CDGFSS")
                   ; // most of the other properties are not easily visible; not going to bother
                   
    // Generate Activity Details
    $this->excelObj->setActiveSheetIndex(0)
                ->setCellValue('A1', "Activity ID:")
                ->setCellValue('B1', $this->activity_id)
                ->setCellValue('A2', "Teacher in charge:")
                ->setCellValue('B2', $this->activityDetails['teacher'])
                ->setCellValue('A3', "Participating Unit:")
                ->setCellValue('B3', $this->activityDetails['unit'])
                ->setCellValue('A4', "Name of Activity / Competition (ENG):")
                ->setCellValue('B4', $this->activityDetails['name_english'])
                ->setCellValue('A5', "Name of Activity / Competition (CHI):")
                ->setCellValue('B5', $this->activityDetails['name_chinese'])
                ->setCellValue('A6', "Date:")
                ->setCellValue('B6', $this->activityDetails['date'])
                ->setCellValue('A7', "Time:")
                ->setCellValue('B7', $this->activityDetails['time'])
                ->setCellValue('A8', "Partner Organization (ENG):")
                ->setCellValue('B8', $this->activityDetails['partner_name_english'])
                ->setCellValue('A9', "Partner Organization (CHI):")
                ->setCellValue('B9', $this->activityDetails['partner_name_chinese'])
                ->setCellValue('A10', "Destination/Route:")
                ->setCellValue('B10', $this->activityDetails['destination'])
                ;
                
    // Generate Activity Students
    $counter = 0; // setCellValueByColumnAndRow() has column A = column 0
    $nextRow = 12; // leaving a blank row after the activity details.  Can be increased if necessary.
    foreach ($this->activityStudentHeading as $field) {
      $this->excelObj->setActiveSheetIndex(0)->setCellValueByColumnAndRow($counter, $nextRow, $field);
      $counter++;
    }
    $nextRow++;
    
    // Generates Student List below Activity Details and also a separate sheet with Students only.
    $nextStudentOnlyRow = 1;  // Rows start at 1, but Columns start at 0.
    $this->excelObj->createSheet(1);
    foreach ($this->activityStudentsArray as $row) {
      $counter = 0;
      foreach ($row as $field) {
        $this->excelObj->setActiveSheetIndex(0)->setCellValueByColumnAndRow($counter, $nextRow, $field);
        $this->excelObj->setActiveSheetIndex(1)->setCellValueByColumnAndRow($counter, $nextStudentOnlyRow, $field);
        $counter++;
      }
      $nextRow++;
      $nextStudentOnlyRow++;
    }


    // set all columns to autosize
    foreach(range('A','Z') as $columnID) {
        $this->excelObj->setActiveSheetIndex(0)->getColumnDimension($columnID)
            ->setAutoSize(true);
        $this->excelObj->setActiveSheetIndex(1)->getColumnDimension($columnID)
            ->setAutoSize(true);
    }
    
    $this->excelObj->setActiveSheetIndex(0)->setTitle('Activity Details');
    $this->excelObj->setActiveSheetIndex(1)->setTitle('Students Only');
    $this->excelObj->setActiveSheetIndex(0);
  }
  
  private function generateAttachments() {
    ob_start();
    $objWriter = PHPExcel_IOFactory::createWriter($this->excelObj, 'Excel2007');
    $objWriter->save('php://output');
    $data = ob_get_contents();
    ob_end_clean();
    
    // regex for stripping out invalid characters:
    // http://stackoverflow.com/a/2021729/3715973
    
    $filename = "{$this->activityDetails['name_english']} - {$this->activityDetails['name_chinese']}";
    $filename = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $filename);
    $filename = mb_ereg_replace("([\.]{2,})", '', $filename);
    
    $this->message->attach(Swift_Attachment::newInstance()
      ->setFilename("$filename.xlsx")
      ->setContentType('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
      ->setBody($data)
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
    
    ob_start();
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');
    $data = ob_get_contents();
    ob_end_clean();
    
    $this->message->attach(Swift_Attachment::newInstance()
      ->setFilename('test.xlsx')
      ->setContentType('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
      ->setBody($data)
    );
  }
  
  private function generateMessage() {
    $this->message = Swift_Message::newInstance('Activity - ' . $this->activityDetails['name_english'] . '/' . $this->activityDetails['name_chinese'])
      ->setFrom(array('eac_system@school.cdgfss.edu.hk' => 'EAC System'))
      ->setTo(array($this->email))
      ->setBody($this->textbody)
      ->addPart($this->htmlbody, 'text/html')
      ;
  }
  
  private function generateActivityDetails() {
    $listActivityStmt = $this->pdoObj->listActivity_Details($this->activity_id);
    if ($listActivityStmt->rowCount() <= 0) {
      exit(); // this can happen if the activity_id is invalid
    }
    $this->activityDetails = $listActivityStmt->fetchAll()[0];  // This moves the cursor on the PDOStatement object and it will return no results at future calls

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
  
  private function generateStudentDetails() {
    $this->activityStudentHeading = $this->pdoObj->columns_Activity_AllStudents();
    $this->activityStudents = $this->pdoObj->listActivity_AllStudents($this->activity_id);
    $this->activityStudentsArray = $this->activityStudents->fetchAll(); // I have to do this because the PDO Statement will iterate and the cursor cannot be reset
    // This causes the activityStudents PDOStatement cursor to be moved to the end

    $this->htmlbody .= "<br><hr><p><u>Students</u></p><table border=1 id='studentTable' style='td {border: thin solid red;}'><tr>";
    $this->textbody .= "** Students **\r\n\r\n";
    foreach ($this->activityStudentHeading as $field) {
      $this->htmlbody .= "<td>{$field}</td>";
      $this->textbody .= "{$field}	";
    }
    $count = 0;

    // the foreach used to be directly on the PDOStatement object, but because I need to iterate over the data multiple times, I had to perform
    // a fetchAll() above and store the data; iterating on the PDOStatement will cause the cursor to move and I cannot reset it.
    // a fetchAll() also moves the cursor on the PDOStatement, but I will have an array to work with after.
    foreach ($this->activityStudentsArray as $row) {
      // the $count is simply alternating the alternate BG color style since CSS blocks doesn't work in emails
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
}
?>
