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
		<title>Монитор температуры график</title>
		<meta http-equiv="refresh" content="1" />
		<link href="/dashboard/images/favicon.png" rel="icon" type="image/png" />
	</head>
	<?php
		$w = 420;
		$h = 300;
		$voff = 40;
		$hoff = 60;
		$mov = $hoff;
		$dpi = 5;
		if(!$conn->connect_errno){
			$getmax = $conn->query("SELECT MAX(t.Dev) AS MaxDev FROM ( SELECT monitor.DeviceA as Dev FROM monitor INNER JOIN (SELECT * FROM monitor WHERE DeviceA > 0 ORDER BY ID DESC LIMIT 10) as t ON monitor.DeviceA = t.DeviceA UNION ALL SELECT monitor.DeviceB as Dev FROM monitor INNER JOIN (SELECT * FROM monitor WHERE DeviceB > 0 ORDER BY ID DESC LIMIT 10) as t ON monitor.DeviceB = t.DeviceB UNION ALL SELECT monitor.DeviceC as Dev FROM monitor INNER JOIN (SELECT * FROM monitor WHERE DeviceC > 0 ORDER BY ID DESC LIMIT 10) as t ON monitor.DeviceC = t.DeviceC ) AS t");
			$row = $getmax->fetch_assoc();
			$maxD = $row['MaxDev'];
			
			$getmin = $conn->query("SELECT MIN(t.Dev) AS MinDev FROM ( SELECT monitor.DeviceA as Dev FROM monitor INNER JOIN (SELECT * FROM monitor WHERE DeviceA > 0 ORDER BY ID DESC LIMIT 10) as t ON monitor.DeviceA = t.DeviceA UNION ALL SELECT monitor.DeviceB as Dev FROM monitor INNER JOIN (SELECT * FROM monitor WHERE DeviceB > 0 ORDER BY ID DESC LIMIT 10) as t ON monitor.DeviceB = t.DeviceB UNION ALL SELECT monitor.DeviceC as Dev FROM monitor INNER JOIN (SELECT * FROM monitor WHERE DeviceC > 0 ORDER BY ID DESC LIMIT 10) as t ON monitor.DeviceC = t.DeviceC ) AS t");
			$row = $getmin->fetch_assoc();
			$minD = $row['MinDev'];
			
			if ($maxD-$minD != 0){
				$dpi = ($h-$voff)/($maxD-$minD);
			}
		}
	?>
	<body background="bkg3.png">
		<script>
		document.write('<canvas id="myCanvas" width="'+(<?php echo $w ?>+<?php echo $hoff ?>)+'" height="'+(<?php echo $h ?>+<?php echo $voff ?>)+'" style="border:1px solid #000000;"></canvas>');
		var c = document.getElementById("myCanvas");
		var ctx = c.getContext("2d");	
		ctx.font = "14px Arial";
		<?php
			echo 'ctx.strokeRect('.($hoff).','.$voff.','.($w-$hoff).','.($h-$voff).');';
			if(!$conn->connect_errno){
				$result=$conn->query("SELECT * FROM `monitor` ORDER BY ID DESC LIMIT 10");
				if ($result->num_rows > 0) {
					$tempA;
					echo 'ctx.beginPath();';	
					echo 'ctx.fillText("'.$maxD.'", 10, '.($voff).');';	
					echo 'ctx.fillText("'.(($maxD+$minD)/2).'", 10, '.(($h+$voff)/2).');';
					echo 'ctx.fillText("'.$minD.'", 10, '.$h.');';
					echo 'ctx.fillText("T/t", 10, '.($h+$voff-20).');';
					echo 'ctx.fillStyle = "red";';
					echo 'ctx.fillText("A", 100, '.($voff-20).');';
					echo 'ctx.fillStyle = "green";';
					echo 'ctx.fillText("B", 120, '.($voff-20).');';
					echo 'ctx.fillStyle = "blue";';
					echo 'ctx.fillText("C", 140, '.($voff-20).');';
					echo 'ctx.fillStyle = "black";';
					
					echo 'ctx.setLineDash([2, 5]);';
					echo 'ctx.strokeRect('.($hoff).','.(($h+$voff)/2).','.($w-$hoff).',0);';
					echo 'ctx.setLineDash([]);';
					
					while($rowmain = $result->fetch_assoc()) {
							echo 'ctx.fillText("'.$rowmain["ID"].'", '.$mov.', '.($h+($voff/2)).');';	
							echo 'ctx.strokeStyle = "red";';
						if ($rowmain["DeviceA"] != 0){
							echo 'ctx.lineTo('.$mov.', '.($h-(($rowmain["DeviceA"]-$minD)*$dpi)).');';
							echo 'ctx.stroke();';
						}
						$mov += 40;
					}
					$mov = $hoff;
					$result=$conn->query("SELECT * FROM `monitor` ORDER BY ID DESC LIMIT 10");
					echo 'ctx.beginPath();';
					while($rowmain = $result->fetch_assoc()) {
						echo 'ctx.strokeStyle = "green";';
						if ($rowmain["DeviceB"] != 0){
							echo 'ctx.lineTo('.$mov.', '.($h-(($rowmain["DeviceB"]-$minD)*$dpi)).');';
							echo 'ctx.stroke();';
						}
						$mov += 40;
					}
					$mov = $hoff;
					$result=$conn->query("SELECT * FROM `monitor` ORDER BY ID DESC LIMIT 10");
					echo 'ctx.beginPath();';
					while($rowmain = $result->fetch_assoc()) {
						echo 'ctx.strokeStyle = "blue";';
						if ($rowmain["DeviceC"] != 0){
							echo 'ctx.lineTo('.$mov.', '.($h-(($rowmain["DeviceC"]-$minD)*$dpi)).');';
							echo 'ctx.stroke();';
						}
						$mov += 40;
					}
				}
			} else {
				echo 'ctx.fillText("No Table", ('.($h/2).'));';
			}
		?>
		</script>
		<br/> = Состояние (относительно последних 5 значений) =
	<?php
		if(!$conn->connect_errno){	
			$getmaxDevA = $conn->query("SELECT MAX(t.Dev) AS MaxDevA FROM ( SELECT monitor.DeviceA as Dev FROM monitor INNER JOIN (SELECT * FROM monitor WHERE DeviceA > 0 ORDER BY ID DESC LIMIT 5) as t ON monitor.DeviceA = t.DeviceA) AS t");
			$row = $getmaxDevA->fetch_assoc();
			$maxDevA = $row['MaxDevA'];
			$getminDevA = $conn->query("SELECT MIN(t.Dev) AS MinDevA FROM ( SELECT monitor.DeviceA as Dev FROM monitor INNER JOIN (SELECT * FROM monitor WHERE DeviceA > 0 ORDER BY ID DESC LIMIT 5) as t ON monitor.DeviceA = t.DeviceA) AS t");
			$row = $getminDevA->fetch_assoc();
			$minDevA = $row['MinDevA'];
			if ($maxDevA > 0) {
				if ($maxDevA > 35) {
					echo "<br/> Датчик A: ".$maxDevA." (горячий!!)";
				} else if ($minDevA < 15) {
					echo "<br/> Датчик A: ".$minDevA." (холодный!!)";
				} else {
					echo "<br/> Датчик А: ".$minDevA." (в норме)";
				}
			} else {
				echo "<br/> Нет данных о датчике А.";
			}
		
			$getmaxDevB = $conn->query("SELECT MAX(t.Dev) AS MaxDevB FROM ( SELECT monitor.DeviceB as Dev FROM monitor INNER JOIN (SELECT * FROM monitor WHERE DeviceB > 0 ORDER BY ID DESC LIMIT 5) as t ON monitor.DeviceB = t.DeviceB) AS t");
			$row = $getmaxDevB->fetch_assoc();
			$maxDevB = $row['MaxDevB'];
			$getminDevB = $conn->query("SELECT MIN(t.Dev) AS MinDevB FROM ( SELECT monitor.DeviceB as Dev FROM monitor INNER JOIN (SELECT * FROM monitor WHERE DeviceB > 0 ORDER BY ID DESC LIMIT 5) as t ON monitor.DeviceB = t.DeviceB) AS t");
			$row = $getminDevB->fetch_assoc();
			$minDevB = $row['MinDevB'];
			if($maxDevB >0){
				if ($maxDevB > 35) {
					echo "; Датчик B: ".$maxDevB." (горячий!!)";
				} else if ($minDevB < 15) {
					echo "; Датчик B: ".$minDevB." (холодный!!)";
				} else {
					echo "; Датчик В: ".$minDevB." (в норме)";
				}
			} else {
				echo "; Нет данных о датчике В.";
			}
			
			$getmaxDevC = $conn->query("SELECT MAX(t.Dev) AS MaxDevC FROM ( SELECT monitor.DeviceC as Dev FROM monitor INNER JOIN (SELECT * FROM monitor WHERE DeviceC > 0 ORDER BY ID DESC LIMIT 5) as t ON monitor.DeviceC = t.DeviceC) AS t");
			$row = $getmaxDevC->fetch_assoc();
			$maxDevC = $row['MaxDevC'];
			$getminDevC = $conn->query("SELECT MIN(t.Dev) AS MinDevC FROM ( SELECT monitor.DeviceC as Dev FROM monitor INNER JOIN (SELECT * FROM monitor WHERE DeviceC > 0 ORDER BY ID DESC LIMIT 5) as t ON monitor.DeviceC = t.DeviceC) AS t");
			$row = $getminDevC->fetch_assoc();
			$minDevC = $row['MinDevC'];		
			if($maxDevC > 0){	
				if ($maxDevC > 35) {
					echo "; Датчик C: ".$maxDevC." (горячий!!)";
				} else if ($minDevC < 15) {
					echo "; Датчик C: ".$minDevC." (холодный!!)";
				} else {
					echo "; Датчик С: ".$minDevC." (в норме)";
				}
			} else {
				echo "; Нет данных о датчике С.";
			}
		}
		
		if(isset($_POST['normA']) || ($minDevA > 15 && $maxDevA < 35)){
			$conn->query("UPDATE `action` SET `DeviceA` = '0' WHERE `ID` = 1");
		}
		if(isset($_POST['heatA']) || ($minDevA < 10 && $minDevA != 0)){
			$conn->query("UPDATE `action` SET `DeviceA` = '1' WHERE `ID` = 1");
		}
		if(isset($_POST['coldA']) || $maxDevA > 40){
			$conn->query("UPDATE `action` SET `DeviceA` = '2' WHERE `ID` = 1");
		}
		if(isset($_POST['normB']) || ($minDevB > 15 && $maxDevB < 35)){
			$conn->query("UPDATE `action` SET `DeviceB` = '0' WHERE `ID` = 1");
		}
		if(isset($_POST['heatB']) || ($minDevB < 10 && $minDevB != 0)){
			$conn->query("UPDATE `action` SET `DeviceB` = '1' WHERE `ID` = 1");
		}
		if(isset($_POST['coldB']) || $maxDevB > 40){
			$conn->query("UPDATE `action` SET `DeviceB` = '2' WHERE `ID` = 1");
		}
		if(isset($_POST['normC']) || ($minDevC > 15 && $maxDevC < 35)){
			$conn->query("UPDATE `action` SET `DeviceC` = '0' WHERE `ID` = 1");
		}
		if(isset($_POST['heatC']) || ($minDevC < 10 && $minDevC != 0)){
			$conn->query("UPDATE `action` SET `DeviceC` = '1' WHERE `ID` = 1");
		}
		if(isset($_POST['coldC']) || $maxDevC > 40){
			$conn->query("UPDATE `action` SET `DeviceC` = '2' WHERE `ID` = 1");
		}
	?>
	<br/>= Контроль =<br/>
	<form action='' method='POST'>
		Датчик А: <input type='submit' name='normA' value="Нормальный режим"/><input type='submit' name='heatA' value="Режим нагрева"/><input type='submit' name='coldA' value="Режим охлаждения"/><br/>
		Датчик B: <input type='submit' name='normB' value="Нормальный режим"/><input type='submit' name='heatB' value="Режим нагрева"/><input type='submit' name='coldB' value="Режим охлаждения"/><br/>
		Датчик C: <input type='submit' name='normC' value="Нормальный режим"/><input type='submit' name='heatC' value="Режим нагрева"/><input type='submit' name='coldC' value="Режим охлаждения"/>
	</form>
	</body>
</html>
<?php $conn->close(); ?>