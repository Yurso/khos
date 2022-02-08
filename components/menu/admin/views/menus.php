<h2 class="content-title">Менеджер меню</h2>

<form method="post" action="/admin/menu/menus/delete" class="adminform" name="adminForm">
    <table class="main-table" border="0">
        <tbody>
            <tr>
                <th width="25"><input type="checkbox" onClick="toggle(this)"></th>
                <th style="text-align:left;"><?php echo htmler::tableSort('name', 'Заголовок'); ?></th>
                <th width="25"><?php echo htmler::tableSort('id', 'id'); ?></th>
            </tr>
            <?php foreach ($this->items as $item) : ?>
            <tr>
                <td style="text-align:center;"><input type="checkbox" name="checked[]" value="<?php echo $item->id; ?>"></td>
                <td style="text-align:left;">
                    <a href="/admin/menu/menus/edit/<?php echo $item->id; ?>"><?php echo $item->name; ?></a>
                </td>
                <td align="center"><?php echo $item->id; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</form>

<div class="buttons">
    <a href="/admin/menu/menus/create" title="Создать новый элемент">Создать</a>
    <a href="#" onClick="adminForm.submit();return false;" title="Удалить элементы">Удалить</a>
</div>

<?php $this->pagination->display(); ?>