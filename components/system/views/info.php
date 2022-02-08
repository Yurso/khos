<div style="font:12pt 'Open Sans', sans-serif; ">
	<h2>Сведения о пользователе</h2>
	<p><strong><?php echo $this->user_data->name; ?></strong> (<?php echo $this->user_data->email; ?>)</p>
	<?php if ($this->user_data->access > 3) : ?>
		<a href="/admin">Панель администрирования</a><br />
	<?php endif; ?>
	<a href="/system/user/logout">Выйти</a>
</div>