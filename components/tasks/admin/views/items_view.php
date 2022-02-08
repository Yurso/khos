<div class="tasks-item-view">

    <h2 class="content-title"><?php echo $this->tmpl_page_title; ?></h2>

    <form method="post" action="/admin/tasks/items/save" class="adminform" name="itemForm" id="itemForm" enctype="multipart/form-data">
        
        <div style="width:600px;float:left;margin-right:20px;">
       
            <div class="tasks-messages"></div>

            <div class="block" style="width:100%;float:none;margin-top:20px;">
                <div class="block-title">Новое сообщение</div>
                <div class="block-item">
                    <label>Текст:</label>
                    <textarea name="message-text" id="message-text"></textarea>
                </div> 
                <div class="block-item">
                    <input type="file" name="attachments[]" multiple="multiple">
                </div>
                <div class="block-item"> 
                    <?php if (!empty($this->item->message_msgno)) : ?>                   
                        <div style="float:left;font-size:11px;">
                            <input type="checkbox" id="message-unflag" <?php if ($this->item->status == 'new') echo 'checked'; ?>><label for="message-unflag">Снять флаги</label>
                            <input type="checkbox" id="message-reply2email" ><label for="message-reply2email">Ответить по почте</label>
                        </div>
                    <?php endif; ?>
                    <div style="float:right;">                        
                        <i class="fas fa-spinner fa-spin message-send-spinner" style="display:none;"></i> <input type="button" id="message-send" value="Отправить" />
                    </div>
                </div>
                <div class="clr"></div>
            </div>

        </div>
        
        <div style="width:300px;float:left;margin-right:20px;">
            <div class="block" style="width:100%;">
                <div class="block-title">Дополнительно</div>
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
                    <select name="customer_id" required>
                        <?php foreach ($this->customers as $customer) : ?>
                            <option value="<?php echo $customer->id; ?>" <?php if ($customer->id == $this->item->customer_id) echo 'selected'; ?>><?php echo $customer->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>  
                <div class="block-item">
                    <label>Цена:</label><br />
                    <input type="text" name="price" id="price" value="<?php echo $this->item->price; ?>">
                </div>       
                <div class="block-item">
                    <label>Дата задачи:</label><br />
                    <input type="text" name="date" class="datepicker" value="<?php echo $this->item->date; ?>">
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
                <?php /* 
                <div class="block-item">
                    <label>Автор:</label><br />
                    <input type="text" name="author_name" value="<?php echo $this->item->author_name; ?>">
                </div>
                <div class="block-item">
                    <label>Комментарий:</label><br />
                    <input type="text" name="comment" value="<?php echo $this->item->comment; ?>">
                </div>
                */ ?>    
            </div>

            <div class="block attachments" style="width:100%;">
                <div class="block-title">Файлы</div>
                <div class="block-item">
                    <?php if (count($this->item->files)) : ?>
                        <?php foreach ($this->item->files as $file) : ?>
                            <div class="attachment">                            
                                <a href="/<?php echo $this->item->filespath.$file->filename; ?>" download=""><?php echo $file->filename; ?></a>                            
                            </div>                      
                        <?php endforeach; ?>
                        <p><a href="/admin/tasks/items/download_all_files/<?php echo $this->item->id; ?>" target="_blank">
                            <span class="small"><i class="fas fa-download"></i> Скачать все файлы</span></a></p>
                    <?php else : ?>
                        <p>Список файлов пуст</p>
                    <?php endif; ?>
                </div>
            </div>
        
        </div>
        	
        <input type="hidden" name="id" id="id" value="<?php echo $this->item->id; ?>">
        <input type="hidden" name="ref" id="ref" value="<?php echo isset($_GET['ref']) ? $_GET['ref'] : ''; ?>">

        <div class="buttons">
            <a href="/admin/tasks/items/edit/<?php echo $this->item->id; ?>">Редактировать</a>             
            <a href="<?php echo (isset($_GET['ref']) && !empty($_GET['ref'])) ? $_GET['ref'] : '/admin/tasks/items'; ?>">Закрыть</a>     
        </div>

    </form>

</div>

<script type="text/javascript" src="/public/js/com_tasks/items_view.js"></script>  

