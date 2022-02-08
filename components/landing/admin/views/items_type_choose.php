<h2 class="content-title">Редактор блоков лендинга</h2>

<form method="get" action="/admin/landing/items/create" class="adminform" name="adminForm">

    <div class="block">
    	<div class="block-title">Выберите тип блока</div>
    	<div class="block-item">
            <label>Тип:</label><br />
            <select name="type">
                <?php foreach ($this->types as $type) : ?>
                    <option><?php echo $type; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="block-item">
            <input type="submit" value="Продолжить">
        </div>
    </div>

</form>

