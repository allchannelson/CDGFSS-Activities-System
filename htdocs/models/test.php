<?php
require 'pdo.php';
require 'pdo.cdgfss.php';

$test = new cdgfss_pdo();
$testResults =  $test->reportActivity_AllActivities_AllStudents();
?>
<table>
<?php foreach ($testResults as $row): ?>
  <tr>
    <?php foreach ($row as $field): ?>
      <td><?= $field ?></td>
    <?php endforeach ?>
  </tr>
<?php endforeach ?>
</table>