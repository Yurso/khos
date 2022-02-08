<h2 class="content-title">Редактор отчетов</h2>

<form method="post" action="/admin/tasks/reports/save" class="adminform" name="itemForm" enctype="multipart/form-data">

    <div class="block" style="width:800px;float:none;">
    	<div class="block-title">Основное</div>
    	<div class="block-item">
            <label>Заголовок:</label><br />
            <input type="text" name="title" value="<?php echo $this->item->title; ?>" required autofocus>
        </div>
        <div class="block-item">
            <label>Описание:</label><br />
            <textarea name="description"><?php echo $this->item->description; ?></textarea>
        </div>
        <div class="block-item">
            <label>Запрос:</label><br />
            <textarea name="query" id="query"><?php echo $this->item->query; ?></textarea>
        </div>      
    </div>

    <div class="block" style="width:800px;float:none;">
        <div class="block-title">Параметры отчета</div>
        <table class="report-params-table edit-table" cellspacing="0">
            <thead>
                <tr>          
                    <th width="25">#</th>
                    <th>Параметр</th>
                    <th>Алиас</th>
                    <th>Тип</th>
                    <th>По умлочанию</th>
                    <th></th>
                </tr>
            </thead>
            <tbody class="sortable ui-sortable">
                <?php $i = 0; foreach ($this->item->params['title'] as $key => $title) : $i++; ?>
                    <tr>
                        <td style="text-align:center;"></td>
                        <td class="et-input-cell"><input type="text" name="params[title][]" value="<?php echo $this->item->params['title'][$key]; ?>"></td>
                        <td class="et-input-cell"><input type="text" name="params[alias][]" value="<?php echo $this->item->params['alias'][$key]; ?>"></td>
                        <td class="et-input-cell"><input type="text" name="params[type][]" value="<?php echo $this->item->params['type'][$key]; ?>"></td>
                        <td class="et-input-cell"><input type="text" name="params[default][]" value="<?php echo $this->item->params['default'][$key]; ?>"></td>
                        <td style="text-align:center;"><i class="far fa-trash-alt" style="color:red;cursor: pointer;"></i></td>                      
                    </tr>
                <?php endforeach; ?>              
            </tbody>
        </table>        
        <div class="table-buttons">
            <input type="button" id="reports-params-table-add" value="Добавить">
            <input type="button" id="reports-params-table-remove" value="Удалить">
        </div>
        <script id="reports-params-table-template" type="text/x-custom-template">
            <tr>
                <td style="text-align:center;"></td>
                <td class="et-input-cell"><input type="text" name="params[title][]" value=""></td>
                <td class="et-input-cell"><input type="text" name="params[alias][]" value=""></td>
                <td class="et-input-cell"><input type="text" name="params[type][]" value=""></td>
                <td class="et-input-cell"><input type="text" name="params[default][]" value=""></td>                       
                <td style="text-align:center;"><i class="far fa-trash-alt" style="color:red;cursor: pointer;"></i></td>                       
            </tr>
        </script>
   </div>

    <div class="block" style="width:800px;float:none;">
        <div class="block-title">Параметры ячеек</div>
        <table class="report-columns-table edit-table" cellspacing="0">
            <thead>
                <tr>          
                    <th width="25">#</th>
                    <th>Заголовок</th>
                    <th>Алиас</th>
                    <th>Стиль шапки</th>
                    <th>Стиль ячейки</th>
                    <th></th>
                </tr>
            </thead>
            <tbody class="sortable ui-sortable">
                <?php $i = 0; foreach ($this->item->columns['title'] as $key => $title) : $i++; ?>
                    <tr>
                        <td style="text-align:center;"></td>
                        <td class="et-input-cell"><input type="text" name="columns[title][]" value="<?php echo $this->item->columns['title'][$key]; ?>"></td>
                        <td class="et-input-cell"><input type="text" name="columns[alias][]" value="<?php echo $this->item->columns['alias'][$key]; ?>"></td>
                        <td class="et-input-cell"><input type="text" name="columns[th_style][]" value="<?php echo $this->item->columns['th_style'][$key]; ?>"></td>
                        <td class="et-input-cell"><input type="text" name="columns[td_style][]" value="<?php echo $this->item->columns['td_style'][$key]; ?>"></td>
                        <td style="text-align:center;"><i class="far fa-trash-alt" style="color:red;cursor: pointer;"></i></td>                      
                    </tr>
                <?php endforeach; ?>              
            </tbody>
        </table>        
        <div class="table-buttons">
            <input type="button" id="reports-columns-table-add" value="Добавить">
            <input type="button" id="reports-columns-table-remove" value="Удалить">
        </div>
        <script id="reports-columns-table-template" type="text/x-custom-template">
            <tr>
                <td style="text-align:center;"></td>
                <td class="et-input-cell"><input type="text" name="columns[title][]" value=""></td>
                <td class="et-input-cell"><input type="text" name="columns[alias][]" value=""></td>
                <td class="et-input-cell"><input type="text" name="columns[th_style][]" value=""></td>
                <td class="et-input-cell"><input type="text" name="columns[td_style][]" value=""></td>                       
                <td style="text-align:center;"><i class="far fa-trash-alt" style="color:red;cursor: pointer;"></i></td>                       
            </tr>
        </script>
   </div>

	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>">

    <div class="buttons">
        <input type="submit" name="save" value="Сохранить">        
        <input type="submit" name="apply" value="Применить">
        <a href="/admin/tasks/reports" title="Закрыть">Закрыть</a>        
    </div>
    
</form>

<!-- Create a simple CodeMirror instance -->
<link rel="stylesheet" href="/public/js/codemirror-5.25.0/lib/codemirror.css">
<script src="/public/js/codemirror-5.25.0/lib/codemirror.js"></script>
<script src="/public/js/codemirror-5.25.0/addon/selection/selection-pointer.js"></script>
<script src="/public/js/codemirror-5.25.0/mode/sql/sql.js"></script>

<script type="text/javascript">

    $(function(){   

        var editor = CodeMirror.fromTextArea(document.getElementById("query"), {
            mode: 'text/x-mysql',
            lineNumbers: true,
            selectionPointer: true
        });

        $('.sortable').sortable({
            handle: ".handle",
            cursor: "move",
            axis: 'y',
            placeholder: "ui-state-highlight",
        });

        var params_template = $('#reports-params-table-template').html();
        var columns_template = $('#reports-columns-table-template').html();

        $('#reports-params-table-add').click(function(){            
            $('.report-params-table tbody').append(params_template);
        });
        
        $('#reports-columns-table-add').click(function(){            
            $('.report-columns-table tbody').append(columns_template);
        });

        $('#reports-columns-table-remove').click(function(){

            console.log('click');

            $('.report-columns-table td .et-row-checkbox').each(function(){
                if ($(this).prop('checked') == true) {
                    console.log($(this));
                }
            });

        });

    });

</script>

