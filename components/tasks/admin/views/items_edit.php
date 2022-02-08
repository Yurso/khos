<h2 class="content-title"><?php echo $this->tmpl_page_title; ?></h2>

<form method="post" action="/admin/tasks/items/save" class="adminform tasks" name="itemForm" id="itemForm" enctype="multipart/form-data">
    
    <div style="width:600px;float:left;margin-right:20px;">
        <div class="block" style="width:100%;float:none;">
        	<div class="block-title">Основное</div>     
            <div class="block-item">
                <label>Заголовок:</label><br />
                <input type="text" name="title" id="title" value="<?php echo $this->item->title; ?>" required autofocus>
            </div>
            <div class="block-item">
                <label>Вид работ:</label><br />
                <select name="type_id" id="type_id" required>
                    <?php foreach ($this->types as $type) : ?>
                        <option value="<?php echo $type->id; ?>" <?php if ($type->id == $this->item->type_id) echo 'selected'; ?>><?php echo $type->title; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="block-item">
                <label>Клиент:</label><br />
                <select name="customer_id" id="customer_id" required>
                    <?php foreach ($this->customers as $customer) : ?>
                        <option value="<?php echo $customer->id; ?>" <?php if ($customer->id == $this->item->customer_id) echo 'selected'; ?>><?php echo $customer->name; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="block-item">
                <label>Проект:</label><br />
                <input type="hidden" id="selected_project_id" value="<?php echo $this->item->project_id; ?>">
                <select name="project_id" id="project_id"></select>
            </div>
            <div class="block-item">
                <label>Ссылка:</label><br />
                <input type="text" name="url" id="url" value="<?php echo $this->item->url; ?>">
            </div>
            <div class="block-item">
                <label>Комментарий:</label><br />
                <input type="text" name="comment" value="<?php echo $this->item->comment; ?>">
            </div>
            <div class="dp30">   
                <div class="block-item">
                    <label>Количество:</label><br />
                    <input type="number" name="count" value="<?php echo $this->item->count; ?>">
                </div>
            </div>
            <div class="dp70">   
                <div class="block-item">
                    <label>Цена:</label><br />
                    <input type="text" name="price" id="price" value="<?php echo $this->item->price; ?>">
                </div>
            </div>
            <div class="clr"></div>
            <div class="block-item">
                <label>Описание задачи:</label><br />
                <textarea name="description" id="description" style="height:200px;"><?php echo $this->item->description; ?></textarea>
            </div>
            <?php if (!empty($this->item->message_msgno)) : ?>
                <div class="block-item">          
                    <p>
                        <input type="checkbox" id="message_unflag" name="message_unflag">
                        <label for="message_unflag">Снять флаг и пометить письмо как прочитанное</label>
                    </p>
                    <p><input type="checkbox" id="autoanswer_create" name="autoanswer_create"><label for="autoanswer_create">Создать автоответ</label></p>
                </div>
                <div id="autoanswer" style="display: none;">
                    <div class="block-item">
                        <label>Ответ:</label><br />
                        <textarea name="autoanswer" id="autoanswer_textaerea" style="height:200px;"></textarea>                
                    </div>   
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div style="width:300px;float:left;margin-right:20px;">
        <div class="block" style="width:100%;">
            <div class="block-title">Дополнительно</div>
            <div class="block-item">
                <label>Дата задачи:</label><br />
                <input type="text" name="date" class="datepicker" value="<?php echo $this->item->date; ?>">
            </div>
            <div class="block-item">
                <label>Автор:</label><br />
                <input type="text" name="author_name" value="<?php echo htmlspecialchars($this->item->author_name); ?>">
            </div>
            <div class="block-item">
                <label>Статус:</label><br />
                <?php echo htmler::SelectList($this->statuses, 'status', 'status', '', $this->item->status); ?>
            </div>    
            <?php if ($this->item->status == 'complete' || $this->item->status == 'paid') : ?>
                <div class="block-item">
                    <label>Дата выполнения:</label><br />
                    <input type="text" name="complete_date" class="datepicker" value="<?php echo $this->item->complete_date; ?>">
                </div>
            <?php endif; ?>
            <?php if ($this->item->status == 'paid') : ?>
                <div class="block-item">
                    <label>Дата оплаты:</label><br />
                    <input type="text" name="paid_date" id="paid_date" class="datepicker" value="<?php echo $this->item->paid_date; ?>">
                </div> 
            <?php endif; ?>  
        </div>
        
        <div class="block attachments" style="width:100%;">
            <div class="block-title">Файлы</div>
            <div class="block-item">
                <?php if (isset($this->item->files) && count($this->item->files)) : ?>
                    <?php foreach ($this->item->files as $file) : ?>
                        <div class="attachment">                            
                            <a href="/<?php echo $this->item->filespath.$file->filename; ?>" download=""><?php echo $file->filename; ?></a>                            
                        </div>                      
                    <?php endforeach; ?>
                    <?php if (count($this->item->files) > 1) : ?>
                        <p><a href="/admin/tasks/items/download_all_files/<?php echo $this->item->id; ?>" target="_blank"><span class="small"><i class="fas fa-download"></i> Скачать все файлы</span></a></p>
                    <?php endif; ?>
                <?php else : ?>
                    <p>Список файлов пуст</p>
                <?php endif; ?>
            </div> 
            <hr />
            <div class="block-item">
                <input type="file" name="attachments[]" multiple="multiple">
            </div>
        </div>
    
    </div>
    	
    <input type="hidden" name="id" id="id" value="<?php echo $this->item->id; ?>">
    <input type="hidden" name="ref" id="ref" value="<?php echo isset($_GET['ref']) ? $_GET['ref'] : ''; ?>">

    <div class="buttons">
    	<input type="submit" name="save_complete" value="Выполнено" <?php echo ($this->item->status != 'new') ? 'disabled' : ''; ?>>        
        <input type="submit" name="save_paid" value="Оплачено" <?php echo ($this->item->status == 'paid') ? 'disabled' : ''; ?>>                
        <input type="submit" name="save_delete" value="Отменить">                
        <input type="submit" name="apply" value="Применить">
        <?php if (isset($_GET['ref']) && !empty($_GET['ref'])) : ?>
            <a href="<?php echo $_GET['ref']; ?>" title="Закрыть">Закрыть</a>        
        <?php else : ?>
            <a href="/admin/tasks/items" title="Закрыть">Закрыть</a>        
        <?php endif; ?>
    </div>

</form>   

<script type="text/javascript">

    $(document).ready(function(){   

        var types_prices = [];
        var id = $("#id").val();

        <?php foreach ($this->types as $type) : ?>
            types_prices[<?php echo $type->id; ?>] = <?php echo $type->default_price; ?>;
        <?php endforeach; ?>

        $( "#type_id" ).change(function() {  

            var type_id = $(this).val();

            $("#price").val(types_prices[type_id]);

            if (types_prices[type_id] == 0) {
                $(".paid").val(1);
            }

        });

    });

</script>

