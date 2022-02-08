<script type="text/javascript">
    $(document).ready(function(){
        $(".adminform .widget-select").change(function() {
            
            var val = $(this).val();

            val = "/admin/system/widgets/params/" + val;

            $('.wparams').load(val);

        });
        var val = $("#show_type").val();

        // SHOW TYPE DISPLAY

        if (val == 'all') {
            $('#show_list').hide();
        }

        $("#show_type").change(function() {

            var val = $(this).val();
            
            if (val == 'all') {
                $('#show_list').slideUp('fast');
            } else {
                $('#show_list').slideDown('fast');
            }
        });
    });
</script>

<div class="widget-edit" style="position:relative;">
    <h2 class="content-title">Редактор позиций виджетов</h2>

    <form method="post" action="/admin/system/widgets" class="adminform" name="adminForm" enctype="multipart/form-data">

        <div class="block" style="width:300px;">
            <div class="block-title">Основное</div>
            <div class="block-item">
                <label>Заголовок:</label><br />
                <input type="text" name="wpname" value="<?php echo $this->widget->title; ?>" required>
            </div>
            <div class="block-item">
                <label>Опубликован:</label><br />
                <?php echo htmler::booleanSelect($this->widget->state, 'state'); ?>
            </div>
            <div class="block-item">
                <label>Виджет:</label><br />                
                <select name="widget" class="widget-select" required> 
                    <option value="">- Выберите виджет -</option>
                    <?php foreach ($this->widgets as $key => $value) : ?>
                        <option value="<?php echo $value->name; ?>" <?php if ($value->name == $this->widget->widget) echo 'selected'; ?>><?php echo $value->name; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="block-item">
                <label>Позиция:</label><br />
                <input type="text" name="position" value="<?php echo $this->widget->position; ?>" required>
            </div>
        </div>

        <div class="block">
            <div class="block-title">Параметры виджета</div>
            <div class="block-item">
                <label>Показывать:</label><br />
                <select name="show_type" id="show_type">
                    <option value="all" <?php if ($this->widget->show_type == 'all') echo 'selected'; ?>>На всех страницах</option>
                    <option value="list" <?php if ($this->widget->show_type == 'list') echo 'selected'; ?>>На указанных страницах</option>
                    <option value="excl" <?php if ($this->widget->show_type == 'excl') echo 'selected'; ?>>Кроме указанных страниц</option>
                </select>
            </div>
            <div class="block-item" id="show_list">
                <label>Укажите ссылки на страницы через запятую:</label><br />
                <i>Пример: /content/blog/post/1, /content/page/item/2</i>
                <textarea name="show_list"><?php echo $this->widget->show_list; ?></textarea>
            </div>
            <div class="wparams">            
                <?php echo widgets::getParamsForm($this->widget->widget, $this->widget->params); ?>
            </div>
        </div>

        <input type="hidden" name="id" value="<?php echo $this->widget->id; ?>">
        
        <div class="buttons">
            <?php echo htmler::formButtons($this->buttons, 'adminForm'); ?>
        </div>

    </form>

</div>