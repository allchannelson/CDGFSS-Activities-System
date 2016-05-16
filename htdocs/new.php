<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <script type="text/javascript">
  function validateForm() {
    var returnValue = true;
    submitAlertStudentsHTML = document.getElementById("submitAlertStudents");
    submitAlertStudentsHTML.innerHTML = "";
    submitAlertHTML = document.getElementById("submitAlert");
    submitAlertHTML.innerHTML = "";

    var a=document.forms["form"]["activity[teacher]"];
    var b=document.forms["form"]["activity[unit]"];
    var c=document.forms["form"]["activity[name_english]"];
    var d=document.forms["form"]["activity[name_chinese]"];
    var e=document.forms["form"]["activity[date]"];
    
    if (a.value=="" ||
        b.value=="" ||
        c.value=="" ||
        d.value=="" ||
        e.value=="") {
      returnValue = false;
      submitAlertHTML.innerHTML = "Please complete following fields: "
      if (a.value=="") {
        highlightRed(a);
      }
      if ( b.value=="") {
        highlightRed(b);
      }
      if (c.value=="") {
        highlightRed(c);
      }
      if (d.value=="") {
        highlightRed(d);
      }
      if (e.value=="") {
        highlightRed(e);
      }
      submitAlertHTML.innerHTML = submitAlertHTML.innerHTML.slice(0, -2);  // Removing the trailing ", "
    }
    
    if (!checkIfStudentsAreSelected()) {
      returnValue = false;
      submitAlertStudentsHTML.innerHTML = "Please Select At Least One Student";
    }
    
    return returnValue;
  }
  
  function highlightRed(element) {
    prevSib = element.previousElementSibling;
    prevSib.style.color="red";
    submitAlertHTML.innerHTML = submitAlertHTML.innerHTML + " <span class='missingItem'>" + prevSib.innerHTML + "</span>, "
  }
  
  function checkIfStudentsAreSelected() {
    studentArray = document.getElementsByName("checkboxArray[]");
    studentArrayChecked = false;
    for (i = 0; i < studentArray.length; i++) {
      if (studentArray[i].checked) {
        studentArrayChecked = true;
      }
    }
    return studentArrayChecked;
  }
  </script>
  <style type="text/css">
    body {
    }
    
    #formHeaders .mandatory {
    }
    
    #formHeaders .mandatory::after {
      vertical-align: super;
      font-size: 0.8em;
      content: "*";
      color: red;
    }
    
    #formHeaders .label {
      float: left;
      width: 17em;
    }
    
    #formHeaders input {
      clear: right;
    }
    
    #formHeaders div.label:nth-child(2n) {
      background-color: rgb(255, 255, 204)
    }
    
    #formHeaders input[type="text"] {
      width: 40em;
      font-size: 1.2em;
    }
    
    #submitAlertStudents {
      color: red;
      font-weight: bold;
    }
    
    #submitAlert {
      margin-left: 1em;
      color: red;
      font-weight: bold;
    }
    
   .missingItem {
      color: blue;
    }
    
    .missingItem::after {
      content: "]";
      color: red;
    }
    
    .missingItem::before {
      content: "[";
      color: red;
    }
  </style>
</head>
<body>
<hr>
<form name="form" onsubmit="return validateForm()" action="submit.php" method="post">
<div id="formHeaders">
<p><u>Activity</u><p>
<div class="mandatory label">Teacher in charge</div>
<input type="text" name="activity[teacher]" maxlength=100 /><br>
<div class="mandatory label">Participating Unit</div>
<input type="text" name="activity[unit]" maxlength=100 /><br>
<div class="mandatory label">Name of Activity / Competition (ENG)</div>
<input type="text" name="activity[name_english]" maxlength=100 /><br>
<div class="mandatory label">Name of Activity / Competition (CHI)</div>
<input type="text" name="activity[name_chinese]" maxlength=100 /><br>
<div class="mandatory label">Date</div>
<input type="text" name="activity[date]" /><br>
<div class="label">Time:</div>
<input type="text" name="activity[time]" /><br>
<div class="label">Partner Organization (ENG)</div>
<input type="text" name="activity[partner_name_english]" maxlength=100 /><br>
<div class="label">Partner Organization (CHI)</div>
<input type="text" name="activity[partner_name_chinese]" maxlength=100 /><br>
<div class="label">Destination/Route:</div>
<input type="text" name="activity[destination]" maxlength=100 /><br>

<span class="mandatory"></span><span style="color: red;"> Mandatory</span>
<hr>
<input type="submit" value="Submit"><span id="submitAlert"></span><br><br>
</div>
<div id="submitAlertStudents"></div>
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
ORDER BY `form` asc, `class` asc, `class_number` asc
;LIMIT 10
";

$queryResult = $pdo->query($query);

foreach ($queryResult as $key => $row) {
  echo(sprintf("<input type='checkbox' name='checkboxArray[]' id = '%s' value='%s,%s' />", $key, $row['student_index'], $row['enrollment_year']));
  // $row['student_index'], $row['student_number'], $row['name_chinese'], $row['name_english'], $row['gender'], $row['active']
  // e(sprintf("Index: %d  Student ID: %s  %s  %s  %s  Active: %d",
  $output = sprintf("<label for='%s'>S%s%s%s %s %s %s %s %s %s</label>",
    f($key),
    f($row['form']),
    f($row['class']),
    f($row['class_number']),
    f($row['name_chinese']),
    f($row['name_english']),
    (strtoupper($row['gender']) == "M"?"<span style='color: blue'>♂</span>":(strtoupper($row['gender']) == "F"?"<span style='color: pink'>♀</span>":"??")),
    f($row['student_number']),
    f($row['enrollment_year']),
    f($row['house']),
    f($row['active']),
    null);
  echo($output);
  echo("<br>");

}
echo("</checkbox>");

$pdo = null;
?>
</form>
</body>
</html>
