<h2 class="content-title">Магазин</h2>

<div class="controller-menu">
    <a href="/admin/categories?controller=shop">Категории</a> |
    <a href="/admin/shop/units">Единицы измерения</a>
</div>

<?php echo htmler::tableFilters($this->filters); ?>

<!-- <form method="post" name="categoriesForm">
    <div class="options">
        <label>Категория: </label>
        <select name="category" onchange="categoriesForm.submit();">
            <option value="0">- Выберите категорию -</option>
            <?php foreach ($this->categories as $key => $value) : ?>
                <option value="<?php echo $value->id; ?>" <?php if(false) echo 'selected'; ?>><?php echo $value->title; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</form> -->

<form method="post" action="/admin/shop/products" class="adminform" name="itemsForm">
    <table class="main-table" border="0">
        <thead>
            <tr>
                <th width="25"><input type="checkbox" onClick="toggle(this)"></th>
                <th style="text-align:left;"><?php echo htmler::tableSort('title', 'Заголовок'); ?></th>            
                <th width="180"><?php echo htmler::tableSort('price', 'Цена'); ?></th>
                <th width="100"><?php echo htmler::tableSort('state', 'Опубликовано'); ?></th>
                <th width="25"><?php echo htmler::tableSort('id', 'id'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this->items as $item) : ?>
                <tr>
                    <td style="text-align:center;"><input type="checkbox" name="checked[]" value="<?php echo $item->id; ?>"></td>
                    <td style="text-align:left;">
                        <a href="/admin/shop/products/edit/<?php echo $item->id; ?>"><?php echo $item->title; ?></a><br />
                        <?php if (!empty($item->alias)) : ?>
                            <span style="font-size:10px;">(<?php echo $item->alias; ?>)</span>
                        <?php endif; ?>
                    </td>   
                    <td><?php echo $item->price; ?></td>         
                    <td><?php echo htmler::YesNo($item->state); ?></td>
                    <td align="center"><?php echo $item->id; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="buttons">
    	<a href="/admin/shop/products/create" title="Создать новый элемент">Создать</a>
        <a href="#" onClick="return submitForm(itemsForm, '/duplicate');" title="Скопировать">Скопировать</a>
        <a href="#" onClick="return submitForm(itemsForm, '/delete');" title="Удалить элементы">Удалить</a>
    </div>
</form>

<?php $this->pagination->display(); ?>

