<!DOCTYPE html>
<html>
<head>
  <?php 
    require_once 'models/pdo.php';
    require_once 'models/pdo.cdgfss.php';
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
  var STUDENT_CHECKBOX_ERROR_MESSAGE = "Please Select At Least One Student";
  validateForm.onChangeBinding = false;
  function resetForm() {
    /* This function may have redundant parts.  studentFilter() was added when I figured out to setup an anonymous function to have the function
       trigger after the reset.  Some of the forms may redundantly reset fields */
    
    // I really should refactor this function and validateForm()... but I don't have the time to go though it.
    // If new fields are added, both functions need to be updated to validate properly
    setTimeout(function() {
      validateForm.onChangeBinding = false;
      
      var a=document.forms["form"]["activity[teacher]"];
      var b=document.forms["form"]["activity[unit]"];
      var c=document.forms["form"]["activity[name_english]"];
      var d=document.forms["form"]["activity[name_chinese]"];
      var e=document.forms["form"]["activity[date]"];
      var f=document.forms["form"]["datepicker"];
      var g=document.forms["form"]["activity[email]"];
      // make sure the object reference here is valid and works
      
      /* server side validation also needs to be added in submit.php 
         see [VALIDATION]
      */

      submitAlertStudentsHTML = document.getElementById("submitAlertStudents");
      submitAlertHTML = document.getElementById("submitAlert");
      
      submitAlertStudentsHTML.innerHTML = "";
      submitAlertHTML.innerHTML = "";
      
      resethighlight(a);
      resethighlight(b);
      resethighlight(c);
      resethighlight(d);
      resethighlight(e);
      resethighlight(g);
      
      unbind(a);
      unbind(b);
      unbind(c);
      unbind(d);
      unbind(e);
      unbind(f);
      unbind(g);
      
      studentArray = document.getElementsByName("checkboxArray[]");
      for (i = 0; i < studentArray.length; i++) {
        unbind(studentArray[i]);
      }

      document.getElementById("submitButton").disabled = false;
      document.getElementById("studentsSelected").innerHTML = 0;
      studentFilter();
    }, 0);
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
    var g=document.forms["form"]["activity[email]"];
    
    if (f.value==="") {e.value="";}
    // activity[date] is a hidden field, populated by the datepicker
    // activity[date] format is yyyymmdd for inserting into the DB
    // datepicker format is dd/mm/yyyy

    resethighlight(a);
    resethighlight(b);
    resethighlight(c);
    resethighlight(d);
    resethighlight(e);
    resethighlight(g);
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
      myBind(g);
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
        f.value=="" ||
        g.value==""
        ) {
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
      if (g.value=="") {
        highlightRed(g);
      }
      // f element is a hidden input, so no label exists
      submitAlertHTML.innerHTML = submitAlertHTML.innerHTML.slice(0, -2);  // Removing the trailing ", "
    }
    
    if (!checkIfStudentsAreSelected()) {
      returnValue = false;
      submitAlertStudentsHTML.innerHTML = STUDENT_CHECKBOX_ERROR_MESSAGE;  // constant //
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
    // binding preserves existing bind
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
  
  function UncheckWithEnter(obj) {
    var doNotUncheckState = document.getElementById("doNotUncheck").checked;
    var thisState = obj.checked;
    if (doNotUncheckState) {
      obj.checked = true;
    } else {
      obj.checked = (thisState ? false : true);
    }
  }
  
  function searchPreviousYearToggle() {
    // document.querySelectorAll("[data-student_form_class][0].nextElementSibling.children[0].innerHTML = "Hello World";
    // document.querySelectorAll("[data-student_form_class]")[0].getAttribute("data-previous_form_classnumber");
    var studentArray = document.querySelectorAll("[data-student_form_class]");
    if (typeof(searchPreviousYearToggle.toggle) == 'undefined' || searchPreviousYearToggle.toggle == false) {
      searchPreviousYearToggle.toggle = true;
      for (searchPreviousYearToggle.i = 0; searchPreviousYearToggle.i < studentArray.length; searchPreviousYearToggle.i++) {
        thisElement = studentArray[searchPreviousYearToggle.i];
        thisElementPrevYear = thisElement.getAttribute("data-previous_form_classnumber");
        if (thisElementPrevYear != "") {
          thisElement.nextElementSibling.children[0].innerHTML = "(" + thisElement.getAttribute("data-previous_form_classnumber") + ") ";
        } else {
          thisElement.nextElementSibling.children[0].innerHTML = "(n/a) ";
        }
      }
    } else {
      searchPreviousYearToggle.toggle = false;
      for (searchPreviousYearToggle.i = 0; searchPreviousYearToggle.i < studentArray.length; searchPreviousYearToggle.i++) {
        thisElement = studentArray[searchPreviousYearToggle.i];
        thisElement.nextElementSibling.children[0].innerHTML = "";
      }
    }
  }
  
  function studentSearchEnter() {
    var clearAfterEnterState = document.getElementById("clearAfterEnter").checked;
    var clearClassNumberState = document.getElementById("clearClassNumberAfterEnter").checked;
    var checkAllState = document.getElementById("checkAll").checked;
    var searchPreviousYearState = document.getElementById("searchPreviousYear").checked;
    
    if (checkAllState) {
      var shownCheckBoxes = document.querySelectorAll("[data-student_form_class]:not(.hiddenStudentFilter):not(.hiddenStudent)");
      for (studentSearchEnter.i = 0; studentSearchEnter.i < shownCheckBoxes.length; studentSearchEnter.i++) {
        UncheckWithEnter(shownCheckBoxes[studentSearchEnter.i]);
      }
    } else {
      var firstCheckBox;
      if (firstCheckBox = document.querySelectorAll("[data-student_form_class]:not(.hiddenStudentFilter):not(.hiddenStudent)")[0]) {
        UncheckWithEnter(firstCheckBox);
      }
    }
    if (clearAfterEnterState) {
      document.getElementById("studentSearchInput").value = "";
    }
    
    if (clearClassNumberState) {
      document.getElementById("studentSearchInput").value = document.getElementById("studentSearchInput").value.replace(/[0-9]+$/, "");
    }
    
    if (searchPreviousYearState) {
      // probably don't need to do anything, since the prev year will populate from clicking the checkbox
    }
    

    // studentSearch(); // triger this so the searched students update
    // for usability, maybe not update the student search list after enter, because it is hard to tell if the student is checked
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
      width: 25em;
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
      width: 60em;
    }
    
    #left {
      float: left;
      width: 50%;
      margin-bottom: 10em;
    }
    
    #right {
      float: right;
      width: 50%;
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
    
    .hiddenStudent, .hiddenStudentFilter, .hidden, .hiddenStudentUnchecked {
      display: none;
    }
    
    #studentList {
    }
    
    #studentSearchInput {
      width: 20em;
    }
    
    #advblock {
      margin-bottom: 1em;
    }
    
    #advblock #showAllCheckedButton {
      margin-top: 1em;
    }
    
    #advoptions {
      width: 80%;
      border: 5px ridge #cccccc;
      padding: 0.3em;
    }
    
    #advoptions p {
      margin: 0.5em initial;
    }
    
    #advoptioncheckboxes {
      margin-top: 1em;
    }
    
    #advoptioncheckboxes input {
      float: left;
      clear: left;
      margin-top: 0.5em;
    }
    
    #advoptioncheckboxes label {
      display: block;
      line-height: 1.5em;
      overflow: hidden;
    }
    
    .clearLeft {
      clear: left;
    }
    
    .prevYear {
    }
    
  </style>
