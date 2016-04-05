<?php require_once('Connections/connection.php'); ?>
<?php require_once('Connections/function.php'); ?>
<html>
<meta charset="UTF-8">
<head>
<link rel="stylesheet" href="/as/result.css">
</head>
<body>
<?php
	$db_selected = mysql_select_db('activity', $connection);
	$query = sprintf("select * from student order by class, class_num asc");
	$result = mysql_query($query);
	if (!$result) {
		echo 'failed: ', mysql_error();
		exit;
	}
	echo "<table>";
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		echo "<tr>";
		foreach ($row as $output)
		{
			echo "<td>";			
			echo $output;
			echo "</td>"; 
		}
		echo "</tr>\n";
	}
	echo "</table>";
?>
</body>
</html>


