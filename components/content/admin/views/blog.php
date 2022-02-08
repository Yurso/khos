<h2 class="content-title">Блог</h2>

<?php echo htmler::_tableFilters($this->filters); ?>

<form method="post" action="/admin/content/blog" class="adminform" name="itemsForm">
    <table class="main-table" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th width="25"><input type="checkbox" onClick="toggle(this)"></th>
                <th style="text-align:left;"><?php echo htmler::tableSort('b.title', 'Заголовок'); ?></th>            
                <th width="180"><?php echo htmler::tableSort('c.title', 'Категория'); ?></th>
                <th width="180"><?php echo htmler::tableSort('u.name', 'Автор'); ?></th>
                <th width="180"><?php echo htmler::tableSort('b.create_date', 'Дата создания'); ?></th>
                <th width="180"><?php echo htmler::tableSort('b.public_date', 'Дата публикации'); ?></th>
                <th width="100"><?php echo htmler::tableSort('b.state', 'Опубликовано'); ?></th>
                <th width="25"><?php echo htmler::tableSort('b.id', 'id'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this->items as $item) : ?>
            <tr>
                <td align="center"><input type="checkbox" name="checked[]" value="<?php echo $item->id; ?>"></td>
                <td style="text-align:left;">
                    <a href="/admin/content/blog/edit/<?php echo $item->id; ?>"><?php echo $item->title; ?></a><br />
                    <?php if (!empty($item->alias)) : ?>
                        <span style="font-size:10px;">(<?php echo $item->alias; ?>)</span>
                    <?php endif; ?>
                </td>   
                <td><?php echo $item->category_title; ?></td>       
                <td><?php echo $item->author_name; ?></td>         
                <td><?php echo $item->create_date; ?></td>
                <td><?php echo $item->public_date; ?></td>
                <td><?php echo htmler::YesNo($item->state); ?></td>
                <td align="center"><?php echo $item->id; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="buttons">
    	<a href="/admin/content/blog/create" title="Создать новый элемент">Создать</a>
        <a href="#" onClick="return submitForm(itemsForm, '/duplicate');" title="Скопировать">Скопировать</a>
    	<a href="#" onClick="return submitForm(itemsForm, '/delete');" title="Удалить элементы">Удалить</a>
    </div>
</form>

<?php $this->pagination->display(); ?>