</head>
<body>
<hr>
<form name="form" onsubmit="return validateForm()" onReset="resetForm();" action="submit.php" method="post">
<div id="formHeaders">
<p><u>Activity</u></p>
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
<div class="mandatory label">Email</div>
<input type="text" name="activity[email]" maxlength=100 /><br>

<span class="mandatory"></span><span style="color: red;"> Mandatory</span>
<hr>
<input type="submit" id="submitButton" value="Submit"><span id="submitAlert"></span><br><br>
</div>
<div id="main">
<div id="left">
<div class="clearLeft">
  <u>Students</u><span id="submitAlertStudents"></span><br>
  <span id="studentsSelected">0</span> selected
</div>
<div class="clearLeft">
  <div class="option">Show --</div>
  <div class="option">
    Form:
    <select id="studentFormFilter" onchange="studentFilter();">
      <option value="">All</option>
      <?php foreach ($cdgfssDB->listCurrentForm(PDO::FETCH_NUM) as $outputRow): 
        // output is single column result, so fetching indexed array and doing $row[0] for quick access ?>
        <option value="<?=$outputRow[0]?>">S<?=$outputRow[0]?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="option">
    Class:
    <select id="studentClassFilter" onchange="studentFilter();">
      <option value="">All</option>
      <?php foreach ($cdgfssDB->listCurrentClass(PDO::FETCH_NUM) as $outputRow): 
        // output is single column result, so fetching indexed array and doing $row[0] for quick access ?>
        <option value="<?=$outputRow[0]?>"><?=$outputRow[0]?></option>
      <?php endforeach; ?>
    </select>
  </div>
