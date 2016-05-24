<html>
<head>
  <?php 
    require 'models/pdo.php';
    require 'models/pdo.cdgfss.php';

    $cdgfssDB = new cdgfss_pdo();
  ?>
  <script src="https://code.jquery.com/jquery-1.12.3.min.js"
    integrity="sha256-aaODHAgvwQW1bFOGXMeX+pC4PZIPsvn2h1sArYOhgXQ="   
  crossorigin="anonymous"></script>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
  <!--
    jQuery dependencies:
    datepicker
  -->
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <script type="text/javascript">
  var StudentCheckboxErrorMessage = "Please Select At Least One Student";
  validateForm.onChangeBinding = false;
  function resetForm() {
    validateForm.onChangeBinding = false;
    
    var a=document.forms["form"]["activity[teacher]"];
    var b=document.forms["form"]["activity[unit]"];
    var c=document.forms["form"]["activity[name_english]"];
    var d=document.forms["form"]["activity[name_chinese]"];
    var e=document.forms["form"]["activity[date]"];
    var f=document.forms["form"]["datepicker"];

    submitAlertStudentsHTML = document.getElementById("submitAlertStudents");
    submitAlertHTML = document.getElementById("submitAlert");
    
    submitAlertStudentsHTML.innerHTML = "";
    submitAlertHTML.innerHTML = "";
    
    resethighlight(a);
    resethighlight(b);
    resethighlight(c);
    resethighlight(d);
    resethighlight(e);
    
    unbind(a);
    unbind(b);
    unbind(c);
    unbind(d);
    unbind(e);
    unbind(f);
    
    studentArray = document.getElementsByName("checkboxArray[]");
    for (i = 0; i < studentArray.length; i++) {
      unbind(studentArray[i]);
    }

    document.getElementById("submitButton").disabled = false;
    document.getElementById("studentsSelected").innerHTML = 0;
  }
  
  function validateForm() {
    var returnValue = true;
    submitAlertStudentsHTML = document.getElementById("submitAlertStudents");
    submitAlertHTML = document.getElementById("submitAlert");
    
    // resetForm() is not called because I do not want to unbind the events from the checkboxes
    submitAlertStudentsHTML.innerHTML = "";
    submitAlertHTML.innerHTML = "";

    var a=document.forms["form"]["activity[teacher]"];
    var b=document.forms["form"]["activity[unit]"];
    var c=document.forms["form"]["activity[name_english]"];
    var d=document.forms["form"]["activity[name_chinese]"];
    var e=document.forms["form"]["activity[date]"];
    var f=document.forms["form"]["datepicker"];
    
    if (f.value==="") {e.value="";}
    // activity[date] is a hidden field, populated by the datepicker
    // activity[date] format is yyyymmdd for inserting into the DB
    // datepicker format is dd/mm/yyyy

    resethighlight(a);
    resethighlight(b);
    resethighlight(c);
    resethighlight(d);
    resethighlight(e);
    // f element has no label to reset
    
    if (typeof validateForm.onChangeBinding == 'undefined' || validateForm.onChangeBinding == false) {
      validateForm.onChangeBinding = true;
      // jQuery version:
      // $(a).on('input propertychange change click', function() {validateForm();});
      myBind(a);
      myBind(b);
      myBind(c);
      myBind(d);
      myBind(e);
      myBind(f);
      studentArray = document.getElementsByName("checkboxArray[]");
      for (i = 0; i < studentArray.length; i++) {
        myBind(studentArray[i]);
      }
    }

    if (a.value=="" ||
        b.value=="" ||
        c.value=="" ||
        d.value=="" ||
        e.value=="" ||
        f.value=="") {
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
      // f element is a hidden input, so no label exists
      submitAlertHTML.innerHTML = submitAlertHTML.innerHTML.slice(0, -2);  // Removing the trailing ", "
    }
    
    if (!checkIfStudentsAreSelected()) {
      returnValue = false;
      submitAlertStudentsHTML.innerHTML = StudentCheckboxErrorMessage;
    }
    
    submitButtonObj = document.getElementById("submitButton");
    if (returnValue == false) {
      submitButtonObj.disabled = true;
    } else {
      submitButtonObj.disabled = false;
    }
    
    return returnValue;
  }
  
  function unbind(element) {
    var attributes = ["oninput", "onpropertychange", "onchange", "onclick"];
    for (unbind.i = 0; unbind.i < attributes.length; unbind.i++) {
      element.setAttribute(attributes[unbind.i], (element.getAttribute(attributes[unbind.i]) || "").replace("validateForm();", ""));
    }
  }
  
  function myBind(element) {
    var attributes = ["oninput", "onpropertychange", "onchange", "onclick"];
    // binding maintains existing bind.
    for (myBind.i = 0; myBind.i < attributes.length; myBind.i++ ) {
      element.setAttribute(attributes[myBind.i],(element.getAttribute(attributes[myBind.i]) || "") + "validateForm();");
    }
  }
  
  function resethighlight(element) {
    prevSib = element.previousElementSibling;
    prevSib.classList.remove("missingLabel");
  }
  
  function highlightRed(element) {
    prevSib = element.previousElementSibling;
    // prevSib.style.color="red";
    prevSib.classList.add("missingLabel");
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
  
  function studentTotal() {
    var studentArray = document.getElementsByName("checkboxArray[]");
    var totalCount = 0;
    for (i = 0; i < studentArray.length; i++) {
      if (studentArray[i].checked) {
        totalCount += 1;
      }
    }
    document.getElementById("studentsSelected").innerHTML = totalCount;
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
    
    #formHeaders div.label:nth-of-type(2n) {
      background-color: rgb(255, 255, 204)
    }
    
    #formHeaders input[type="text"] {
      width: 40em;
      font-size: 1.2em;
    }
    
    #submitAlertStudents {
      color: red;
      font-weight: bold;
      margin-left: 1em;
    }
    
    #submitAlert {
      margin-left: 1em;
      color: red;
      font-weight: bold;
    }
    
   .missingLabel {
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
    
    #main {
      width: 75em;
    }
    
    #left {
      float: left;
      width: 45%;
    }
    
    #right {
      float: right;
      width: 55%;
    }
    
    .option {
      float: left;
      margin: 0.5em;
    }
    
    input[value="Check"], input[value="Uncheck"], input[value="Uncheck All Students"], input[type="Reset"] {
      display: block;
    }
    
    input[value='Uncheck'] {
      margin-top: 2em;
    }
    input[value='Uncheck All Students'] {
      margin-top: 3em;
    }
    input[type='Reset'] {
      margin-top: 5em;
    }
    
    .gender {
      font-size: 1.2em;
    }
    
    .male {
      color: blue;
    }
    
    .female {
      color: pink;
    }
    
    .hiddenStudent {
      display: none;
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
<input type="hidden" name="activity[date]" id="realDate" />
<input type="text" name="datepicker" id="datepicker" /><br>
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
<input type="submit" id="submitButton" value="Submit"><span id="submitAlert"></span><br><br>
</div>
<div id="main">
<div id="left">
<u>Students</u><span id="submitAlertStudents"></span><br>
<span id="studentsSelected">0</span> selected<br>
<div class="option">
Show --
</div>
<div class="option">
  Form:
  <select id="studentFormFilter" oninput="studentFilter();">
    <option value="">All</option>
    <?php foreach ($cdgfssDB->listCurrentForm(PDO::FETCH_NUM) as $outputRow): 
      // output is single column result, so fetching indexed array and doing $row[0] for quick access ?>
      <option value="<?=$outputRow[0]?>">S<?=$outputRow[0]?></option>
    <?php endforeach; ?>
  </select>
</div>
<div class="option">
  Class:
  <select id="studentClassFilter" oninput="studentFilter();">
    <option value="">All</option>
    <?php foreach ($cdgfssDB->listCurrentClass(PDO::FETCH_NUM) as $outputRow): 
      // output is single column result, so fetching indexed array and doing $row[0] for quick access ?>
      <option value="<?=$outputRow[0]?>"><?=$outputRow[0]?></option>
    <?php endforeach; ?>
  </select>
</div>
<script type="text/javascript">
  function studentFilter() {
    // Hide all students, then unhide the selected ones
    var studentArray = document.querySelectorAll("[data-student_form_class]");
    for (studentFilter.i = 0; studentFilter.i < studentArray.length; studentFilter.i ++) {
      studentArray[studentFilter.i].classList.add("hiddenStudent");
      studentArray[studentFilter.i].nextElementSibling.classList.add("hiddenStudent");
    }
    var formValue = document.getElementById("studentFormFilter").value;
    var classValue = document.getElementById("studentClassFilter").value;
    var formClass = formValue + classValue;
    if (classValue === "" && formValue === "") {
      var filterArray = document.querySelectorAll("[data-student_form_class]");
    } else {
      var filterArray = document.querySelectorAll("[data-student_form_class*='" + formClass +"']");
    }
    for (studentFilter.i = 0; studentFilter.i < filterArray.length; studentFilter.i ++) {
      filterArray[studentFilter.i].classList.remove("hiddenStudent");
      filterArray[studentFilter.i].nextElementSibling.classList.remove("hiddenStudent");
    }
  }
</script>
<br><br>
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
  echo(sprintf("<div><input type='checkbox' name='checkboxArray[]' id='%s' value='%s,%s' class='studentCheck' data-student_form_class='%s%s' data-student_gender='%s' onclick='studentTotal();'/>", $key, $row['student_index'], $row['enrollment_year'], $row['form'], $row['class'], $row['gender']));
  // $row['student_index'], $row['student_number'], $row['name_chinese'], $row['name_english'], $row['gender'], $row['active']
  // e(sprintf("Index: %d  Student ID: %s  %s  %s  %s  Active: %d",
  $output = sprintf("<label for='%s'>S%s%s%s %s %s %s</label>",
    f($key),
    f($row['form']),
    f($row['class']),
    f($row['class_number']),
    f($row['name_chinese']),
    f($row['name_english']),
    (strtoupper($row['gender']) == "M"?"<span class='gender male'>♂</span>":(strtoupper($row['gender']) == "F"?"<span class='gender female'>♀</span>":"??"))
    );
  echo($output);
  echo("</div>");

}
echo("</checkbox>");

