<h2 class="content-title">Редактор сообщений</h2>

<form method="post" action="/admin/forum/messages/save" class="adminform">

    <div class="block" style="width:700px;">
    	<div class="block-title">Основное</div>
        <div class="block-item">
            <label>Текст сообщения:</label><br />
            <textarea name="message" required><?php echo $this->item->message; ?></textarea>
        </div>
    </div>

    <?php if (User::getUserData('access_name') != 'administrator') : ?>
        <div class="block">
            <div class="block-title">Дополнительно</div>
            <div class="block-item">
                <label>Дата создания:</label><br />
                <input type="text" name="create_date" class="datepicker" value="<?php echo $this->item->create_date; ?>">
            </div>
            <div class="block-item">
                <label>Дата последнего редактирования:</label><br />
                <input type="text" name="edit_date" class="datepicker" value="<?php echo $this->item->edit_date; ?>">
            </div>
        </div>
    <?php endif; ?>

	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>">

    <div class="buttons">        
        <input type="submit" name="submit" value="Сохранить">
        <a href="/admin/forum/messages/view/<?php echo $this->item->topic_id; ?>" title="Закрыть">Закрыть</a>
    </div>

</form>

