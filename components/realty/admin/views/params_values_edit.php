<h2 class="content-title">Редактор значения параметра</h2>

<form method="post" action="/admin/realty/params_values/save" class="adminform" name="itemForm">

    <div class="block" style="width:980px;">
    	<div class="block-title">Основное</div>
        <div class="block-item">
            <label>Параметр:</label><br />
            <select name="param_id">                
                <?php foreach ($this->params as $key => $param) : ?>
                    <option value="<?php echo $param->id; ?>" <?php if ($param->id == $this->data->param_id) echo 'selected'; ?>><?php echo $param->label; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    	<div class="block-item">
            <label>Заголовок:</label><br />
            <input type="text" name="title" value="<?php echo $this->data->title; ?>" required autofocus>
        </div>
        <div class="block-item">
            <label>Значение:</label><br />
            <input type="text" name="value" value="<?php echo $this->data->value; ?>" required>
        </div>
        <div class="block-item">
            <label>Порядок:</label><br />
            <input type="text" name="ordering" value="<?php echo $this->data->ordering; ?>" required>
        </div>
        <div class="block-item">
            <label>Опубликован:</label><br />
            <?php echo htmler::booleanSelect($this->data->state, 'state'); ?>
        </div>
    </div>

	<input type="hidden" name="id" value="<?php echo $this->data->id; ?>">

    <div class="buttons">
    	<input type="submit" name="save" value="Сохранить">        
        <input type="submit" name="apply" value="Применить">
        <a href="/admin/realty/params_values" title="Закрыть">Закрыть</a>        
    </div>

</form>

