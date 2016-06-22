<?php
require 'pdo.php';
require 'pdo.cdgfss.php';

$test = new cdgfss_pdo();
$testResults =  $test->listActivity_Details(15);
?>
<table border=1>
  <?php foreach ($testResults as $row): ?>
  <tr>
    <?php foreach ($row as $field): ?>
    <td><?= $field ?></td>
    <?php endforeach ?>
  </tr>
  <?php endforeach ?>
</table>
