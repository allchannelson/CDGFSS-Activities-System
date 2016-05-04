<?php
class cdgfss_pdo extends PdoModel {
  private $dsn = "mysql:dbname=activity_prototype;host=localhost;charset=utf8";

  private $select_only_user = 'select_only';
  private $select_only_password = 'xBX8swTSrGawmB5r';

  private $submit_user = 'submit';
  private $submit_password = 'DqcJ3WeWWYQBTG6r';

  public function __construct() {
    $this->initSelect();
  }
  
  public function initSelect() {
    echo("Init Select()\n");
    $this->initPdo($this->dsn, $this->select_only_user, $this->select_only_password);
    echo("Done\n");
  }
  
  public function initSubmit() {
    $this->initPdo($dsn, $submit_user, $submit_password);
  }
  
  private function initPdo($aDSN, $aUser, $aPassword) {
    try {
      parent::__construct(new PDO($aDSN, $aUser, $aPassword));
    } catch (PDOException $e) {
      echo(htmlentities(sprintf("Connection failed: %s\n", $e->getMessage())));
      exit();
    }
  }
  
  public function reportActivity_AllActivities_AllStudents() {
    return $this->db->query("
    SELECT act.name_chinese as 'Activity CHI', act.name_english 'Activity ENG', act.date, act.teacher, s.name_chinese, s.name_english, CONCAT(syi.form, syi.class, syi.class_number) AS 'Class'
    FROM `activity` act
    INNER JOIN `activity_student` acts
      ON act.activity_index = acts.activity_index
    INNER JOIN `student_yearly_info` syi
      ON acts.student_index = syi.student_index 
      AND acts.student_enrollment_year = syi.enrollment_year
    INNER JOIN `student` s
      ON syi.`student_index` = s.`student_index`
    WHERE 1
    ORDER BY act.name_english;");
  }
}
?>