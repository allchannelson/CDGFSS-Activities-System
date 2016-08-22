<html>
<body>
<pre>
<?php 
  // DEBUG
  var_dump($_REQUEST);
?>
</pre>
<?php
// http://stackoverflow.com/a/20480796/3715973
// INSERT INTO `awards` (`awards_name`) VALUES ('Test Award')
  // ON DUPLICATE KEY UPDATE awards_id=LAST_INSERT_ID(awards_id), `awards_name`='Test Award';
// SELECT LAST_INSERT_ID();

// NOTE:  autoincrement will increase when the DUPLICATE is triggered.  awards_id will not be sequential.  This is expected and documented.
// http://stackoverflow.com/questions/7087869/mysql-insert-on-duplicate-update-adds-one-to-the-autoincrement
// from report outputs, assuming somewhere around 20,000 IDs used per year (2015-16 had ~7800, very generous estimate)
// it will take about 100,000 years to exhaust all the IDs (INT(10) is used as primary key), assuming the IDs are not consumed by a loop bug

?>
</body>
</html>
