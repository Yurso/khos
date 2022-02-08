<h2 class="content-title">Редактор сессий</h2>

<form method="post" action="/admin/system/sessions/save" class="adminform">

    <div class="block" style="width:700px;">
    	<div class="block-title">Основное</div>
    	<div class="block-item">
            <label>Пользователь:</label><br />
            <input type="text" name="id" value="<?php echo $this->item->id; ?>" required autofocus>
        </div>
        <div class="block-item">
            <label>Hash:</label><br />
            <input type="text" name="hash" value="<?php echo $this->item->hash; ?>">
        </div>
        <div class="block-item">
            <label>ip:</label><br />
            <input type="text" name="ip" value="<?php echo $this->item->ip; ?>">
        </div>        
        <div class="block-item">
            <label>Последняя открытая страница:</label><br />
            <input type="text" name="last_page" value="<?php echo $this->item->last_page; ?>">
        </div>
    </div>

    <div class="block">
        <div class="block-title">Дополнительно</div>
        <div class="block-item">
            <label>Дата начала:</label><br />
            <input type="text" name="start_date" class="datepicker" value="<?php echo $this->item->start_date; ?>">
        </div>
        <div class="block-item">
            <label>Дата последней активности:</label><br />
            <input type="text" name="active_date" class="datepicker" value="<?php echo $this->item->active_date; ?>">
        </div>
        <div class="block-item">
            <label>Запомнить сессию:</label><br />
            <?php echo htmler::booleanSelect($this->item->stored, 'stored'); ?>
        </div>
    </div>

	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>">

    <div class="buttons">
    	<input type="submit" value="Сохранить">
        <a href="/admin/system/sessions" title="Закрыть">Закрыть</a>
    </div>

</form>

