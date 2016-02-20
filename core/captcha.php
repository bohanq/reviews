<?php
// клас генерирующий проверочное изображение и хеш сумму для подальшего сравнения
class captcha {
	// поле хранящее значение хеша
	var $hash;
	// поле хранящее картинку в виде кода (base 64)
	var $image;
	// суммарное количество символов на картинке 
	var $letter_simbol = 6;
	var $height_image = 40;
	var $width_image = 100;
	// набор символов для генераци защитного кода
	var $letters = 'qwertyuiopasdfghjklzxcvbnm';
	// сгенерированный код 
	private $captcha_code;

	// запускаем методы генерации секретного кода и картинки при создании объект
	function __construct(){
		$this->create_chars();
		$this->create_image();
	}

	// метод создания случайного секретного кода
	public function create_chars(){
		$one_number = rand(0,9);
		$two_number = rand(0,9);
		for ($i=0; $i < $this->letter_simbol-2; $i++) { 
			$number_letter = rand(0, strlen($this->letters));
			$captcha_text .= $this->letters{$number_letter};
		}
		$this->captcha_code = $one_number.$two_number.' '.$captcha_text;
		$this->hash = md5($one_number+$two_number.$captcha_text);
	}

	// метод создания картинки на основе секретного кода 
	public function create_image(){
		$image = imagecreate(100, 40);
		$bg_color = imagecolorallocate ($image, 233, 233, 233);
		$text_color = imagecolorallocate ($image, 233, 14, 91);
		$font = imageloadfont(ROOT_DIR.'/font/HomBoldB_16x24_LE.gdf');
		for($i=0; $i < $this->letter_simbol+1; $i++){
			imagestring ($image, $font, $i*14, 12, $this->captcha_code{$i}, $text_color);
		}
		$image = imagerotate($image, rand(-5, 5), 0);
		// Включить буферизацию вывода
		ob_start();
		imagepng($image);
		// записываем выход в буфер 
		$imagedata = ob_get_contents();
		// очищаем буфер
		ob_end_clean();
		$this->image = 'data:image/png;base64, '.base64_encode($imagedata);
	}
}
?>