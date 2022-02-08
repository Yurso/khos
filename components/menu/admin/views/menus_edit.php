<h2 class="content-title">Редактор записей блога</h2>

<form method="post" action="/admin/menu/menus//save" class="adminform">

    <div class="block" style="width:700px;">
    	<div class="block-title">Основное</div>
    	<div class="block-item">
            <label>Заголовок:</label><br />
            <input type="text" name="name" value="<?php echo $this->item->name; ?>" required autofocus>
        </div>
        <div class="block-item">
            <label>Описание:</label><br />
            <input type="text" name="description" value="<?php echo $this->item->description; ?>">
        </div>
    </div>

	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>">

    <div class="buttons">
    	<input type="submit" value="Сохранить">
        <a href="/admin/menu/menus/" title="Закрыть">Закрыть</a>
    </div>

</form>

