<h2 class="content-title">Новая тема</h2>

<form method="post" action="/admin/forum/topics/save" class="adminform">

    <div class="block" style="width:700px;">
    	<div class="block-title">Основное</div>
    	<div class="block-item">
            <label>Заголовок:</label><br />
            <input type="text" name="title" value="<?php echo $this->item->title; ?>" required autofocus>
        </div>
        <div class="block-item">
            <label>Описание темы:</label><br />
            <textarea name="message" required><?php echo $this->item->message; ?></textarea>
        </div>
    </div>

	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>">
    <input type="hidden" name="category_id" value="<?php echo $this->item->category_id; ?>">

    <div class="buttons">        
        <input type="submit" name="save" value="Сохранить">
        <a href="/admin/forum/" title="Закрыть">Закрыть</a>
    </div>

</form>

