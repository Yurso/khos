<h2 class="content-title">Редактор изображений галереи</h2>

<form method="post" action="/admin/content/gallery/save" class="adminform">

    <div class="block" style="width:700px;">
    	<div class="block-title">Основное</div>
    	<div class="block-item">
            <label>Заголовок:</label><br />
            <input type="text" name="title" value="<?php echo $this->item->title; ?>" required autofocus>
        </div>
        <div class="block-item">
            <label>Категория:</label><br />
            <select name="category_id">
                <option value="">- Выберите категорию -</option>                
                <?php foreach ($this->categories as $key => $value) : ?>
                    <option value="<?php echo $value->id; ?>" <?php if ($value->id == $this->item->category_id) echo 'selected'; ?>><?php echo $value->title; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="block-item">
            <label>Путь к файлу:</label><br />
            <input type="text" name="pathway" value="<?php echo $this->item->pathway; ?>" required>
        </div>
        <div class="block-item">
            <label>Имя файла:</label><br />
            <input type="text" name="filename" value="<?php echo $this->item->filename; ?>" required>
        </div>
        <div class="block-item">
            <label>Опубликован:</label><br />
            <?php echo htmler::booleanSelect($this->item->state, 'state'); ?>
        </div>
        <div class="block-item">
            <label>Комментарий:</label><br />
            <textarea class="redactor" name="description"><?php echo $this->item->description; ?></textarea>
        </div>
    </div>

    <?php if ($this->item->id) : ?>
        <div class="block">
            <div class="block-title">Предпросмотр</div>
            <div class="block-item">
                <a class="fancybox" href="<?php echo $this->item->pathway.$this->item->filename; ?>"><img src="<?php echo $this->item->pathway.'thumbs/'.$this->item->filename; ?>" alt="" /></a>
            </div>
        </div>
    <?php endif; ?>

	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>">

    <div class="buttons">
    	<input type="submit" value="Сохранить">
        <a href="/admin/content/gallery" title="Закрыть">Закрыть</a>
    </div>

</form>

