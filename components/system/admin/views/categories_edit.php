<h2 class="content-title">Редактор категорий</h2>

<form method="post" action="/admin/system/categories/save" class="adminform">

    <div class="block" style="width:700px;">
    	<div class="block-title">Основное</div>
    	<div class="block-item">
            <label>Заголовок:</label><br />
            <input type="text" name="title" value="<?php echo $this->item->title; ?>" required autofocus>
        </div>
        <div class="block-item">
            <label>Алиас:</label><br />
            <input type="text" name="alias" value="<?php echo $this->item->alias; ?>">
        </div>
        <div class="block-item">
            <label>Компонент:</label><br />
            <select name="component" required <?php if ($this->item->id > 0) echo 'disabled'; ?>>
                <option value="">- Выберите компонент -</option>                
                <?php foreach ($this->components as $component) : ?>
                    <option value="<?php echo $component->name; ?>" <?php if ($component->name == $this->item->component) echo 'selected'; ?>><?php echo $component->name; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="block-item">
            <label>Родитель:</label><br />
            <select name="parent_id">
                <option value="0">- Нет родителя -</option>                
                <?php foreach ($this->parents as $parent) : ?>
                    <option value="<?php echo $parent->id; ?>" <?php if ($parent->id == $this->item->parent_id) echo 'selected'; ?>><?php echo $parent->title; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="block-item">
            <label>Опубликован:</label><br />
            <?php echo htmler::booleanSelect($this->item->state, 'state'); ?>
        </div>
        <div class="block-item">
            <label>Описание:</label><br />
            <textarea class="redactor" name="description"><?php echo $this->item->description; ?></textarea>
        </div>
    </div>

	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>">

    <div class="buttons">
    	<input type="submit" name="save" value="Сохранить">        
        <input type="submit" name="apply" value="Применить">
        <a href="/admin/system/categories" title="Закрыть">Закрыть</a>
    </div>

</form>

