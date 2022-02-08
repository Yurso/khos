<div id="login">

	<h2>Восстановление пароля</h2>
	
	<?php if ($this->stage == 1) : ?>
		<form action="/system/user/recovery" method="post" id="mainform">	
			<p>Введите ваш адрес электронной почты и нажмите "Продолжить"</p>
			<input type="email" name="email" placeholder="Email" autofocus />				
			<input type="hidden" name="stage" value="<?php echo $this->stage; ?>" />
			<input type="submit" name="submit" value="Продолжить" />
		</form>
	<?php endif; ?>

	<?php if ($this->stage == 3) : ?>
		<form action="/system/user/recovery" method="post" id="mainform">	
			<p>Придумайте новый пароль для вашей учетной записи и введите его в двух полях</p>
			<input type="password" name="password" placeholder="Пароль" autofocus required />				
			<input type="password" name="check" placeholder="Повтор пароля" required />				
			<input type="hidden" name="code" value="<?php echo $this->code; ?>" />
			<input type="hidden" name="stage" value="<?php echo $this->stage; ?>" />
			<input type="submit" name="submit" value="Продолжить" />
		</form>
	<?php endif; ?>

	<div class="messages">
		<?php foreach ($this->messages as $message) : ?>
			<p><?php echo $message; ?></p>
		<?php endforeach; ?>
	</div>

	<p><a href="/system/user/login">< Вернуться на старинцу авторизации</a></p>

</div>