</div>
<div class="clearLeft">
  <div class="option">Search --</div>
  <div class="option"><input type="text" name="studentSearchInput" id="studentSearchInput" oninput="studentSearch();" onchange="studentSearch();" onpropertychange="studentSearch();" /> <input type="button" value="Enter" onclick="enterKeyPressed();" /></div>
</div>
<div class="clearLeft">You can search by Form, Class, or Name.</div>
<div class="clearLeft" id="advblock">
  <a href="javascript:toggleAdvOptions();">Advanced Options</a>
  <div class="hidden" id="advoptions">
      <p>Wildcards are supported (e.g.: 1?20 to search all Form 1 students with number 20)</p>
      <p>Pressing [Enter] will check the first name by default</p>
      <div id="advoptioncheckboxes">
        <input type="checkbox" id="clearAfterEnter" /><label for="clearAfterEnter">Clear Search after [Enter]</label>
        <input type="checkbox" id="clearClassNumberAfterEnter" /><label for="clearClassNumberAfterEnter">Clear Class Number after [Enter]</label>
        <input type="checkbox" id="doNotUncheck" checked=true/><label for="doNotUncheck">Do not uncheck with [Enter]</label>
        <input type="checkbox" id="checkAll" /><label for="checkAll">Check all found students with [Enter]</label>
        <input type="checkbox" id="searchPreviousYear" onclick="searchPreviousYearToggle()"/><label for="searchPreviousYear">Search including previous year's Form and Class Number</label>
      </div>
  </div>
  <div id="showAllCheckedButton"><input type="button" id="showAllCheckedStudentButton" value="Show All Checked Students" onclick="showAllCheckedStudents()" /></div>
</div>
<script type="text/javascript">
  function toggleAdvOptions() {
    document.getElementById("advoptions").classList.toggle("hidden");
  }
</script>
<script type="text/javascript">
  $('input#studentSearchInput').keydown(function(e) {
    if(e.keyCode == 13) { // enter key was pressed
      enterKeyPressed();
      return false; // prevent execution of rest of the script + event propagation / event bubbling + prevent default behaviour
    }
  });
  
  function enterKeyPressed() {
    studentSearchEnter();
    studentTotal();
    $("#studentSearchInput").focus();
  }
  
  function removeStudentListClass(inputClass) {
    var studentArray = document.querySelectorAll("[data-student_form_class]");
    for (removeStudentListClass.i = 0; removeStudentListClass.i < studentArray.length; removeStudentListClass.i++) {
      studentArray[removeStudentListClass.i].classList.remove(inputClass);
      studentArray[removeStudentListClass.i].nextElementSibling.classList.remove(inputClass);
    }
  }
  
  function addStudentListClass(inputClass) {
    var studentArray = document.querySelectorAll("[data-student_form_class]");
    for (addStudentListClass.i = 0; addStudentListClass.i < studentArray.length; addStudentListClass.i++) {
      studentArray[addStudentListClass.i].classList.add(inputClass);
      studentArray[addStudentListClass.i].nextElementSibling.classList.add(inputClass);
    }
  }
  
  function resetSearch() {
    document.getElementById("studentSearchInput").value = "";
    removeStudentListClass("hidden");
    removeStudentListClass("hiddenStudent");
    removeStudentListClass("hiddenStudentUnchecked");
    removeStudentListClass("hiddenStudentFilter");
    removeStudentListClass("hiddenStudentFilterUnchecked");
    document.getElementById("studentFormFilter").value = "";
    document.getElementById("studentClassFilter").value = "";
    document.getElementById("studentFormFilter").disabled = false;
    document.getElementById("studentClassFilter").disabled = false;
  }
  
  function showAllCheckedStudents() {
    resetSearch();
    addStudentListClass("hiddenStudentUnchecked");
    var studentArray = document.querySelectorAll("[data-student_form_class]:checked");
    for (showAllCheckedStudents.i = 0; showAllCheckedStudents.i < studentArray.length; showAllCheckedStudents.i++) {
      studentArray[showAllCheckedStudents.i].classList.remove("hiddenStudentUnchecked");
      studentArray[showAllCheckedStudents.i].nextElementSibling.classList.remove("hiddenStudentUnchecked");
    }
    showAllButton = document.getElementById("showAllCheckedStudentButton");
    showAllButton.setAttribute("onclick", "notShowAllCheckedStudents()");
    showAllButton.value = "Restore";
    document.getElementById("studentFormFilter").disabled = true;
    document.getElementById("studentClassFilter").disabled = true;
  }
  
  function notShowAllCheckedStudents() {
    resetShowAllCheckedStudents();
    showAllButton = document.getElementById("showAllCheckedStudentButton");
    showAllButton.setAttribute("onclick", "showAllCheckedStudents()");
    showAllButton.value = "Show All Checked Students";
  }
  
  function resetShowAllCheckedStudents() {
    resetSearch();
  }
  
  function studentSearch() {
    // resetShowAllCheckedStudents();
    var obj = document.getElementById("studentSearchInput");
    var studentArray = document.querySelectorAll("[data-student_form_class]:not(.hiddenStudent)");
    try {
      var wildCardReplacedValue = obj.value.replace(/\?/, ".");
      var wildCardReplacedValue = wildCardReplacedValue.replace(/\*/, ".*");
      var regEx = new RegExp(wildCardReplacedValue, "i");  // do not enable the "g" flag for test().  This produces a documented, but unwanted, behavior
    } catch(e) {
      // the RegEx can throw exceptions if the search box has bad regex characters inputted, so we're just going to end the function instead of erroring out.
      // extremely unlikely the users will bother learning RegEx to use it properly... so just going to leave it as it is.
      return;
    }
    // console.log(regEx);
    
    for (this.i = 0; this.i < studentArray.length; this.i++) {
      // using a basic RegEx to get rid of the <span> in the labels.
      studentArrayLabel = studentArray[i].nextElementSibling.innerHTML.replace(/<\/?span.*?>/ig, "");
      // console.log(studentArrayLabel + "|" + regEx.test(studentArrayLabel));
      if (!regEx.test(studentArrayLabel)) {
        studentArray[i].classList.add("hiddenStudentFilter");
        studentArray[i].nextElementSibling.classList.add("hiddenStudentFilter");
      } else {
        studentArray[i].classList.remove("hiddenStudentFilter");
        studentArray[i].nextElementSibling.classList.remove("hiddenStudentFilter");
      }
    }
  }

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
    studentSearch();
  }
