<?php

	// функция "шаблонизатор" ищет в шаблоне определенного блока ключевую запись "{$name_index_arrey}" и 
	// меняет ее на значение записаное под данным индексом 
	function template($data, $content){
		preg_match_all('|.*?{\$(.*?)}.*?|si', $content, $matches);
		foreach($matches[1] as $value){
			// в случай если ипользуеться многомерный масив
			if(preg_match('/(.*):(.*)/', $value, $matc)){
				$search = '{$'.$value.'}';
				$content = str_replace($search, $data[$matc[1]][$matc[2]], $content);
			}else{
				$search = '{$'.$value.'}';
				$content = str_replace($search, $data[$value], $content);
			}
		}
		return $content;
	}

	// функция создании нового имени для загруженного файла 
	function new_name_image($mime){

		$letters = 'qwertyuiopasdfghjklzxcvbnm';
		$random_letters = $letters{rand(0,25)}.$letters{rand(0,25)};
		return time().$random_letters.'.'.$mime;
	}

	//Кросформенная валидация полей при добавленни отзыва 
	function validate_input($name_input, $regular, $max_length_input){
		global $data;

		// проверка на пустое поле
		if(empty($_POST[$name_input])){
			$data['error'][$name_input] = 'Заполните поле';
			$data['add_class'][$name_input] = 'error';
		}else{
			// проверка длины поля
			if(strlen($_POST[$name_input]) >= 3 && strlen($_POST[$name_input]) < $max_length_input){
				// проверка на спец символы
				if(!preg_match($regular, $_POST[$name_input])){
					$data['add_class'][$name_input] = 'success';
				}else{
					$data['error'][$name_input] = 'Недопустимый символ в поле';
					$data['add_class'][$name_input] = 'error';
				}
			}else{
				$data['error'][$name_input] = 'Недопустимое количество символов (от 3 до '.$max_length_input.' знаков)';
				$data['add_class'][$name_input] = 'error';
			}
		}
	}
?>