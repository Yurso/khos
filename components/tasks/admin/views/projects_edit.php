<h2 class="content-title">Редактор проектов</h2>

<form method="post" action="/admin/tasks/projects/save" class="adminform" name="itemForm" enctype="multipart/form-data">

    <div class="block">
    	<div class="block-title">Основное</div>
    	<div class="block-item">
            <label>Название:</label><br />
            <input type="text" name="title" value="<?php echo $this->item->title; ?>" required autofocus>
        </div>
        <div class="block-item">
            <label>Клиент:</label><br />
            <?php echo htmler::selectListByObjectsArray($this->customers, 'id', 'name', 'customer_id', $this->item->customer_id); ?>            
        </div>
        <div class="block-item">
            <label>Активен:</label><br />
            <?php echo htmler::booleanSelect($this->item->state, 'state'); ?>
        </div>
        <div class="block-item">
            <label>Описание:</label><br />
            <textarea name="description" id="description" style="height:150px;"><?php echo $this->item->description; ?></textarea>
        </div>      
    </div>

	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>">

    <div class="buttons">
        <input type="submit" name="save" value="Сохранить">        
        <input type="submit" name="apply" value="Применить">
        <a href="/admin/tasks/projects" title="Закрыть">Закрыть</a>        
    </div>
    
</form>

