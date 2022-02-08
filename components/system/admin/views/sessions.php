<h2 class="content-title">Активные сессии</h2>

<?php echo htmler::_tableFilters($this->filters); ?>

<form method="post" action="/admin/system/sessions" class="adminform" name="itemsForm">
    <table class="main-table" border="0">
        <tbody>
            <tr>
                <th width="25"><input type="checkbox" onClick="toggle(this)"></th>
                <th style="text-align:left;"><?php echo htmler::tableSort('u.user_name', 'Имя пользователя'); ?></th>            
                <th width="180"><?php echo htmler::tableSort('s.ip', 'ip'); ?></th>
                <th width="180"><?php echo htmler::tableSort('s.start_date', 'Дата начала'); ?></th>                
                <th width="180"><?php echo htmler::tableSort('s.active_date', 'Последняя активность'); ?></th>
                <th width="100"><?php echo htmler::tableSort('s.last_page', 'Последняя страница'); ?></th>
                <th width="25"><?php echo htmler::tableSort('s.stored', 'Запомнить'); ?></th>
                <th width="25"><?php echo htmler::tableSort('p.id', 'id'); ?></th>
            </tr>
            <?php foreach ($this->items as $item) : ?>
            <tr>
                <td style="text-align:center;"><input type="checkbox" name="checked[]" value="<?php echo $item->id; ?>"></td>
                <td style="text-align:left;">
                    <a href="/admin/system/sessions/edit/<?php echo $item->id; ?>"><?php echo $item->user_name; ?></a>
                </td>   
                <td><?php echo $item->ip; ?></td>         
                <td><?php echo $item->start_date; ?></td>                
                <td><?php echo $item->active_date; ?></td>                
                <td><?php echo $item->last_page; ?></td>                
                <td align="center"><?php echo htmler::YesNo($item->stored); ?></td>                         
                <td align="center"><?php echo $item->id; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</form>

<div class="buttons">
	<a href="/admin/system/sessions/create" title="Создать новый элемент">Создать</a>
    <a href="#" onClick="return submitForm(itemsForm, '/duplicate');" title="Скопировать">Скопировать</a>
    <a href="#" onClick="return submitForm(itemsForm, '/delete');" title="Удалить элементы">Удалить</a>
</div>


<?php $this->pagination->display(); ?>

