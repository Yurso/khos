<h2 class="content-title">Редактор страниц</h2>

<form method="post" action="/admin/content/pages/save" class="adminform">

    <div class="block" style="width:700px;">
    	<div class="block-title">Основное</div>
    	<div class="block-item">
            <label>Заголовок:</label><br />
            <input type="text" name="title" value="<?php echo $this->data->title; ?>" required autofocus>
        </div>
            <div class="block-item">
            <label>Алиас:</label><br />
            <input type="text" name="alias" value="<?php echo $this->data->alias; ?>">
        </div>
        <div class="block-item">
            <label>Опубликован:</label><br />
            <?php echo htmler::booleanSelect($this->data->state, 'state'); ?>
        </div>
        <div class="block-item">
            <label>Текст:</label><br />
            <textarea class="wysywig" name="content"><?php echo $this->data->content; ?></textarea>
        </div>
        <div class="block-item">
            <label>Комментарий:</label><br />
            <input type="text" name="comments" value="<?php echo $this->data->comments; ?>">
        </div>
    </div>

    <div class="block">
        <div class="block-title">Дополнительно</div>
        <div class="block-item">
            <label>Автор:</label><br />
            <input type="text" name="author_name" value="<?php echo $this->data->author_name; ?>" disabled>
        </div>
        <div class="block-item">
            <label>Дата создания:</label><br />
            <input type="text" name="create_date" class="datepicker" value="<?php echo $this->data->create_date; ?>">
        </div>
        <div class="block-item">
            <label>Дата последнего изменения:</label><br />
            <input type="text" name="edit_date" class="datepicker" value="<?php echo $this->data->edit_date; ?>">
        </div>
    </div>

	<input type="hidden" name="id" value="<?php echo $this->data->id; ?>">

    <div class="buttons">
        <input type="submit" name="save" value="Сохранить">        
        <input type="submit" name="apply" value="Применить">
        <a href="/admin/content/blog" title="Закрыть">Закрыть</a>        
    </div>

</form>

