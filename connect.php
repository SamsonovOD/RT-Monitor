<?php			
	header("Content-Type: text/html; charset=UTF-8");	
	#error_reporting(E_ALL & ~E_WARNING);
	$conn = new mysqli("127.0.0.1", "", "", "temperature");
	if($conn->connect_errno){
		#echo "Ошибка, подключение не удалось: ";
		#echo mb_convert_encoding($conn->connect_error, "UTF-8", "WINDOWS-1251");
	} else {
		#echo "Ошибка, подключение не удалось: ";
		$conn->query("SET NAMES utf8");
	}
?>