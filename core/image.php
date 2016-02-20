<?php
// клас для изменении разширения загруженых на сайт картинок 

	class image{
		// корневая директория изображений
		var $dir_img;
		// директория оригинальных изображений
		var $dir_image_origin;
		// директория изображений с разришением 70px*100px
		var $dir_image_70_100;
		// директория изображений с разришением 500px*700px
		var $dir_image_500_700;
		// адрес оригинального изображения 
		var $image_url_origin;
		// адрес изображения с разришением 70px*100px
		var $image_url_70_100;
		// адрес изображения с разришением 500px*700px
		var $image_url_500_700;

		public function __construct(){
			$this->dir_img = ROOT_DIR.'/image/';
			$this->dir_image_origin = $this->dir_img.'origin/';
			$this->dir_image_70_100 = $this->dir_img.'cache/70_100/';
			$this->dir_image_500_700 = $this->dir_img.'cache/500_700/';
		}

		// метод проверки картинки на наличие если нет возвращает стандартное изображение "noimage" и 
		// проверки на наличие уменшенних копий данного файла
		public function isset_url($name_image){
			if(file_exists($image_url_origin = $this->dir_image_origin.$name_image)){
				$this->image_url_origin = 'http://'.SERVER_NAME.'/image/origin/'.$name_image;
				if(file_exists($image_url_70_100 = $this->dir_image_70_100.$name_image)){
					$this->image_url_70_100 = 'http://'.SERVER_NAME.'/image/cache/70_100/'.$name_image;
				}else{
					if($this->create_thumb_image($name_image, 70, 100, $this->dir_image_70_100))
						$this->image_url_70_100 = 'http://'.SERVER_NAME.'/image/cache/70_100/'.$name_image;
				}

				if(file_exists($image_url_500_700 = $this->dir_image_500_700.$name_image)){
					$this->image_url_500_700 = 'http://'.SERVER_NAME.'/image/cache/500_700/'.$name_image;
				}else{
					if($this->create_thumb_image($name_image, 500, 700, $this->dir_image_500_700))
						$this->image_url_500_700 = 'http://'.SERVER_NAME.'/image/cache/500_700/'.$name_image;
				}
			}else{
				$this->image_url_origin = 'http://'.SERVER_NAME.'/image/noimage.png';
				$this->image_url_70_100 = 'http://'.SERVER_NAME.'/image/noimage70_100.png';
				$this->image_url_500_700 = 'http://'.SERVER_NAME.'/image/noimage500_700.png';
			}
		}

		// метод создания уменшеной копии картинки 
		public function create_thumb_image($name_image, $new_width, $new_height, $dir_save){
			$img_origin_info  = getimagesize($this->dir_image_origin.$name_image);

			$scale_x = $new_width / $img_origin_info[0];
			$scale_y = $new_height / $img_origin_info[1];
			$scale = min($scale_x, $scale_y);

			$new_scale_width = (int)($img_origin_info[0]*$scale);
			$new_scale_height = (int)($img_origin_info[1]*$scale);

			$xpos = (int)(($new_width - $new_scale_width) / 2);
			$ypos = (int)(($new_height - $new_scale_height) / 2);


			$new_image = imagecreatetruecolor($new_width, $new_height);

			if ($img_origin_info['mime'] == 'image/gif') {
				$old_image = imagecreatefromgif($this->dir_image_origin.$name_image);
				imagecolorallocate($new_image, 255, 255, 255);
			} elseif ($img_origin_info['mime'] == 'image/png') {
				$new_image = imagecreate($new_width, $new_height);
				$old_image = imagecreatefrompng($this->dir_image_origin.$name_image);
				imagecolorallocatealpha($new_image, 255, 255, 255, 127);
			} elseif ($img_origin_info['mime'] == 'image/jpeg') {
				$old_image = imagecreatefromjpeg($this->dir_image_origin.$name_image);
				imagecolorallocate($old_image, 255, 255, 255);
			}

			imagecopyresampled($new_image, $old_image, $xpos, $ypos, 0, 0, $new_scale_width, $new_scale_height, $img_origin_info[0], $img_origin_info[1]);

			$file = $dir_save.$name_image;
			if ($img_origin_info['mime'] == 'image/jpeg') {
				imagejpeg($new_image, $file, 100);
			} elseif ($img_origin_info['mime'] == 'image/png') {
				imagepng($new_image, $file);
			} elseif ($img_origin_info['mime'] == 'image/gif') {
				imagegif($new_image, $file);
			}

			imagedestroy($new_image);
			imagedestroy($old_image);

			return TRUE;
		}
	}
?>