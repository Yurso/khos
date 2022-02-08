<h2 class="content-title">Счет на оплату</h2>

<form method="post" action="/admin/tasks/bills/save" class="adminform" name="itemForm" enctype="multipart/form-data">

    <div class="block" style="width:400px;">
    	<div class="block-title">Основное</div>
        <div class="dp100">
        	<div class="block-item">
                <label>Заголовок:</label><br />
                <input type="text" name="title" value="<?php echo $this->item->title; ?>" required autofocus>
            </div>
        </div>
        <div class="dp100">
            <div class="block-item">
                <label>Описание:</label><br />
                <textarea name="description" id="description" style="height:150px;"><?php echo $this->item->description; ?></textarea>
            </div> 
        </div>
        <div class="clr"></div>
    </div>

    <div class="block">
        <div class="block-title">Параметры</div>
        <div class="block-item">
            <label>Активен:</label><br />
            <?php echo htmler::booleanSelect($this->item->state, 'state'); ?>
        </div>
        <div class="block-item">
            <label>Оплачен:</label><br />
            <?php echo htmler::booleanSelect($this->item->paid, 'paid'); ?>
        </div>
        <div class="block-item">
            <label>Дата создания:</label><br />
            <input type="text" name="create_date" value="<?php echo $this->item->create_date; ?>">
        </div>   

        <div class="block-item">
            <label>Дата изменения:</label><br />
            <input type="text" name="modify_date" value="<?php echo $this->item->modify_date; ?>">
        </div>   
        <div class="clr"></div>
    </div>

<!--     <div class="block-filters">
        <div class="block-title">Фильтры</div>
        <div class="block-item">
            <label>Начало периода:</label><br />
            <input type="text" name="date_from" class="datepicker">
        </div>
        <div class="block-item">
            <label>Конец периода:</label><br />
            <input type="text" name="date_to" class="datepicker">
        </div>
        <div class="block-item">
            <label>Клиент:</label><br />
            <select name="customer_id" required>
                <?php foreach ($this->customers as $customer) : ?>
                    <option value="<?php echo $customer->id; ?>"><?php echo $customer->name; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="block-item">
            <label>Выполненые:</label><br>
            <select name="state">
                <option value="0">Все</option>
                <option value="1">Да</option>
                <option value="2">Нет</option>
            </select>
        </div>
        <div class="clr"></div>
    </div> -->
    <div class="block" style="width:90%;background: transparent;">
        <div class="block-title">Задачи по счету</div>
<!--         <p>
            <input type="button" value="Добавить">
            <input type="button" value="Удалить">
        </p> -->
        <table class="main-table tasks" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th width="60">Дата</th>
                    <th style="text-align:left;">Заголовок</th>            
                    <th width="110">Клиент</th>
                    <th width="80">Сумма</th>
                    <th width="80">Статус</th>
                    <th width="25">id</th>
                </tr>
            </thead>
            <tbody>
                <?php $sum = 0; foreach ($this->tasks as $item) : ?>
                <tr class="status-<?php echo $item->status; ?>">
                    <td><?php echo date("d.m.y", strtotime($item->date)); ?></td>
                    <td style="text-align:left;">
                        <a href="/admin/tasks/items/edit/<?php echo $item->id; ?>"><?php echo $item->title; ?></a><br />
                        <span class="small"><?php echo $item->type_title; ?></span>
                    </td>   
                    <td><?php echo $item->customer_name; ?></td>
                    <td><?php echo htmler::esc_price($item->price*$item->count); ?></td>
                    <td><?php echo $item->status; ?></td>
                    <td align="center"><?php echo $item->id; ?></td>
                </tr>
                <?php $sum = $sum + $item->price*$item->count; endforeach; ?>
            </tbody>
        </table>
        <p style="text-align: right;"><strong>Сумма:</strong> <?php echo htmler::esc_price($sum); ?></p>
    </div>

	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>">

    <div class="buttons">
        <input type="submit" name="save" value="Сохранить">        
        <input type="submit" name="apply" value="Применить">
        <a href="/admin/tasks/bills/paid/<?php echo $this->item->id; ?>" title="Подтвердить оплату">Подтвердить оплату</a>
        <a href="/admin/tasks/export/bill?bill_id=<?php echo $this->item->id; ?>" title="Экспорт">Экспорт</a>        
        <a href="/admin/tasks/bills" title="Закрыть">Закрыть</a>        
    </div>
    
</form>

