<h2 class="content-title">Редактор параметра</h2>

<form method="post" action="/admin/realty/params/save" class="adminform" name="itemForm">

    <div class="block" style="width:980px;">
    	<div class="block-title">Основное</div>
    	<div class="block-item">
            <label>Название:</label><br />
            <input type="text" name="label" value="<?php echo $this->data->label; ?>" required autofocus>
        </div>
        <div class="block-item">
            <label>Имя (латиницей):</label><br />
            <input type="text" name="name" value="<?php echo $this->data->name; ?>" required>
        </div>
        <div class="dp20">
            <div class="block-item">
                <label>Опубликован:</label><br />
                <?php echo htmler::booleanSelect($this->data->state, 'state'); ?>
            </div>
        </div>
        <div class="clr"></div>
        <div class="block-item">
            <label>Описание:</label><br />
            <textarea class="redactor" name="description"><?php echo $this->data->description; ?></textarea>
        </div>
    </div>

    <div class="block">
        <div class="block-title">Дополнительно</div>
        <div class="block-item">
            <label>Тип:</label><br />
            <select name="type">
                <option value="input" <?php if ($this->data->type == 'input') echo 'selected'; ?>>Поле ввода</option>
                <option value="select" <?php if ($this->data->type == 'select') echo 'selected'; ?>>Список выбора</option>
            </select>
        </div>
        <div class="block-item">
            <label>Ширина поля (%):</label><br />
            <input type="text" name="field_width" value="<?php echo $this->data->field_width; ?>">
        </div>
    </div>

	<input type="hidden" name="id" value="<?php echo $this->data->id; ?>">

    <div class="buttons">
    	<input type="submit" name="save" value="Сохранить">        
        <input type="submit" name="apply" value="Применить">
        <a href="/admin/realty/params" title="Закрыть">Закрыть</a>        
    </div>

</form>

