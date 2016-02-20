<?php
	class review {
		// поле с информацией о подключенин к БД
		private $mysqli;

		// подключаемся к БД при создании обекта данного класса 
		function __construct(){
			$this->mysqli = new mysqli(DB_SERVER, DB_LOGIN, DB_PASSWORD, DB_NAME);
			$this->mysqli->set_charset('utf8');
		}

		// получение отзывов 
		public function get($namber_page, $number_reviews_in_page){
			return $this->mysqli->query('SELECT * FROM `review` ORDER BY `date` DESC LIMIT '.$namber_page.', '.$number_reviews_in_page);
		}
		// получаем количество отзывов 
		public function get_count(){
			$count_array = $this->mysqli->query('SELECT COUNT(*) FROM `review`')->fetch_assoc();
			return (int)$count_array['COUNT(*)'];
		}
		// получаем  данные про картинку, по идентификатору отзыва
		public function get_images($id_review){
			return $this->mysqli->query("SELECT * FROM `review_image` WHERE `id_review` = '".$id_review."'");
		}
		// записываем новый отзыв 
		public function set($data_post){
			$query = "
            	INSERT INTO  `".DB_NAME."`.`review` 
            	(`id` ,`name` ,`author` ,`message_review` ,`date`, `email`)
            	VALUES (NULL,'".$data_post['name_review']."', '".$data_post['name_user']."', '".$data_post['message_review']."', NOW(), '".$data_post['email_user']."');";
			$this->mysqli->query($query) or die(mysqli_error());
			return $this->mysqli->insert_id;
		}
		// записываем новое изображение
		public function set_image($id_review, $name_image){
			$query = "
            	INSERT INTO  `".DB_NAME."`.`review_image` 
            	(`id`, `id_review` ,`image_name`)
            	VALUES (NULL,'".$id_review."', '".$name_image."');";
			$this->mysqli->query($query) or die(mysqli_error());
		}
	}
?>