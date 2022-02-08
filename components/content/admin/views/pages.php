<h2 class="content-title">Страницы</h2>

<?php echo htmler::_tableFilters($this->filters); ?>

<form method="post" action="/admin/content/pages" class="adminform" name="itemsForm">
    <table class="main-table" border="0">
        <tbody>
            <tr>
                <th width="25"><input type="checkbox" onClick="toggle(this)"></th>
                <th style="text-align:left;"><?php echo htmler::tableSort('p.title', 'Заголовок'); ?></th>            
                <th width="180"><?php echo htmler::tableSort('u.name', 'Автор'); ?></th>
                <th width="180"><?php echo htmler::tableSort('p.create_date', 'Дата создания'); ?></th>                
                <th width="100"><?php echo htmler::tableSort('p.state', 'Опубликовано'); ?></th>
                <th width="25"><?php echo htmler::tableSort('p.id', 'id'); ?></th>
            </tr>
            <?php foreach ($this->items as $item) : ?>
            <tr>
                <td style="text-align:center;"><input type="checkbox" name="checked[]" value="<?php echo $item->id; ?>"></td>
                <td style="text-align:left;">
                    <a href="/admin/content/pages/edit/<?php echo $item->id; ?>"><?php echo $item->title; ?></a><br />
                    <?php if (!empty($item->alias)) : ?>
                        <span style="font-size:10px;">(<?php echo $item->alias; ?>)</span>
                    <?php endif; ?>
                </td>   
                <td><?php echo $item->author_name; ?></td>         
                <td><?php echo $item->create_date; ?></td>                
                <td><?php echo htmler::YesNo($item->state); ?></td>
                <td align="center"><?php echo $item->id; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</form>

<div class="buttons">
	<a href="/admin/content/pages/create" title="Создать новый элемент">Создать</a>
    <a href="#" onClick="return submitForm(itemsForm, '/duplicate');" title="Скопировать">Скопировать</a>
    <a href="#" onClick="return submitForm(itemsForm, '/delete');" title="Удалить элементы">Удалить</a>
</div>


<?php $this->pagination->display(); ?>

