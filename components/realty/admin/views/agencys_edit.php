<h2 class="content-title">Редактор агентств</h2>

<form method="post" action="/admin/realty/agencys/save" class="adminform" name="itemForm" enctype="multipart/form-data">

    <div class="block" style="width:980px;">
    	<div class="block-title">Основное</div>
    	<div class="block-item">
            <label>Наименование:</label><br />
            <input type="text" name="name" value="<?php echo $this->data->name; ?>" required autofocus>
        </div>
        <div class="block-item">
            <label>Полное наименование:</label><br />
            <input type="text" name="full_name" value="<?php echo $this->data->full_name; ?>" required>
        </div>
        <div class="block-item">
            <label>Адрес:</label><br />
            <input type="text" name="adress" value="<?php echo $this->data->adress; ?>">
        </div>
        <div class="block-item">
            <label>Опубликован:</label><br />
            <?php echo htmler::booleanSelect($this->data->state, 'state'); ?>
        </div>
        <div class="block-item">
            <label>Описание:</label><br />
            <textarea class="redactor" name="description"><?php echo $this->data->description; ?></textarea>
        </div>
    </div>

    <div class="block">
        <div class="block-title">Дополнительно</div>
        <div class="block-item">
            <label>Логотип:</label><br />
            <div class="image-item">
                <?php if (!empty($this->data->logo)) : ?>
                    <img src="<?php echo $this->data->logo; ?>" alt="">
                <?php endif; ?><br />
                <input type="file" name="logo">    
            </div>
        </div>        
    </div>

    <div class="block">
        <div class="block-title">Пользователи</div>
        <div class="block-item">
            <ul>
                <?php foreach ($this->users as $user) : ?>
                    <li><?php echo $user->name; ?></li>   
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

	<input type="hidden" name="id" value="<?php echo $this->data->id; ?>">

    <div class="buttons">
    	<input type="submit" name="save" value="Сохранить">        
        <input type="submit" name="apply" value="Применить">
        <a href="/admin/realty/agencys" title="Закрыть">Закрыть</a>        
    </div>

</form>

