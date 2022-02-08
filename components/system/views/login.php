<p style="text-align: center;margin-top: 20px;"><img src="/public/images/logo.png" alt="Beescom"></p>
<div id="login">	
	<h2>Авторизация</h2>
	<form action="/system/user/login" method="post" id="mainform">
		
		<div style="text-align: left;">
			<label for="email">Email:</label><br />
			<input type="email" name="email" placeholder="your@email.com" autofocus />
		</div>
		
		<div style="text-align: left;">
			<label for="email">Пароль:</label><br />
			<input type="password" name="password" placeholder="password" />
		</div>

		<input type="checkbox" name="store_password" id="store_password" /><label for="store_password">Запомнить меня</label><br />

		<input type="hidden" name="redirect" value="<?php echo $this->redirect; ?>">

		<input type="submit" name="submit" value="Войти" />

	</form>
	<div class="messages">
		<?php foreach ($this->messages as $message) : ?>
			<p><?php echo $message; ?></p>
		<?php endforeach; ?>
	</div>
</div>