$pdo = null;
?>
</div>
<div id="right">
<u>Select Multiple Students</u><br>
Note:  This will select hidden students.<br>
<div class="option">
  Form:
  <select id="multicheck_form">
    <option value="">All</option>
    <?php foreach ($cdgfssDB->listCurrentForm(PDO::FETCH_NUM) as $outputRow): 
      // output is single column result, so fetching indexed array and doing $row[0] for quick access ?>
      <option value="<?=$outputRow[0]?>">S<?=$outputRow[0]?></option>
    <?php endforeach; ?>
  </select>
</div>
<div class="option">
  Class:
  <select id="multicheck_class">
    <option value="">All</option>
    <?php foreach ($cdgfssDB->listCurrentClass(PDO::FETCH_NUM) as $outputRow): 
      // output is single column result, so fetching indexed array and doing $row[0] for quick access ?>
      <option value="<?=$outputRow[0]?>"><?=$outputRow[0]?></option>
    <?php endforeach; ?>
  </select>
</div>
<div class="option">
  Gender:
  <select id="multicheck_gender">
    <option value="">All</option>
    <option value="F">F</option>
    <option value="M">M</option>
  </select>
</div>
<div style="clear: both;">
  <input type="button" value="Check" onclick="multiCheck()" />
  <input type="button" value="Uncheck" onclick="multiCheck(true)" />
  <input type="button" value="Uncheck All Students" onclick="uncheckAllStudents()" />
  <input type="reset" value="** RESET FORM **" onclick="resetForm()"/>
