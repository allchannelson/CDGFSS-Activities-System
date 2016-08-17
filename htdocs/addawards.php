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
<input type="button" value="Add" onclick="addColumn()"/>
<script type="text/javascript">
  function addColumn() {
    $("#studentTable tr:first").append("<td>"+ $("#awardName").val() +"</td>");
    $("#studentTable tr:gt(0)").append(function (){return("<td>" + $(this).children("td:first").html() + "</td>")});
    // $("#studentTable tr:first").children("td:first")
  }
  
</script>
<hr>
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
</body>
</html>