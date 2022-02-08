<h2 class="content-title"><?php echo $this->tmpl_page_title; ?></h2>

<?php include('menu.php'); ?>

<h3>Очистка старых файлов</h3>
<form action="/admin/tasks/maintenance/clear_files_on_date" method="post">
	<p>
		<label for="date_before">Укажите дату до которой необходимо очистить файлы:</label><br />
		<input type="text" name="date_before" id="date_before" class="datepicker">
	</p>
	<p><input type="submit" value="Очистить"></p>
</form>