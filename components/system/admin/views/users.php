<h2 class="content-title">Менеджер пользователей</h2>

<?php echo htmler::_tableFilters($this->filters); ?>

<form method="post" action="/admin/system/users/delete" class="adminform" name="adminForm">
	<table class="main-table">
		<thead>
			<tr>
				<th width="25"><input type="checkbox" onClick="toggle(this)"></th>
				<th style="text-align:left;"><?php echo htmler::tableSort('u.name', 'Имя'); ?></th>
				<th width="200"><?php echo htmler::tableSort('u.login', 'Login'); ?></th>
				<th width="200"><?php echo htmler::tableSort('u.email', 'Email'); ?></th>
				<th width="200"><?php echo htmler::tableSort('a.name', 'Доступ'); ?></th>
				<th width="25"><?php echo htmler::tableSort('u.id', 'id'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($this->users as $user) : ?>
				<tr>
					<td style="text-align:center;"><input type="checkbox" name="checked[]" value="<?php echo $user->id; ?>"></td>
					<td style="text-align:left;"><a href="/admin/system/users/edit/<?php echo $user->id; ?>"><?php echo $user->name; ?></a></td>
					<td><?php echo $user->login; ?></td>
					<td><?php echo $user->email; ?></td>
					<td><?php echo $user->access_name; ?></td>
					<td><?php echo $user->id; ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</form>

<div class="buttons">
	<a href="/admin/system/users/create">Создать</a>
	<a href="#" onClick="adminForm.submit();return false;" title="Удалить элементы">Удалить</a>
</div>

<?php $this->pagination->display(); ?>