<h2 class="content-title"><?php echo $this->tmpl_page_title; ?></h2>

<?php include('menu.php'); ?>

<?php echo htmler::_tableFilters($this->filters); ?>

<form method="post" action="/admin/tasks/items" class="adminform" name="itemsForm">
    <table class="main-table tasks" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th width="25"><input type="checkbox" onClick="toggle(this)"></th>
                <th width="15"><?php echo htmler::tableSort('i.status', ''); ?></th>      
                <th width="60"><?php echo htmler::tableSort('i.date', 'Дата'); ?></th>                                
                <th style="text-align:left;"><?php echo htmler::tableSort('i.title', 'Заголовок'); ?></th>                        
                <th width="140"><?php echo htmler::tableSort('c.name', 'Клиент'); ?></th>  
                <th width="140"><?php echo htmler::tableSort('p.title', 'Проект'); ?></th>  
                <th width="80"><?php echo htmler::tableSort('i.price', 'Сумма'); ?></th> 
                <th width="25"><?php echo htmler::tableSort('i.id', 'id'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this->items as $item) : ?>
            <tr class="status-<?php echo $item->status; ?>">                
                <td align="center"><input type="checkbox" name="checked[]" value="<?php echo $item->id; ?>"></td>
                <td style="width:31px;padding: 0;font-size: 14px;">
                    <?php if ($item->status == 'complete') : ?>                                                
                        <i class="far fa-check-square" style="color:orange;" title="Выполнена"></i>
                    <?php elseif ($item->status == 'paid') : ?>                                                
                        <i class="far fa-money-bill-alt" style="color:green;" title="Оплачена"></i>                        
                    <?php elseif ($item->status == 'canceled') : ?>                        
                        <i class="fas fa-ban" style="color:red;" title="Отменена"></i>
                    <?php else : ?>                        
                        <i class="far fa-file" style="color:gray;" title="Новая"></i>
                    <?php endif; ?>
                </td>    
                <td><?php echo date("d.m.y", strtotime($item->date)); ?></td>       
                <td style="text-align:left;" class="title">                    
                    <a href="/admin/tasks/items/edit/<?php echo $item->id; ?>"><?php echo empty($item->title) ? 'Без заголовка' : $item->title; ?></a><br />
                    <span class="small"><?php echo $item->type_title; ?></span>
                </td>                                              
                <td><?php echo $item->customer_name; ?></td>
                <td><?php echo empty($item->project_title) ? '-' : $item->project_title; ?></td>
                <td><?php echo htmler::esc_price($item->price*$item->count); ?></td>         
                <td align="center"><?php echo $item->id; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="buttons">
    	<a href="/admin/tasks/items/create" title="Создать новый элемент">Создать</a>
<!--         <a href="#" onClick="return submitForm(itemsForm, '/duplicate');" title="Скопировать">Скопировать</a>
        <a href="#" onClick="return submitForm(itemsForm, '/paided');" title="Оплачено">Оплачено</a> -->
    	<a href="#" onClick="return submitForm(itemsForm, '/delete');" title="Удалить элементы">Удалить</a> 
    </div>
</form>

<?php $this->pagination->display(); ?>