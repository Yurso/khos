<h2 class="content-title">Фыбор формата экспорта</h2>

<form method="get" action="/admin/tasks/export/bill" class="adminform" name="itemForm" enctype="multipart/form-data">
    <div class="block-item"> 
        <label>Выберите формат:</label><br />
        <select name="format">
            <?php foreach ($this->formats as $format) : ?>
                <option value="<?php echo $format; ?>"><?php echo $format; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <p>    
        <input type="hidden" name="bill_id" value="<?php echo $this->bill_id; ?>">
        <input type="submit" value="Продолжить">
    </p>
</form>

            