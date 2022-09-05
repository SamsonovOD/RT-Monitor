<!doctype html>
<?php
	include('connect.php');
?>
<html>
	<head>
		<meta charset="utf-8">
		<meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<meta name="description" content="Temperature Monitor test project." />
		<title>Монитор температуры таблица</title>
		<meta http-equiv="refresh" content="1" />
		<link href="/dashboard/images/favicon.png" rel="icon" type="image/png" />
	</head>
	<body background="bkg2.png">	
		<table border="1">
			<tr>
				<th>время</th>
				<th><font color="red">Датчик A</font></th>
				<th><font color="green">Датчик B</font></th>
				<th><font color="blue">Датчик C</font></th>
			</tr>
		<?php
			if(!$conn->connect_errno){
				$result=$conn->query("SELECT * FROM `monitor` ORDER BY ID DESC");
				if ($result->num_rows > 0) {
					while($row = $result->fetch_assoc()) {
						echo "<tr>";
						echo "<td>".$row["ID"]."</td>";
						echo "<td>".$row["DeviceA"]."</td>";
						echo "<td>".$row["DeviceB"]."</td>";
						echo "<td>".$row["DeviceC"]."</td>";
						echo "</tr>";
					}
				} else {
					echo "Таблица пуста";
				}
			} else {
				echo "Нет соединения с БД.";
			}
		?>
		</table>
	</body>
</html>
<?php $conn->close(); ?>