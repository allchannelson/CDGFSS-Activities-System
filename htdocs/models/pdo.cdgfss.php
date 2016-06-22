<?php

/*
Need to test:
Adding new students.  Probably need a max(enrolled year) somewhere.
*/
class cdgfss_pdo extends PdoModel {
  private $dsn = "mysql:dbname=activity_prototype;host=localhost;charset=utf8";

  private $select_only_user = 'select_only';
  private $select_only_password = 'xBX8swTSrGawmB5r'; // dev 15ys

  private $submit_user = 'submit';
  private $submit_password = 'DqcJ3WeWWYQBTG6r'; // dev 15ys
  
  private $initSelect = false;
  private $initSubmit = false;

  public function __construct() {
    // moving object initialization to the query functions instead, when it is needed
    // $this->initSelect();
  }
  
  private function initSelect() {
    if (!$this->initSelect) {
      $this->initSelect = true;
      $this->initSubmit = false;
      $this->initPdo($this->dsn, $this->select_only_user, $this->select_only_password);
    }
  }
  
  private function initSubmit() {
    if (!$this->initSubmit) {
      $initSubmit = true;
      $initSelect = false;
      $this->initPdo($this->dsn, $this->submit_user, $this->submit_password);
    }
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
      // this is done because the query output is being processed by nested FOREACH,
      // so it requires an array in an array for the FOREACH to not error out.
      // return array(array("SQL Error"));
      
      // actually, the above just doesn't work all the time, need a better way to display an error... 
      return null;
      
      // need to get more error details...
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
    // if the fetch mode is not specific, a foreach loop will produce doubled up results
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
  
  public function columns_AllActivities_AllStudents() {
    return array("Act_Name_CHI","Act_Name_ENG","Act_Date","Act_Teacher","Std_Name_CHI","Std_Name_ENG","Class");
  }
  
  public function reportActivity_AllActivities_AllStudents($args = PDO::FETCH_ASSOC) {
    $this->initSelect();
    // column title is for debugging.  They are inaccessible from the PHP front-end.
    // column title generated from columns_AllActivities_AllStudents()
    $query = "
      SELECT act.name_chinese as 'Activity CHI', act.name_english 'Activity ENG', act.date, act.teacher, s.name_chinese, s.name_english, CONCAT(syi.form, syi.class, syi.class_number) AS 'Class'
        FROM `activity` act
       INNER JOIN `activity_student` acts
          ON act.activity_index = acts.activity_index
  INNER JOIN `student_yearly_info` syi
          ON acts.student_index = syi.student_index 
         AND acts.student_enrollment_year = syi.enrollment_year
  INNER JOIN `student` s
          ON syi.`student_index` = s.`student_index`
       ORDER BY act.name_english asc, syi.form asc, syi.class asc, syi.class_number asc";
    return $this->myQuery($query, $args);
  }
  
  public function listCurrentForm($args = PDO::FETCH_ASSOC) {
    $this->initSelect();
    $query = "
      SELECT distinct form
        FROM student_yearly_info syi
       WHERE syi.enrollment_year = (select max(enrollment_year) from `student_yearly_info`)
    ORDER BY form asc;";
    return $this->myQuery($query, $args);
  }
  
  public function listCurrentFormClass($args = PDO::FETCH_ASSOC) {
    $this->initSelect();
    $query = "
      SELECT distinct concat(form,class)
        FROM student_yearly_info syi
       WHERE syi.enrollment_year = (select max(enrollment_year) from `student_yearly_info`)
    ORDER BY form asc, class asc;";
    return $this->myQuery($query, $args);
  }
  
  public function listPreviousYearStudentFormClassNumber($args = PDO::FETCH_ASSOC) {
    $this->initSelect();
    $query = "
      SELECT student_index, concat(form,class,class_number) as formclassnumber
        FROM student_yearly_info syi
       WHERE syi.enrollment_year = (select max(enrollment_year) - 1 from `student_yearly_info`)
    ORDER BY form asc, class asc, class_number asc;";
    return $this->myQuery($query, $args);
  }
  
  public function listCurrentClass($args = PDO::FETCH_ASSOC) {
    $this->initSelect();
    $query = "
      SELECT distinct class
        FROM student_yearly_info syi 
       WHERE syi.enrollment_year = (select max(enrollment_year) from `student_yearly_info`)
    ORDER BY class asc;";
    return $this->myQuery($query, $args);
  }
  
  public function listCurrentStudent($args = PDO::FETCH_ASSOC) {
    $this->initSelect();
    $query = "
      SELECT s.student_index, student_number, name_chinese, name_english, gender, active, enrollment_year, form, class, class_number, house
        FROM `student` s
  INNER JOIN `student_yearly_info` syi
          ON s.student_index = syi.student_index
       WHERE syi.enrollment_year = (select max(enrollment_year) from `student_yearly_info`)
    ORDER BY `form` asc, `class` asc, `class_number` asc";
    return $this->myQuery($query, $args);
  }

  public function listActivity_AllStudents($activity_index, $args = PDO::FETCH_ASSOC) {
    $this->initSelect();
    $query = "
    SELECT `s`.`student_number`, `s`.`name_english`, `s`.`name_chinese`, `s`.`gender`, `syi`.`student_index`, `syi`.`enrollment_year`, `syi`.`form`, `syi`.`class`, `syi`.`class_number`, `syi`.`house`
      FROM `student_yearly_info` `syi`
      JOIN `student` `s`
        ON `s`.`student_index` = `syi`.`student_index`
     WHERE (`syi`.`student_index`, `syi`.`enrollment_year`) IN (
           SELECT `student_index`, `enrollment_year`
             FROM `activity_student`
            WHERE `activity_index` = $activity_index)";
    return $this->myQuery($query, $args);
  }
  
  public function listActivity_Details($activity_index, $args = PDO::FETCH_ASSOC) {
    $this->initSelect();
    $query = "
    SELECT `activity_index`, `teacher`, `unit`, `name_english`, `name_chinese`, `date`, `time`, `partner_name_english`, `partner_name_chinese`, `destination`
      FROM `activity`
     WHERE activity_index = $activity_index";
    return $this->myQuery($query, $args);
  }
}
?>
