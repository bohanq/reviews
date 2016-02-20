<?php

	// подключение дополнительные файлы
	// файл с установленными константами базы данных
	require_once(ROOT_DIR.'/config/db.conf.php');
	// модель обращение к базе данных
	require_once(ROOT_DIR.'/model/review.php');
	// класс генерации капчи
	require_once(ROOT_DIR.'/core/captcha.php');
	// класс роботы с картинками
	require_once(ROOT_DIR.'/core/image.php');

	// создание объекта модели
	$review = new review;
	// создание объекта работы с капчей 
	$captcha = new captcha;
	// создание объекта работы с изображениями 
	$img = new image;

	// допустимые mime типы картинко 
	$load_mime = array('image/jpeg','image/png','image/gif');
	// допустимые расширение файлов
	$load_extension = array('jpg','jpeg','gif','png');
	// набор спец символов 
	$reg_exp_for_validate = '|[\@\#\$\%\^\&\*\(\)\+\=\<\>\\\/\`\~\{\}\[\]\|]|';
	// максимальный размер загружаемого файла
	$data['apload_max_filesize'] = '2097152';
	// подключение скрипта
	$data['script'] = '<script src="/themes/js/review.js"></script>';
	// подключение стилев
	$data['style'] = '<link rel="stylesheet" type="text/css" href="/themes/css/review.css">';

	// количество отзывов на одной странице
	(int)$count_reviews_in_page = 10;
	// количество страниц
	(int)$number_of_pages = ceil($review->get_count()/$count_reviews_in_page);

	// получение номера страницы из параметра url
	if(count($routes) === 2 && empty($routes[2]) || count($routes) === 3 && (int)$routes[2] === 1){

		$number_page = 0;
		$this_page = 1;

	}elseif ((int)$routes[2] <= $number_of_pages && $routes[2] !== '0' && $routes[2] > 1 && count($routes) === 3) {

		$number_page = (((int)$routes[2]-1)*$count_reviews_in_page);
		$this_page = (int)$routes[2];

	}else{
		include ('controller/404.php');
	}


	// запускаем проверку валидации полей 
	if($_SERVER['REQUEST_METHOD'] == 'POST'){

		// валидация имени пользователя
		validate_input('name_user', $reg_exp_for_validate, 32);

		// валидация названия отзыва
		validate_input('name_review', $reg_exp_for_validate, 200);

		// валидация текста сообщения 
		validate_input('message_review', $reg_exp_for_validate, 2000);

		// валидация email пользователя
		if(!preg_match('|.*\@.*\..*|si', $_POST['email_user']) && !empty($_POST['email_user'])){
			$data['error']['email_user'] = 'Неверный формат email';
			$data['add_class']['email_user'] = 'error';
		}elseif(preg_match('|.*\@.*\..*|si', $_POST['email_user']) && !empty($_POST['email_user'])){
			if(strlen($_POST['email_user']) >= 6 && strlen($_POST['email_user']) < 32){
				if(!preg_match('|[\#\$\%\^\&\(\)\+\=\<\>\\\/\`\~\{\}\[\]\|]|', $_POST['email_user'])){
					$data['add_class']['email_user'] = 'success';
				}else{
					$data['error']['email_user'] = 'Недопустимый символ в поле';
					$data['add_class']['email_user'] = 'error';
				}
			}else{
				$data['error']['email_user'] = 'Недопустимое количество символов (от 6 до 32 знаков)';
				$data['add_class']['email_user'] = 'error';
			}
		}

		//провверка капчи
		if(empty($_POST['captcha-sum']) || empty($_POST['captcha-text'])){
			$data['error']['captcha'] = 'Введите проверочные комбинации';
			$data['add_class']['captcha'] = 'error';
		}else{
			if(is_numeric($_POST['captcha-sum']) && !is_numeric($_POST['captcha-text'])){
				if(strlen($_POST['captcha-sum']) <= 2 && strlen($_POST['captcha-text']) <= 4){
					if($_COOKIE['c_code'] !== md5($_POST['captcha-sum'].$_POST['captcha-text'])){
						$data['error']['captcha'] = 'Не верная каптча';
						$data['add_class']['captcha'] = 'error';
					}
				}elseif(strlen($_POST['captcha-sum']) >= 3){
					$data['error']['captcha'] = 'Первое поле не должно быть больше 2 знаков';
					$data['add_class']['captcha'] = 'error';
				}elseif(strlen($_POST['captcha-text']) >= 5){
					$data['error']['captcha'] = 'Второе поле не должно быть больше 4 знаков';
					$data['add_class']['captcha'] = 'error';
				}
			}elseif(!is_numeric($_POST['captcha-sum'])){
				$data['error']['captcha'] = 'Первое поле должно состоять из цыфр';
				$data['add_class']['captcha'] = 'error';
			}elseif(is_numeric($_POST['captcha-text'])){
					$data['error']['captcha'] = 'Второе поле должно состоять из букв';
					$data['add_class']['captcha'] = 'error';
			}
		}
		// проверка загружаемого файла 
		if ($_FILES["pictures"]["error"] == UPLOAD_ERR_OK) {
			$tmp_name = $_FILES["pictures"]["tmp_name"];
			$extension = mb_strtolower(end(explode('.', $_FILES["pictures"]["name"])));
			if(in_array($extension, $load_extension) && in_array($_FILES["pictures"]["type"], $load_mime)){
				$image_name = new_name_image($extension);
				if(move_uploaded_file($tmp_name, "image/origin/".$image_name)){
					if(empty($data['error'])){
						$id_review = $review->set($_POST);
						$data['add_class'] = '';
					}else{
						$data['post'] = $_POST;
					}
					$review->set_image($id_review, $image_name);
				}else{
					$data['error']['load_file'] = 'Не удалось загрузить изображение на сервер. Попробуйте еще раз';
					$data['post'] = $_POST;
				}
			}elseif(in_array($extension, $load_extension) || in_array($_FILES["pictures"]["type"], $load_mime)){
				$data['error']['load_file'] = 'Не коректный файл изображения';
				$data['post'] = $_POST;
			}else{
				$data['error']['load_file'] = 'К загрузке доступны только изображения';
				$data['post'] = $_POST;
			}
		}elseif($_FILES["pictures"]["error"] == UPLOAD_ERR_INI_SIZE || $_FILES['userfile']['size'] > $data['apload_max_filesize']){
			$data['error']['load_file'] = 'Принятый файл превысил максимально допустимый размер '.$data['apload_max_filesize'];
			$data['post'] = $_POST;
		}elseif(empty($data['error']) && $_FILES["pictures"]["error"] == UPLOAD_ERR_NO_FILE){
			$id_review = $review->set($_POST);
			$data['add_class'] = '';
		}else{
			$data['post'] = $_POST;
		}
	}

	// вывод картинки капчи
	$data['captcha_image'] = $captcha->image;
	// записываем кукы для хранения проверочного хеш значения
	setcookie('c_code', $captcha->hash, time()+3600,'/review');
	setcookie('c_code', $captcha->hash, time()+3600,'/');

	// собираем шаблон
	// загружаем хедер
	$content .= file_get_contents(ROOT_DIR.'/themes/header.tpl');
	// загружаем центральную часть страницы отзывов 
	$content .= file_get_contents(ROOT_DIR.'/themes/review.tpl');
	// запрос в БД для получения перечня отзывов
	$date_reviews = $review->get($number_page, $count_reviews_in_page);
	// загружаем блок отзыва
	$item_review_tpl = file_get_contents(ROOT_DIR.'/themes/item_review.tpl');
	while ($row = mysqli_fetch_assoc($date_reviews)) {
		if($date_images = $review->get_images($row['id'])){
			// загружаем блок вывода картинки
			$review_images_tpl = file_get_contents(ROOT_DIR.'/themes/review_images.tpl');
			while ($image = mysqli_fetch_assoc($date_images)){
				// устанавливаем данные про изображение 
				$row['header_image_box'] = 'Прикрепленные изображения';
				$img->isset_url($image['image_name']);
				$image['image_origin_url'] = $img->image_url_origin;
				$image['image_url_70_100'] = $img->image_url_70_100;
				$image['image_url_500_700'] = $img->image_url_500_700;
				// получаем блок с установленными  значениями переменных вместо их названия 
				$row['image'] .= template($image, $review_images_tpl);
			}
		}
		// вкл подсветку блока, если данный отзыв был добавлен только что добавлен пользователем
		if((int)$row['id'] === $id_review){
			$row['ok_add_review'] = 'green';
		}
		// получам блок с установленными значениями переменных
		$data['item_review'] .= template($row, $item_review_tpl);
	}
	// загружаем блок вывода номеров страниц 
	$href_number_page_tpl = file_get_contents(ROOT_DIR.'/themes/href_namber_page.tpl');
	for ($i=1; $i <= $number_of_pages; $i++) {
		$nav_info = array();
		$nav_info['page_url'] = 'http://'.SERVER_NAME.'/review/'.$i;
		$nav_info['page_name'] = $i;
		if($this_page === $i) $nav_info['page_active'] = 'active'; 
		$data['href_number_page'] .= template($nav_info, $href_number_page_tpl);
	}
	// загружаем блок с формой для добавление отзыва
	$content .= file_get_contents(ROOT_DIR.'/themes/form_add_review.tpl');
	// загружаем подвальную часть шаблона 
	$content .= file_get_contents(ROOT_DIR.'/themes/footer.tpl');
	// устанавливаем все значение с масива data в общий файл шаблона и выводим страницу 
	echo template($data, $content);
?>