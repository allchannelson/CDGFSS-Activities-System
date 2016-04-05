<?php
require_once('Connections/connection.php');
require_once('Connections/function.php');
	$db_selected = mysql_select_db('activity', $connection);
	$query = sprintf("select * from student order by class, class_num asc limit 2");
	$result = mysql_query($query);
	if (!$result) {
		echo "alert('failed: " . mysql_error() . "');";
		exit;
	}
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
    $rowString = "";
    $rowString .= "{";
		foreach ($row as $k => $v)
		{
			$rowString .= $k . ":\"" . $v . "\",";
      // $rowString .= $v;
		}
    $rowString .= "}";
    // echo "p({label:\"$rowString\"});";
    // echo "p($rowString);";
	}
?>