</script>
<span id="studentList">
<?php
/* custom function for HTML output */
function e($arg_1) {
  echo(htmlentities($arg_1));
}

function f($arg_1) {
  return htmlentities($arg_1);
}

$previousYearFormClassNumber = $cdgfssDB->listPreviousYearStudentFormClassNumber()->fetchAll(PDO::FETCH_ASSOC);

foreach ($cdgfssDB->listCurrentStudent() as $key => $row) {
  // See [Optimization: studentArray]
  // moved class='studentCheck' and onclick='studentTotal();' to the succeeding javascript section to reduce network footprint
  $prevFormClass = array_key_exists($key, $previousYearFormClassNumber) ? $previousYearFormClassNumber[$key]['formclassnumber'] : "";
  echo(sprintf("<div class='clearLeft'><input type='checkbox' name='checkboxArray[]' id='%s' value='%s,%s' data-student_form_class='%s%s' data-student_gender='%s' data-previous_form_classnumber='%s'/>",
    f($key),
    f($row['student_index']),
    f($row['enrollment_year']),
    f($row['form']),
    f($row['class']),
    f($row['gender']),
    f($prevFormClass)));
  
  echo(sprintf("<label for='%s'>S%s%s%s <span class='prevYear'></span>%s %s %s</label>",
    f($key),
    f($row['form']),
    f($row['class']),
    f($row['class_number']),
    // f($prevFormClass),
    f($row['name_chinese']),
    f($row['name_english']),
    (strtoupper($row['gender']) == "M"?"<span class='gender male'>♂</span>":(strtoupper($row['gender']) == "F"?"<span class='gender female'>♀</span>":"??"))
    )
  );
  echo("</div>");
}
$pdo = null;
?>
<script type="text/javascript">
  /* [Optimization: studentArray] */
  var studentArray = document.querySelectorAll('input[data-student_form_class]')
  for (i = 0; i < studentArray.length; i++) {
    studentArray[i].setAttribute("onclick", "studentTotal();");
    studentArray[i].setAttribute("class", "studentCheck");
  }
</script>
</span> <!-- ending tag for <span id="studentList">-->
</div> <!-- ending tag for <div id="left"> -->
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
  <input type="reset" value="** RESET FORM **" />
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
