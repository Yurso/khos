<h2 class="content-title">Редактор записей блога</h2>

<form method="post" action="/admin/content/comments/save" class="adminform">

    <div class="block" style="width:700px;">
    	<div class="block-title">Основное</div>
    	<div class="block-item">
            <label>Заголовок:</label><br />
            <input type="text" name="title" value="<?php echo $this->item->title; ?>" required autofocus>
        </div>
        <div class="block-item">
            <label>Контроллер:</label><br />
            <select name="controller" <?php if ($this->item->id > 0) echo 'disabled'; ?>>
                <option value="">- Выберите котроллер -</option>                
                <?php foreach ($this->controllers as $key => $value) : ?>
                    <option value="<?php echo $value->name; ?>" <?php if ($value->name == $this->item->controller) echo 'selected'; ?>><?php echo $value->name; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="block-item">
            <label>Идентификатор записи:</label><br />
            <input type="text" name="item_id" value="<?php echo $this->item->item_id; ?>" required>
        </div>
        <div class="block-item">
            <label>Опубликован:</label><br />
            <?php echo htmler::booleanSelect($this->item->state, 'state'); ?>
        </div>
        <div class="block-item">
            <label>Комментарий:</label><br />
            <textarea class="redactor" name="comment"><?php echo $this->item->comment; ?></textarea>
        </div>
    </div>

    <div class="block">
        <div class="block-title">Дополнительно</div>

        <div class="block-item">
            <label>Имя:</label><br />
            <input type="text" name="name" value="<?php echo $this->item->name; ?>">
        </div>
        <div class="block-item">
            <label>Email:</label><br />
            <input type="text" name="email" value="<?php echo $this->item->email; ?>">
        </div>
        <div class="block-item">
            <label>Website:</label><br />
            <input type="text" name="website" value="<?php echo $this->item->website; ?>">
        </div>

        <div class="block-item">
            <label>Дата создания:</label><br />
            <input type="text" name="create_date" class="datepicker" value="<?php echo $this->item->create_date; ?>">
        </div>
        <div class="block-item">
            <label>Дата последнего изменения:</label><br />
            <input type="text" name="edit_date" class="datepicker" value="<?php echo $this->item->edit_date; ?>">
        </div>

    </div>

    <?php if ($this->item->id > 0) : ?>
        <div class="block" class="operations">    
            <div class="block-title">Операции</div>        
            <a href="/admin/content/comments/delete/<?php echo $this->item->id; ?>" style="color:red;" class="element_delete">Удалить элемент</a>
        </div>
    <?php endif; ?>

	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>">

    <div class="buttons">
    	<input type="submit" value="Сохранить">
        <a href="/admin/content/comments" title="Закрыть">Закрыть</a>
    </div>

</form>

