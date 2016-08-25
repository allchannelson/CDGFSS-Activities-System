<html>
<head>
  <script   src="https://code.jquery.com/jquery-3.0.0.min.js"   integrity="sha256-JmvOoLtYsmqlsWxa7mDSLMwa6dZ9rrIdtrrVYRnDRH0="   crossorigin="anonymous"></script>
  <style type="text/css">
    table, td, th {
      border-collapse: collapse;
      border: thin solid black;
    }
    
    td {
      padding: .2em;
    }
    
    td:nth-of-type(1) {
      /* This is to hide first column, which is an Activity_Student concatenated primary key */
      display: none;
    }
    
    tr:nth-of-type(2n) {
      background-color: #DDD;
    }
    
    #errorMsg {
      color: red;
      display: none;
    }
    
    .black {
      color: black;
    }
    
    .awardTitle {
    }
    
    .awardCheckbox, .awardRadio {
      text-align: center;
    }
    
    #hiddenData {
      display: none;
    }
    
  </style>
</head>
<body>
<?php 
  if (isset($_REQUEST['activity_id'])) {
    $activity_id = (int)($_REQUEST['activity_id']);  // (int) cast prevents invalid and non-numeric characters, probably will prevent SQL injection
  } else {
    exit("Missing or Invalid Activity ID");
  }

  require_once 'models/pdo.php';
  require_once 'models/pdo.cdgfss.php';
  $cdgfssDB = new cdgfss_pdo();
?>

Activity ID:  <?=$activity_id?><br>

<?php
$pdoObj = new cdgfss_pdo();
$activityStudentHeading = $pdoObj->columns_ActivityAndAwards_AllStudents();
$activityStudents = $pdoObj->listActivityAndAwards_AllStudents($activity_id);
?>
<label for="awardName">Name of Award: </label><input id="awardName" type="text" maxlength=100 />
<input type="button" value="Add" onclick="addColumn()"/> <span id="errorMsg"></span><br>
<input type="reset" onclick="resetForm()" />
<script type="text/javascript">
  function addColumn() {
    if (typeof window.gAwardCount == 'undefined') {
      window.gAwardCount = 0;
    } else {
      window.gAwardCount += 1;
    }
    $('#submit').prop('disabled', false);
    var myAwardName = $("#awardName").val();
    if (myAwardName != "") {
      $("#errorMsg").css('display', 'none');
      $("#studentTable tr:first").append("<td class='awardTitle'>" + myAwardName + "</td>");
      $("#studentTable tr:gt(0)").append(function (){return("<td class='awardRadio'><input type='radio' name='awards[" + $(this).children("td:first").html() +"]' value='" + myAwardName + "' /></td>")});
      $("#hiddenData").append("<input type='hidden' name='awardsName[" + window.gAwardCount +"]' value='" + myAwardName + "' />");
      // for debugging in the console to access the TR DOM jQuery object
      // $("#studentTable tr:first").children("td:first")
      
      // This method of populating data is exploitable, since the HTML table can be modified on the browser.
      // Prepared PDO statements prevent SQL injections, so bad data can go in if someone decides to mess with it.
      
      $("#awardName").val('');  // clears the input field since we probably don't want multiples of the same award
    } else {
      errorSpan = $("#errorMsg");
      errorSpan.css('display', 'initial');
      errorSpan.html('[<span class="black">Name of Award</span>] cannot be blank.');
    }
  }
  function resetForm() {
    // probably just best to reload the page to clean everything up.  I can't quickly and safely reverse the HTML table appends.
    location.reload();
  }
  
</script>
<hr>
<form name="form" action="addawards_submit.php" method="post">
<p><input type="submit" id="submit" disabled /></p>
<script type="test/javascript">
  function validateForm() {
    //
  }
  </script>
<table id="studentTable">
<!-- Table is generated by foreach, with the first column hidden via CSS because it is a DB concatenated primary key
-->
  <tr>
    <?php foreach ($activityStudentHeading as $field): ?>
    <td><?= $field ?></td>
    <?php endforeach ?>
  </tr>
  <?php foreach ($activityStudents as $row): ?>
  <tr>
    <?php foreach ($row as $field): ?>
    <td><?= $field ?></td>
    <?php endforeach ?>
  </tr>
  <?php endforeach ?>
</table>
<div id="hiddenData">
</div>
</form>
</body>
</html>