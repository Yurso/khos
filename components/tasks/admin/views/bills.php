<h2 class="content-title">Счета на оплату</h2>

<?php include('menu.php'); ?>

<?php echo htmler::_tableFilters($this->filters); ?>

<form method="post" action="/admin/tasks/bills" class="adminform" name="itemsForm">
    <table class="main-table tasks" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th width="25"><input type="checkbox" onClick="toggle(this)"></th>   
                <th width="60"><?php echo htmler::tableSort('create_date', 'Дата'); ?></th>             
                <th style="text-align:left;"><?php echo htmler::tableSort('title', 'Заголовок'); ?></th>            
                <th width="100"><?php echo htmler::tableSort('sum', 'Сумма'); ?></th>
                <th width="70"><?php echo htmler::tableSort('state', 'Активен'); ?></th>
                <th width="70"><?php echo htmler::tableSort('paid', 'Оплачен'); ?></th>
                <th width="25"><?php echo htmler::tableSort('id', 'id'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this->items as $item) : ?>
            <tr class="state<?php echo $item->state; ?> paid<?php echo $item->paid; ?>">
                <td align="center"><input type="checkbox" name="checked[]" value="<?php echo $item->id; ?>"></td>
                <td align="center">
                    <?php echo date("d.m.y", strtotime($item->create_date)); ?>
                </td>                 
                <td style="text-align:left;">
                    <a href="/admin/tasks/bills/edit/<?php echo $item->id; ?>" class="state<?php echo $item->state; ?> paid<?php echo $item->paid; ?>"><?php echo $item->title; ?></a>
                </td>  
                <td align="center"><?php echo htmler::esc_price($item->sum); ?></td>                                 
                <td align="center"><?php echo htmler::YesNo($item->state); ?></td>
                <td align="center"><?php echo htmler::YesNo($item->paid); ?></td>
                <td align="center"><?php echo $item->id; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="buttons">
    	<a href="/admin/tasks/bills/create" title="Создать новый элемент">Создать</a>
        <!-- <a href="#" onClick="return submitForm(itemsForm, '/duplicate');" title="Скопировать">Скопировать</a> -->
    	<a href="#" onClick="return submitForm(itemsForm, '/delete');" title="Удалить элементы">Удалить</a>
    </div>
</form>

<?php $this->pagination->display(); ?>