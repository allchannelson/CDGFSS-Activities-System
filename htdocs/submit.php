<html>
<body>
<?php
if (isset($_POST['checkboxArray'])) {
  echo("Submitted!<br>");
  foreach($_POST['checkboxArray'] as $thisCheckbox) {
      echo($thisCheckbox . "<br>");
  }
} else {
  echo("No Data!");
}
?>
</body>
</html>