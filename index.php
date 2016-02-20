<?php
	// возвращаемы сервером тип кодировки 
	header('Content-Type: text/html; charset=utf-8');
	// масив с параметрами url
	$routes = explode('/', $_SERVER['REQUEST_URI']);

	// масив для хранение дынных полученых в процесе роботы приложения
	$data = array();
	
	// подключение дополнительные файлы
	// файл с основными функциями
	require_once('core/functions.php');
	// файл с установленными константами
	require_once('config/global_vars.conf.php');

	// подтягиваем файл контроллера равен значению первого параметра url
	if(empty($routes[1])){
		//устанавливаем контроллер домашней страницы 
		include ('controller/review.php');
	}else{
		// проверяем контроллер на наличие. В случаи если контроллера с указаным именем нет устанавливаем контроллер 404 ошибки 
		if(file_exists($filename = 'controller/'.$routes[1].'.php')){
			include ($filename);
		}else{
			include ('controller/404.php');
		}
	}
?>