<h2 class="content-title">Редактро видов работ</h2>

<form method="post" action="/admin/tasks/types/save" class="adminform" name="itemForm" enctype="multipart/form-data">

    <div class="block">
    	<div class="block-title">Основное</div>
    	<div class="block-item">
            <label>Заголовок:</label><br />
            <input type="text" name="title" value="<?php echo $this->item->title; ?>" required autofocus>
        </div>
        <div class="block-item">
            <label>Прайс по-умолчанию:</label><br />
            <input type="text" name="default_price" value="<?php echo $this->item->default_price; ?>">
        </div>      
    </div>

	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>">

    <div class="buttons">
        <input type="submit" name="save" value="Сохранить">        
        <input type="submit" name="apply" value="Применить">
        <a href="/admin/tasks/types" title="Закрыть">Закрыть</a>        
    </div>
    
</form>

