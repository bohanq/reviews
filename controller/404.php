<?php 
	// подключаем стили
	$data['style'] = '<link rel="stylesheet" type="text/css" href="/themes/css/404.css">';
	// адресс запрашиваемой страницы 
	$data['routes'] = SERVER_NAME.$_SERVER['REQUEST_URI'];
	// записываем содержимое шаблона в переменную 
	$content .= file_get_contents(ROOT_DIR.'/themes/header.tpl');
	$content .= file_get_contents(ROOT_DIR.'/themes/404.tpl');
	$content .= file_get_contents(ROOT_DIR.'/themes/footer.tpl');
	// устанавливаем ответ сервера "404 Not Found"
	header("HTTP/1.0 404 Not Found");
	// выводим содержимое
	echo template($data, $content);
	exit;
?>