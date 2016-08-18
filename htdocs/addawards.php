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

Activity ID:  <?=$_REQUEST["activity_id"];?><br>

<?php
$pdoObj = new cdgfss_pdo();
$activityStudentHeading = $pdoObj->columns_Activity_AllStudents();
$activityStudents = $pdoObj->listActivity_AllStudents($activity_id);
?>
<label for="awardName">Name of Award: </label><input id="awardName" type="text" />
<input type="button" value="Add" onclick="addColumn()"/> <span id="errorMsg"></span><br>
<input type="reset" onclick="resetForm()" />
<script type="text/javascript">
  function addColumn() {
    var myAwardName = $("#awardName").val();
    if (myAwardName != "") {
      $("#errorMsg").css('display', 'none');
      $("#studentTable tr:first").append("<td>"+ myAwardName +"</td>");
      $("#studentTable tr:gt(0)").append(function (){return("<td>" + $(this).children("td:first").html() + "</td>")});
      // for debugging in the console to access the TR DOM jQuery object
      // $("#studentTable tr:first").children("td:first")
      $("#awardName").val('');
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
<form name="form" onsubmit="return validateForm()" action="addawards_submit.php" method="post">
<script type="test/javascript">
  function validateForm() {
  }
  </script>
<table id="studentTable">
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
</form>
</body>
</html>