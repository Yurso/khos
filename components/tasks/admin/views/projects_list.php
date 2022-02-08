<h2 class="content-title"><?php echo $this->tmpl_page_title; ?></h2>

<?php include('menu.php'); ?>

<?php echo htmler::_tableFilters($this->filters); ?>

<form method="post" action="/admin/tasks/projects" class="adminform" name="itemsForm">
    <table class="main-table" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th width="25"><input type="checkbox" onClick="toggle(this)"></th>
                <th style="text-align:left;"><?php echo htmler::tableSort('p.title', 'Название'); ?></th>            
                <th width="100"><?php echo htmler::tableSort('c.name', 'Клиент'); ?></th>
                <th width="100"><?php echo htmler::tableSort('p.state', 'Активно'); ?></th>                
                <th width="25"><?php echo htmler::tableSort('id', 'id'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this->items as $item) : ?>
            <tr>
                <td align="center"><input type="checkbox" name="checked[]" value="<?php echo $item->id; ?>"></td>
                <td style="text-align:left;">
                    <a href="/admin/tasks/projects/edit/<?php echo $item->id; ?>"><?php echo $item->title; ?></a>
                </td>  
                <td align="center"><?php echo $item->customer_name; ?></td> 
                <td align="center"><?php echo htmler::YesNo($item->state); ?></td>
                <td align="center"><?php echo $item->id; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="buttons">
    	<a href="/admin/tasks/projects/create" title="Создать новый элемент">Создать</a>
        <a href="#" onClick="return submitForm(itemsForm, '/duplicate');" title="Скопировать">Скопировать</a>
    	<a href="#" onClick="return submitForm(itemsForm, '/delete');" title="Удалить элементы">Удалить</a>
    </div>
</form>

<?php $this->pagination->display(); ?>