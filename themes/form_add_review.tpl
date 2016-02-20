<form id="add-review" enctype="multipart/form-data" class="col2" method="POST" action="/review">
	<h2>оставить свой отзыв</h2>
	<p>ИМЯ И ФАМИЛИЯ <span>*</span></p>
	<p class="message_error">{$error:name_user}</p>
	<input type="name" onfocus="removeError(this)" name="name_user" value="{$post:name_user}" class="{$add_class:name_user}"/>
	<p>EMAIL (Не выводиться в комментариях)</p>
	<p class="message_error">{$error:email_user}</p>
	<input type="email" name="email_user" value="{$post:email_user}" class="{$add_class:email_user}"/>
	<p>НАЗВАНИЕ ОТЗЫВА <span>*</span></p>
	<p class="message_error">{$error:name_review}</p>
	<input type="name" name="name_review" value="{$post:name_review}" class="{$add_class:name_review}"/>
	<p>СООБЩЕНИЕ <span>*</span></p>
	<p class="message_error">{$error:message_review}</p>
	<textarea name="message_review" class="{$add_class:message_review}">{$post:message_review}</textarea>
	<div id="captcha">
		<img src="{$captcha_image}" height="40px" width="100px" />
		<p class="message_error">{$error:captcha}</p>
		<p class="col3 fl">СУММА ПЕРВЫХ ДВОХ ЧИСЕЛ <span>*</span></p>
		<input type="text" name="captcha-sum" class="{$add_class:captcha} col3 fl"/>
		<p class="col4 fl">ТЕКСТ С КАРТИНКИ <span>*</span></p>
		<input type="text" name="captcha-text"  class="{$add_class:captcha} col4 fl"/>
	</div>
	<p id="header_file">ПРИКРЕПИТЬ ИЗОБРАЖЕНИЕ</p>
	<p class="message_error">{$error:load_file}</p>
	<input type="file" name="pictures" />
	<input type="hidden" name="MAX_FILE_SIZE" value="{$apload_max_filesize}" />
	<input type="submit" value="Добавить отзыв">
	<p>Поля, отмеченные звездочкой (*) обязательны для заполнения</p>
</form>