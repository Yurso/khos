<h2 class="content-title">Редактор записей блога</h2>

<form method="post" action="/admin/content/blog/save" class="adminform" name="itemForm">

    <div class="block" style="width:980px;">
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
            <label>Категория:</label><br />
            <select name="category_id" required>
                <option value="">- Выберите категорию -</option>
                <?php foreach ($this->categories as $key => $category) : ?>
                    <option value="<?php echo $category->id; ?>" <?php if ($category->id == $this->data->category_id) echo 'selected'; ?>><?php echo $category->title; ?></option>
                <?php endforeach; ?>
            </select>
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
            <label>Ключевые слова:</label><br />
            <input type="text" name="tags" class="tags" value="<?php echo $this->data->tags; ?>">            
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
            <input type="text" name="create_date" class="datepicker" value="<?php echo $this->data->create_date; ?>" disabled>
        </div>
        <div class="block-item">
            <label>Дата последнего изменения:</label><br />
            <input type="text" name="edit_date" class="datepicker" value="<?php echo $this->data->edit_date; ?>" disabled>
        </div>
        <div class="block-item">
            <label>Дата публикации:</label><br />
            <input type="text" name="public_date" class="datepicker" value="<?php echo $this->data->public_date; ?>">
        </div>
        <div class="block-item">
            <label>Комментарий:</label><br />
            <input type="text" name="comments" value="<?php echo $this->data->comments; ?>">
        </div>
    </div>

    <?php if (count($this->data->params)) : ?>
        <div class="block">
            <div class="block-title">Параметры</div>
            <?php foreach ($this->data->params as $key => $value) : ?>
                <div class="block-item">
                    <label><?php echo $key; ?></label><br />
                    <input type="text" name="params[<?php echo $key; ?>]" value="<?php echo $value; ?>">
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

	<input type="hidden" name="id" value="<?php echo $this->data->id; ?>">

    <div class="buttons">
    	<input type="submit" name="save" value="Сохранить">        
        <input type="submit" name="apply" value="Применить">
        <a href="/admin/content/blog" title="Закрыть">Закрыть</a>        
    </div>

</form>