</div>
<script type="text/javascript">
  function checkAll(elementArr) {
    for (elementIndex in elementArr) {
      elementArr[elementIndex].checked = true;
    }
  }
  
  function uncheckAll(elementArr) {
    for (elementIndex in elementArr) {
      elementArr[elementIndex].checked = false;
    }
  }
  
  function uncheckAllStudents() {
    uncheckAll(document.querySelectorAll('[data-student_form_class]'));
    if (validateForm.onChangeBinding) {
      validateForm();
    }
    studentTotal();
  }
  
  function multiCheck(uncheck) {
    uncheck = uncheck || false; // IE does not handle default parameter
    FormClassSelector = "";  // initialize for consistent behavior... 
    iForm = document.getElementById("multicheck_form").value;
    sClass = document.getElementById("multicheck_class").value;
    sGender = document.getElementById("multicheck_gender").value;
    if (iForm) {
      FormClassSelector = iForm;
    }
    if (sClass) {
      if (FormClassSelector !== "") {
        FormClassSelector += sClass;
      } else {
        FormClassSelector = sClass;
      }
    }
    // document.querySelectorAll("[data-student_form_class*='6']")
    selectorString = "";
    if (FormClassSelector !== "") {
      selectorString += "[data-student_form_class*='" + FormClassSelector + "']";
    }
    if (sGender) {
      selectorString += "[data-student_gender='" + sGender + "']";
    }
    if (selectorString !== "") {
      if (uncheck) {
        uncheckAll(document.querySelectorAll(selectorString));
      } else {
        checkAll(document.querySelectorAll(selectorString));
      }
    } else {
      if (uncheck) {
        uncheckAll(document.querySelectorAll("[data-student_form_class]"));
      } else {
        checkAll(document.querySelectorAll("[data-student_form_class]"));
      }
    }
    FormClassSelector = "";
    
    if (validateForm.onChangeBinding) {
      validateForm();
    }
    studentTotal();
  }
  
  $("#datepicker").datepicker(
    $.extend({
      altField: '#realDate',
      altFormat: 'yy-mm-dd',
      dateFormat: 'dd/mm/yy'
    })
  );
</script>

<!-- closing section for <div id="right"> -->
</div> 


<!-- closing section for <div id="main"> -->
<span style="clear: both;"></span>
</div>
</form>
</body>
</html>
