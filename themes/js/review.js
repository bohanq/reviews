window.onload = function(){
	var regExpForValidate = /[\@\#\$\%\^\&\*\(\)\+\=\<\>\\\/\`\~\{\}\[\]\|]/;
	var regExpForEmail 	  = /.*\@.*\..*/;
	var captchaElemError  = document.getElementById('captcha').children[1];

	//Проверка всех полей
	document.getElementById('add-review').addEventListener('submit', function(event){
		var validateUserName 	  = validateInput(this.name_user, regExpForValidate);
		var validateReviewName 	  = validateInput(this.name_review, regExpForValidate);
		var validateReviewMessage = validateInput(this.message_review, regExpForValidate);
		var validateEmail 		  = validateEmailUser(this.email_user);
		var captchaValidate 	  = validateCaptcha(document.getElementById('captcha'));
		
		if(validateUserName 	 || 
		   validateReviewName 	 || 
		   validateReviewMessage || 
		   validateEmail 		 || 
		   captchaValidate)
		{
		   event.preventDefault();
		};
	});

	//Добавление кнопки для открытия оригинала картинки
	var pictureAnchors = document.getElementsByClassName('small_presentation');
	for(var i = 0; i < pictureAnchors.length; i++){
		pictureAnchors[i].addEventListener('click', function(event){
			if(this.className === 'great_presentation'){
				var button = document.createElement('a');
				button.href = this.href;
				button.innerText = 'Открыть оригинал';
				button.className = 'open_origin_image';
				button.target = '_blank';

				this.parentNode.insertBefore(button, this);
			} else {
				this.previousElementSibling.remove();
			}
		});
	}

	// Валидация имейл адреса
	function validateEmailUser(elem){
		if(/[.*\@.*\..*]/.test(elem.value)){
			if (6 <= elem.value.length < 64){
				if (/[\#\$\%\^\&\(\)\+\=\<\>\\\/\`\~\{\}\[\]\|]/.test(elem.value)){
					elem.previousElementSibling.innerText = 'Недопустимый символ в поле';
					animateError(elem);
					return true;
				}else{
					elem.className = 'success';
					elem.previousElementSibling.innerText = '';
					return false;
				};
			}else{
				elem.previousElementSibling.innerText = 'Недопустимое количество символов (от 6 до 64 знаков)';
				animateError(elem);
				return true;
			};
		}else if(elem.value == ''){
			return false;
		}else{
			elem.previousElementSibling.innerText = 'Не коректный имейл адрес';
			animateError(elem);
			return true;
		};
	};

	//Валидация картинки
	document.forms[0].pictures.addEventListener('change', function(event){
		//Флаг валидации
		var isValid = false;
		//Максимальный размер картинки
		var maxPictureSize = this.nextElementSibling.value;
		//Расширение текущего файла
		var fileExt = this.value.substring((this.value.lastIndexOf('.')+1)).toLowerCase();
		//Допустимые расширения
		var validExts = ['png', 'jpg', 'gif', 'jpeg'];

		//Проверка на максимально допустимый размер
		if(this.files[0].size > maxPictureSize){
			this.previousElementSibling.innerText = "Принятый файл превысил максимально допустимый размер: " + parseInt(maxPictureSize/1000000) + "Мб";
			this.value = null;
			return false;
		} else {
			//Проверка на правильность расширения файла
			for(var i = 0; i < validExts.length; i++){
				if(validExts[i] === fileExt){
					isValid = true;
					break;
				}
			}
		}

		if(!isValid){
			this.previousElementSibling.innerText = "К загрузке доступны только изображения";
			this.value = null;
			return false;
		}

		this.previousElementSibling.innerText = '';
	});

	//Bалидации капчи
	function validateCaptcha(captchaBlock){
		var numCaptcha = captchaBlock.getElementsByTagName('input')[0];
		var charCaptcha = captchaBlock.getElementsByTagName('input')[1];

		if(numCaptcha.value !== '' && charCaptcha.value !== ''){
			//Проверка численного поля
			//Значение первого полня должно быть численным
			if(isNaN(numCaptcha.value)){
				captchaElemError.innerText = 'Первое поле должно состоять из цыфр';
				animateError(numCaptcha);
				return true;
			} else {
				//Сумма чисел должна быть меньше 2х символов
				if(numCaptcha.value.length > 2){
					captchaElemError.innerText = 'Первое поле не должно быть больше 2 знаков';
					animateError(numCaptcha);
					return true;
				} 
			}

			//Проверка символьного поля
			//Длинна строки должны быть не больше 4х
			if(charCaptcha.value.length > 4){
		 		captchaElemError.innerText = 'Второе поле не должно быть больше 4 знаков';
		 		animateError(charCaptcha);
		 		return true;
			} else {	
				//Поле не должно содержать числа
				if(charCaptcha.value.search(/\d/) !== -1){
		 			captchaElemError.innerText = 'Второе поле должно состоять из букв';
		 			animateError(charCaptcha);
		 			return true;
				} else {
					//Валидация пройдена
					captchaElemError.innerText = '';
					return false;
				}
			}

		} else {
			captchaElemError.innerText = 'Введите проверочные комбинации';
			animateError(numCaptcha);
			animateError(charCaptcha);
			return true;
		}
	};

	//Кросформенная валидация
	function validateInput(elem, regular){
		if(elem.value !== ''){
			if (3 <= elem.value.length < 32){
				if (regular.test(elem.value)){
					elem.previousElementSibling.innerText = 'Недопустимый символ в поле';
					animateError(elem);
					return true;
				}else{
					elem.className = 'success';
					elem.previousElementSibling.innerText = '';
					return false;
				};
			}else{
				elem.previousElementSibling.innerText = 'Недопустимое количество символов (от 3 до 32 знаков)';
				animateError(elem);
				return true;
			};
		}else{
			elem.previousElementSibling.innerText = 'Заполните поле';
			animateError(elem);
			return true;
		};
	}

	//Выдиление полей при ошбике
	function animateError(elem){
		elem.className = elem.className !== 'swing error' ? 'swing error' : 'error';
	}
};

//Удаление сообщения об ошибке
function removeError(elem){
	elem.className = '';
	elem.previousElementSibling.innerText = '';
}

// Функция увеличение/уменьшение картинки
function increaseImage(elem){
	var tagImg = elem.getElementsByTagName('img')[0];
	var oldUrlImage = tagImg.src;
	tagImg.src = elem.getAttribute('data-url-zumbox');

	if(elem.className === 'small_presentation'){
		elem.className = 'great_presentation';
	}else if(elem.className === 'great_presentation'){
		elem.className = 'small_presentation'
	};

	elem.setAttribute('data-url-zumbox', oldUrlImage);
	event.preventDefault();
};