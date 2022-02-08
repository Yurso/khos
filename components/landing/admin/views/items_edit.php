<h2 class="content-title">Редактор блоков лендинга</h2>

<form method="post" action="/admin/landing/items" class="adminform" name="adminForm" enctype="multipart/form-data">

    <div class="block" style="width:75%;">
    	<div class="block-title">Основное</div>
    	<div class="block-item">
            <label>Заголовок:</label><br />
            <input type="text" name="title" value="<?php echo $this->item->title; ?>" required autofocus>
        </div>
        <div class="block-item">
            <label>Опубликован:</label><br />
            <?php echo htmler::booleanSelect($this->item->state, 'state'); ?>
        </div>
        <?php echo $this->edit_form; ?>
    </div>

    <div class="block" style="width:20%;">
        <div class="block-title">Дополнительно</div>
        <div class="block-item">
            <label>Автор:</label><br />
            <input type="text" name="author_name" value="<?php echo $this->item->author_name; ?>" disabled>
        </div>
        <div class="block-item">
            <label>Дата создания:</label><br />
            <input type="text" name="create_date" class="datepicker" value="<?php echo $this->item->create_date; ?>" disabled>
        </div>
        <div class="block-item">
            <label>Дата последнего изменения:</label><br />
            <input type="text" name="edit_date" class="datepicker" value="<?php echo $this->item->modify_date; ?>" disabled>
        </div>
        <div class="block-item">
            <label>Порядок:</label><br />
            <input type="number" name="ordering" value="<?php echo $this->item->ordering; ?>">
        </div>
        <?php echo $this->params_form; ?>
    </div>

	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>">
    <input type="hidden" name="type" value="<?php echo $this->item->type; ?>">

    <div class="buttons">
        <?php echo htmler::formButtons($this->buttons, 'adminForm'); ?>       
    </div>

</form>

