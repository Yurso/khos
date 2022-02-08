<h2 class="content-title">Редактор адресов</h2>

<form method="post" action="/admin/tasks/emails/save" class="adminform" name="itemForm" enctype="multipart/form-data">

    <div class="block">
    	<div class="block-title">Основное</div>
    	<div class="block-item">
            <label>Email:</label><br />
            <input type="text" name="email" value="<?php echo $this->item->email; ?>" required autofocus>
        </div>
        <div class="block-item">
            <label>Клиент:</label><br />
            <?php echo htmler::selectListByObjectsArray($this->customers, 'id', 'name', 'customer_id', $this->item->customer_id); ?>
        </div>      
    </div>

	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>">

    <div class="buttons">
        <input type="submit" name="save" value="Сохранить">        
        <input type="submit" name="apply" value="Применить">
        <a href="/admin/tasks/emails" title="Закрыть">Закрыть</a>        
    </div>
    
</form>

