<h2 class="content-title">Менеджер комментариев</h2>

<?php echo htmler::tableFilters($this->filters); ?>

<form method="post" action="/admin/content/comments" class="adminform" name="itemsForm">
    <table class="main-table" border="0">
        <thead>
            <tr>
                <th width="25"><input type="checkbox" onClick="toggle(this)"></th>
                <th style="text-align:left;"><?php echo htmler::tableSort('title', 'Заголовок'); ?></th>            
                <th width="180"><?php echo htmler::tableSort('controller', 'Контроллер'); ?></th>
                <th width="180"><?php echo htmler::tableSort('create_date', 'Дата создания'); ?></th>                
                <th width="180"><?php echo htmler::tableSort('edit_date', 'Дата редактирования'); ?></th>
                <th width="100"><?php echo htmler::tableSort('state', 'Опубликовано'); ?></th>
                <th width="25"><?php echo htmler::tableSort('id', 'id'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this->items as $item) : ?>
                <tr>
                    <td style="text-align:center;"><input type="checkbox" name="checked[]" value="<?php echo $item->id; ?>"></td>
                    <td style="text-align:left;">
                        <a href="/admin/content/comments/edit/<?php echo $item->id; ?>"><?php echo $item->title; ?></a>
                    </td>   
                    <td><?php echo $item->controller; ?></td>         
                    <td><?php echo $item->create_date; ?></td>                
                    <td><?php echo $item->edit_date; ?></td>
                    <td><?php echo htmler::YesNo($item->state); ?></td>
                    <td align="center"><?php echo $item->id; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="buttons">
    	<a href="/admin/content/comments/create" title="Создать новый элемент">Создать</a>
        <a href="#" onClick="return submitForm(itemsForm, '/duplicate');" title="Скопировать">Скопировать</a>
        <a href="#" onClick="return submitForm(itemsForm, '/delete');" title="Удалить элементы">Удалить</a>
    </div>
</form>

<?php $this->pagination->display(); ?>

