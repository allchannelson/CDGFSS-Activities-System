<?php
require 'pdo.php';
require 'pdo.cdgfss.php';

$test = new cdgfss_pdo();
$testResults =  $test->reportActivity_AllActivities_AllStudents();
?>
<table border=1>
  <tr>
    <?php foreach ($test->columns_AllActivities_AllStudents() as $columnTitle): ?>
      <td><?= $columnTitle ?></td>
    <?php endforeach?>
  </tr>
  <?php foreach ($testResults as $row): ?>
  <tr>
    <?php foreach ($row as $field): ?>
    <td><?= $field ?></td>
    <?php endforeach ?>
  </tr>
  <?php endforeach ?>
</table>