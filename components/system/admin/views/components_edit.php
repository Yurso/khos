<h2 class="content-title">Редактор компонентов</h2>

<form method="post" action="/admin/system/components/save" class="adminform">

    <div class="block" style="width:300px;">
    	<div class="block-title">Основное</div>
    	<div class="block-item">
            <label>Заголовок:</label><br />
            <input type="text" name="itemname" value="<?php echo $this->item->name; ?>" disabled>
        </div>
        <div class="block-item">
            <label>Тип:</label><br />
            <input type="text" name="type" value="<?php echo $this->item->type; ?>" disabled>
        </div>
        <div class="block-item">
            <label>Опубликован:</label><br />
            <?php echo htmler::booleanSelect($this->item->state, 'state'); ?>
        </div>
    </div>

    <?php if ($this->item->type == 'component' && false) : ?>
        <div class="block" style="width:300px;">
            <div class="block-title">Дополнительно</div>
            <div class="block-item">
                <label>Доступ:</label><br />
                <?php echo htmler::SelectTree($this->users_access, 'access', 'id', 'name', $this->item->access); ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (count($this->item->params)) : ?>
	    <div class="block" style="width:300px;">
	        <div class="block-title">Параметры по-умолчанию</div>
	        <div class="wparams">
	            <?php foreach ($this->item->params as $key => $value) : ?>
	                <div class="block-item">
	                    <label><?php echo $key; ?></label><br />
	                    <input type="text" name="params[<?php echo $key; ?>]" value="<?php echo $value; ?>">
	                </div>
	            <?php endforeach; ?>
	        </div>
	    </div>
	<?php endif; ?>

    <?php if ($this->item->type == 'component') : ?>
        <div class="block" style="width:90%;background: transparent;">
            <div class="block-title">Контроллеры</div>
            <p>Новые контроллеры добавляются автоматически при открытии компонента</p>
            <table class="main-table" border="0">
                <thead>
                    <tr>
                        <th width="25"><input type="checkbox" onClick="toggle(this)"></th>
                        <th style="text-align:left;">Имя</th>
                        <th width="100">Опубликован</th>
                        <th width="100">Доступ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($this->controllers as $controller) : ?>
                        <tr>
                            <td><input type="checkbox" name="checked[]" value="<?php echo $controller->id; ?>"></td>
                            <td style="text-align:left;"><?php echo $controller->name; ?></td>
                            <td><?php echo htmler::booleanSelect($controller->state, "ctrl_state[$controller->id]"); ?></td>
                            <td><?php echo htmler::SelectTree($this->users_access, "ctrl_access[$controller->id]", 'id', 'name', $controller->access); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <!-- <p>
                <input type="button" value="Добавить">
                <input type="button" value="Удалить">
            </p> -->
        </div>
    <?php endif; ?>

	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>">

    <div class="buttons">
    	<input type="submit" value="Сохранить">
        <a href="/admin/system/components" title="Закрыть">Закрыть</a>
    </div>

</form>

