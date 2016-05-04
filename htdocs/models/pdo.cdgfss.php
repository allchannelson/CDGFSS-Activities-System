<?php
class cdgfss_pdo extends PdoModel {
  private $dsn = "mysql:dbname=activity_prototype;host=localhost;charset=utf8";

  private $select_only_user = 'select_only';
  private $select_only_password = 'xBX8swTSrGawmB5r'; // dev 15ys

  private $submit_user = 'submit';
  private $submit_password = 'DqcJ3WeWWYQBTG6r'; // dev 15ys

  public function __construct() {
    $this->initSelect();
  }
  
  public function initSelect() {
    echo("Init Select()<br>\n");
    $this->initPdo($this->dsn, $this->select_only_user, $this->select_only_password);
    echo("Done<br>\n");
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
  
  private function queryCheck($args) {
    if ($args === false) {
      return array(array("SQL Error"));
      // this is done because the query output is most likely being processed by nested FOREACH, so it requires an array in an array for the FOREACH to not error out.
    } else {
      return $args;
    }
  }
  
  private function myQueryAssoc($args) {
    return $this->queryCheck($this->db->query($args, PDO::FETCH_ASSOC));
  }
  
  private function myQueryNum($args) {
    return $this->queryCheck($this->db->query($args, PDO::FETCH_NUM));
  }
  
  private function myQueryBoth($args) {
    // nothing uses this at the moment... probably not necessary
    return $this->queryCheck($this->db->query($args));
  }
  
  private function myQuery($query, $args) {
    switch ($args) {
      case PDO::FETCH_ASSOC:
        return $this->myQueryAssoc($query);
        break;
      case PDO::FETCH_NUM:
        return $this->myQueryNum($query);
        break;
      default:
        return $this->myQueryAssoc($query);
    }
  }
  
  public function reportActivity_AllActivities_AllStudents($args = PDO::FETCH_ASSOC) {
    // The UNION exists so the output contains the column name. 
    // This is probably the simplest way to control the output and not have to do associative array with $key => $value processing.
    $query = "
    (SELECT 'Act_Name_CHI', 'Act_Name_ENG', 'Act_Date', 'Act_Teacher', 'Std_Name_CHI', 'Std_Name_ENG', 'Class')
    UNION
    (SELECT act.name_chinese as 'Activity CHI', act.name_english 'Activity ENG', act.date, act.teacher, s.name_chinese, s.name_english, CONCAT(syi.form, syi.class, syi.class_number) AS 'Class'
    FROM `activity` act
    INNER JOIN `activity_student` acts
      ON act.activity_index = acts.activity_index
    INNER JOIN `student_yearly_info` syi
      ON acts.student_index = syi.student_index 
      AND acts.student_enrollment_year = syi.enrollment_year
    INNER JOIN `student` s
      ON syi.`student_index` = s.`student_index`
    WHERE 1
    ORDER BY act.name_english)";
    return $this->myQuery($query, $args);
  }
}
?>
