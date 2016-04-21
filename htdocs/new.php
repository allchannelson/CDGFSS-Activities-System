<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <script type="text/javascript">
  function validateForm() {
    var a=document.forms["form"]["activity[name_english]"].value;
    var b=document.forms["form"]["activity[name_chinese]"].value;
    var c=document.forms["form"]["activity[date]"].value;
    var d=document.forms["form"]["activity[teacher]"].value;
    if ((a==null || a=="") ||
        (b==null || b=="") ||
        (c==null || c=="") ||
        (d==null || d=="")) {
      alert("Please Fill All Required Field");
      return false;
    }
  }
  </script>
</head>
<body>
<hr>
<form name="form" onsubmit="return validateForm()" action="submit.php" method="post">
<u>Activity</u><br>
Activity ENG Name: <input type="text" name="activity[name_english]" length=100></input><br>
Activity CHI Name: <input type="text" name="activity[name_chinese]" length=100></input><br>
Date: <input type="text" name="activity[date]" length=40></input><br>
Teacher: <input type="text" name="activity[teacher]" length=40></input><br>
<hr>
<u>Students</u><br>
<?php
$dbname = 'activity_prototype';
$user = 'select_only';
$password = 'xBX8swTSrGawmB5r';
$dsn = "mysql:dbname=$dbname;host=localhost;charset=utf8";

/* check connection */
try {
  $pdo = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
  e(sprintf("Connection failed: %s\n", $e->getMessage()));
  exit();
}

/* custom function for HTML output */
function e($arg_1) {
   echo(htmlentities($arg_1));
}

function f($arg_1) {
   return htmlentities($arg_1);
}

$query = "SELECT * FROM `student` s
INNER JOIN `student_yearly_info` syi
ON s.student_index = syi.student_index
WHERE syi.enrollment_year = (select max(enrollment_year) from `student_yearly_info`)
LIMIT 10
;ORDER BY `form` asc, `class` asc, `class_number` asc
";

$queryResult = $pdo->query($query);

foreach ($queryResult as $row) {
  echo(sprintf("<input type='checkbox' name='checkboxArray[]' value='%s,%s'>", $row['student_index'], $row['enrollment_year']));
  // $row['student_index'], $row['student_number'], $row['name_chinese'], $row['name_english'], $row['gender'], $row['active']
  // e(sprintf("Index: %d  Student ID: %s  %s  %s  %s  Active: %d",
  $output = sprintf("Index: %s  Student ID: %s %s %s %s %d %d%s%d %s",
    f($row['student_index']),
    f($row['student_number']),
    f($row['name_chinese']),
    f($row['name_english']),
    (strtoupper($row['gender']) == "M"?"<span style='color: blue'>♂</span>":(strtoupper($row['gender']) == "F"?"<span style='color: pink'>♀</span>":"??")),
    f($row['enrollment_year']),
    f($row['form']),
    f($row['class']),
    f($row['class_number']),
    f($row['house']),
    f($row['active']),
    null);
  echo($output);
  echo("</input><br>");

}
echo("</checkbox>");

$pdo = null;
?>
<input type="submit" value="Submit">
</form>
</body>
</html>
