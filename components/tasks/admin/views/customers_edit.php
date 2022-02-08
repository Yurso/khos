<script type="text/javascript">
   function close_window() {
      if (confirm("Close Window?")) {
        close();
      }
    }
</script>

<h2 class="content-title">Редактор клиентов</h2>

<form method="post" action="/admin/tasks/customers/save" class="adminform" name="itemForm" enctype="multipart/form-data">

    <div class="block">
    	<div class="block-title">Основное</div>
    	<div class="block-item">
            <label>Имя:</label><br />
            <input type="text" name="name" value="<?php echo $this->item->name; ?>" required autofocus>
        </div>
        <div class="block-item">
            <label>Адрес:</label><br />
            <input type="text" name="adress" value="<?php echo $this->item->adress; ?>">
        </div>
        <div class="block-item">
            <label>Телефон:</label><br />
            <input type="text" name="phone" value="<?php echo $this->item->phone; ?>">
        </div>
        <div class="block-item">
            <label>E-mail:</label><br />
            <input type="text" name="email" value="<?php echo $this->item->email; ?>">
        </div>
        <div class="block-item">
            <label>Группа:</label><br />
            <input type="text" name="group_name" value="<?php echo $this->item->group_name; ?>">
        </div>       
    </div>

    <div class="block" style="width:500px;">
        <div class="block-title">Адреса для автозагрузки</div>
        <table class="emails-table edit-table" cellspacing="0">
            <thead>
                <tr>
                    <th>E-mail</th>            
                    <th width="100">Проект</th>
                    <th width="300">Комментарий</th>
                    <th width="100">Активно</th>
                    <th width="25"></th>
                </tr>
            </thead>
            <tbody class="sortable ui-sortable">
                <?php foreach ($this->item->emails as $item) : ?>
                    <tr>
                        <td class="et-input-cell">
                            <input type="text" name="emails[email][]" value="<?php echo $item->email; ?>">
                        </td>
                        <td class="et-input-cell">
                            <select name="emails[project_id][]">
                                <option>-</option>
                                <?php foreach ($this->item->projects as $key => $project) : ?>
                                    <option value="<?php echo $project->id; ?>" <?php $project->id == $item->project_id ? 'selected' : ''; ?>><?php echo $project->title; ?></option>
                                <?php endforeach; ?>
                            </select>                            
                        </td>  
                        <td class="et-input-cell">
                            <input type="text" name="emails[description][]" value="<?php echo $item->description; ?>">
                        </td>
                        <td class="et-input-cell" style="text-align:center;">
                            <?php echo htmler::booleanSelect($item->state, 'emails[state][]'); ?>
                        </td>          
                        <td class="et-input-cell delete-row" style="text-align:center;">                            
                            <i class="far fa-trash-alt" style="color:red;cursor: pointer;"></i>
                        </td>                    
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="table-buttons">
            <input type="button" id="emails-table-add" value="Добавить">
        </div>

        <script type="text/javascript">
        
            $(document).ready(function(){  

                $('.sortable').sortable({
                    handle: ".handle",
                    cursor: "move",
                    axis: 'y',
                    placeholder: "ui-state-highlight",
                });

                $('#emails-table-add').click(function(){
                    
                    var new_row = '<tr>';                    
                        new_row += '    <td class="et-input-cell"><input type="text" name="emails[email][]" value=""></td>';
                        new_row += '    <td class="et-input-cell"><input type="text" name="emails[project_id][]" value=""></td>';
                        new_row += '    <td class="et-input-cell"><input type="text" name="emails[description][]" value=""></td>';
                        new_row += '    <td class="et-input-cell" style="text-align:center;"><select name="emails[state][]" class="booleanSelect"><option value="1" selected="">Да</option><option value="0">Нет</option></select></td>';
                        new_row += '    <td class="et-input-cell" style="text-align:center;"><i class="far fa-trash-alt" style="color:red;cursor: pointer;"></i></td>';
                        new_row += '</tr>';
                    
                    $('.emails-table tbody').append(new_row);

                    $('.emails-table .delete-row').click(function(){
                        $(this).parent('tr').remove();
                    });

                });

                $('.emails-table .delete-row').click(function(){
                    $(this).parent('tr').remove();
                });

            });

        </script>

    </div>

	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>">

    <div class="buttons">
        <input type="submit" name="save" value="Сохранить">        
        <input type="submit" name="apply" value="Применить">
        <a href="/admin/tasks/customers" title="Закрыть">Закрыть</a>        
    </div>
    
</form>

