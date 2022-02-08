<h2 class="content-title">Редактор алиасов</h2>

<form method="post" action="/admin/system/aliases/save" class="adminform" enctype="multipart/form-data">

    <div class="block" style="width:300px;">
        <div class="block-title">Основное</div>
        <div class="block-item">
            <label>Алиас:</label><br />
            <input type="text" name="alias" value="<?php echo $this->item->alias; ?>">
        </div>
        <div class="block-item">
            <label>Ссылка:</label><br />
            <input type="text" name="url" value="<?php echo $this->item->url; ?>" required>
        </div>
    </div>

    <input type="hidden" name="id" value="<?php echo $this->item->id; ?>">
    
    <div class="buttons">
        <input type="submit" name="save" value="Сохранить">        
        <input type="submit" name="apply" value="Применить">
        <a href="/admin/system/aliases" title="Закрыть">Закрыть</a>
    </div>

</form>