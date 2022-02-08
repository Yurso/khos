<h2 class="content-title">Менеджер алиасов</h2>

<?php echo htmler::_tableFilters($this->filters); ?>

<form method="post" action="/admin/system/aliases" class="adminform" name="adminForm">
	<table class="main-table">
		<thead>
			<tr>
				<th width="25"><input type="checkbox" onClick="toggle(this)"></th>
				<th style="text-align:left;"><?php echo htmler::tableSort('alias', 'Алиас'); ?></th>
				<th style="text-align:left;"><?php echo htmler::tableSort('url', 'Ссылка'); ?></th>
				<th width="25"><?php echo htmler::tableSort('id', 'id'); ?></th>
			</tr>
		</thead>
		<tbody class="sortable">
			<?php foreach($this->items as $item) : ?>
				<tr id="item-<?php echo $item->id; ?>">
					<td style="text-align:center;"><input type="checkbox" name="checked[]" value="<?php echo $item->id; ?>"></td>
					<td style="text-align:left;"><a href="/admin/system/aliases/edit/<?php echo $item->id; ?>"><?php echo $item->alias; ?></a></td>
					<td style="text-align:left;"><?php echo $item->url; ?></td>
					<td><?php echo $item->id; ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</form>

<div class="buttons">
	<a href="/admin/system/aliases/create" title="Создать новый элемент">Создать</a>
    <a href="#" onClick="return submitForm(itemsForm, '/duplicate');" title="Скопировать">Скопировать</a>
	<a href="#" onClick="return submitForm(itemsForm, '/delete');" title="Удалить элементы">Удалить</a>
</div>

<?php $this->pagination->display(); ?>