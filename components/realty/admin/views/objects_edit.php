 <?php 

if ($this->data->disabled)
    $disabled = " disabled ";
else 
    $disabled = "";

?>

<script src="/public/js/dropzone/dist/min/dropzone.min.js"></script>
<link rel="stylesheet" href="/public/js/dropzone/dist/min/dropzone.min.css" />    

<script type="text/javascript">

    function now_verbose(){
        return new Date().getTime();
    }

    $(document).ready(function(){       

        $( ".autocomplete" ).autocomplete({
            source: "/admin/realty/objects/autocomplite?field=agent_name",
            minLength: 1
        });

        $('.sortable').sortable({
            //handle: ".handle",
            cursor: "move",
            //axis: 'y',
            placeholder: "ui-state-highlight",
            update: function (event, ui) {
                var data = $(this).sortable('serialize');

                console.log(data);

                $.ajax({
                    data: data,
                    type: 'POST',
                    url: '/admin/realty/objects/images_sort',                    
                });            
            }
        });

        $(".images-list .delete").click(function(){
            
            var url = $(this).attr('href');

            $.ajax(url);

            $(this).parents("li").animate({ backgroundColor: "#fbc7c7" }, "fast")
            .animate({ opacity: "hide" }, 250);

            return false;

        });

        Dropzone.options.realtyImages = {
            
            success: function(file, response) { 

                //console.log(file);

                var obj = jQuery.parseJSON(response);

                if (obj.success) {
                    $(".uploaded-images").append('<input type="hidden" name="uploaded[]" value="'+obj.filename+'">');
                } else {
                    alert("Не удалось загрузить изображение. "+obj.message);
                    $(file.previewElement).remove();
                }       

            },
            dictDefaultMessage: "Для <strong>загрузки изображений</strong> нажмите здесь.<br />Либо перетащите файлы в эту область."         

        };

    });

</script>

<h2 class="content-title">Редактор объекта
    <?php if ($this->data->archive || $this->data->deleted) : ?>
        <a href="/admin/realty/objects/recover/<?php echo $this->data->id; ?>" style="color:red;font-size:12pt;">(Восстановить)</a>
    <?php endif; ?>
</h2>

<div class="buttons mobile-only">
    <?php if ($this->data->deleted) : ?>
        <a href="/admin/realty/objects/trash" class="btn-green" title="Закрыть">< Вернуться к списку объектов</a>     
    <?php elseif ($this->data->archive) : ?>   
        <a href="/admin/realty/objects/archive" class="btn-green" title="Закрыть">< Вернуться к списку объектов</a>     
    <?php else : ?>                        
        <a href="/admin/realty/objects" title="Закрыть" class="btn-green">< Вернуться к списку объектов</a>  
    <?php endif; ?>            
</div>

<form method="post" action="/admin/realty/objects/save" class="adminform" name="itemForm" enctype="multipart/form-data">

    <div class="vertical-blocks">

        <div class="block block-big">
        	<div class="block-title">Основное</div>
            <div class="block-item">
                <label>Адрес:</label><br />
                <?php echo htmler::inputText('adress', $this->data->adress, '', true, $this->data->disabled); ?>            
            </div>
            <div class="dp33">
                <div class="block-item">
                    <label>Опубликован:</label><br />
                    <?php echo htmler::booleanSelect($this->data->state, 'state', '', $this->data->disabled); ?>
                </div>
            </div>
            <div class="dp33">
                <div class="block-item">
                    <label>Категория:</label><br />
                    <select name="category_id" <?php echo $disabled; ?> required>
                        <?php foreach ($this->categories as $key => $category) : ?>
                            <option value="<?php echo $category->id; ?>" <?php if ($category->id == $this->data->category_id) echo 'selected'; ?>><?php echo $category->title; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="dp33">
                <div class="block-item">
                    <label>Агентство:</label><br />
                    <select name="agency_id" <?php if (User::getUserData('access_name') != 'administrator' && $this->data->id > 0) echo 'disabled' ?> required>
                        <?php foreach ($this->agencys as $key => $agency) : ?>
                            <option value="<?php echo $agency->id; ?>" <?php if ($agency->id == $this->data->agency_id) echo 'selected'; ?>><?php echo $agency->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="clr"></div>
            <hr />
            <div class="dp33">
                <div class="block-item">
                    <label>Тип дома:</label><br />
                    <?php echo htmler::SelectList($this->params['house_type'], 'house_type', '', '', $this->data->house_type, $this->data->disabled); ?>
                </div>
            </div>
            <div class="dp33">
                <div class="block-item">
                    <label>Этаж:</label><br />
                    <?php echo htmler::SelectList($this->params['floor'], 'floor', '', '', $this->data->floor, $this->data->disabled); ?>
                </div>
            </div> 
            <div class="dp33">
                <div class="block-item">
                    <label>Этажность:</label><br />
                    <?php echo htmler::SelectList($this->params['floors'], 'floors', '', '', $this->data->floors, $this->data->disabled); ?>
                </div>    
            </div>
            <div class="clr"></div>
            <div class="dp33">
                <div class="block-item">
                    <label>Общая площадь:</label><br />                
                    <?php echo htmler::inputText('total_area', $this->data->total_area, '', false, $this->data->disabled); ?>
                </div>
            </div>
            <div class="dp33">
                <div class="block-item">
                    <label>Жилая площадь:</label><br />                
                    <?php echo htmler::inputText('living_area', $this->data->living_area, '', false, $this->data->disabled); ?>
                </div>
            </div>
            <div class="dp33">
                <div class="block-item">
                    <label>Площадь кухни:</label><br />                
                    <?php echo htmler::inputText('kitchen_area', $this->data->kitchen_area, '', false, $this->data->disabled); ?>
                </div>
            </div>
            <div class="clr"></div>
            <div class="dp33">
                <div class="block-item">
                    <label>Санузел:</label><br />
                    <?php echo htmler::SelectList($this->params['wc_type'], 'wc_type', '', '', $this->data->wc_type, $this->data->disabled); ?>
                </div>
            </div>
            <div class="dp33">
                <div class="block-item">
                    <label>Лоджия:</label><br />                                
                    <?php echo htmler::SelectList($this->params['loggia_type'], 'loggia_type', '', '', $this->data->loggia_type, $this->data->disabled); ?>
                </div>
            </div>
            <div class="dp33">            
                <div class="block-item">
                    <label>Эксклюзив:</label><br />
                    <?php echo htmler::booleanSelect($this->data->param_exclusive, 'param_exclusive', '', $this->data->disabled); ?>
                </div>
            </div>
            <div class="clr"></div>
            <hr />
            <div class="dp33">
                <div class="block-item">
                    <label>Угловая:</label><br />
                    <?php echo htmler::booleanSelect($this->data->param_uglovaya, 'param_uglovaya', '', $this->data->disabled); ?>
                </div> 
            </div>
            <div class="dp33">  
                <div class="block-item">
                    <label>Трубы:</label><br />                            
                    <?php echo htmler::SelectList($this->params['param_pipes'], 'param_pipes', '', '', $this->data->param_pipes, $this->data->disabled); ?>
                </div>
            </div>
            <div class="dp33">
                <div class="block-item">
                    <label>Окна:</label><br />                            
                    <?php echo htmler::SelectList($this->params['param_windows'], 'param_windows', '', '', $this->data->param_windows, $this->data->disabled); ?>
                </div>
                </div>
            <div class="dp33">
                <div class="block-item">
                    <label>Полы:</label><br />                            
                    <?php echo htmler::SelectList($this->params['param_flooring'], 'param_flooring', '', '', $this->data->param_flooring, $this->data->disabled); ?>
                </div>
            </div>
            <div class="dp33">
                <div class="block-item">
                    <label>Входная дверь:</label><br />                            
                    <?php echo htmler::SelectList($this->params['param_main_door'], 'param_main_door', '', '', $this->data->param_main_door, $this->data->disabled); ?>
                </div>
            </div>
            <div class="dp33">
                <div class="block-item">
                    <label>Межкомнатные двери:</label><br />                            
                    <?php echo htmler::SelectList($this->params['param_room_doors'], 'param_room_doors', '', '', $this->data->param_room_doors, $this->data->disabled); ?>
                </div>
            </div>
            <div class="clr"></div>
            <hr />
            <div class="dp50">
                <div class="block-item">
                    <label>Статус/Правоустановка:</label><br />                
                    <?php echo htmler::inputText('rights', $this->data->rights, '', false, $this->data->disabled); ?>
                </div>
            </div>
            <div class="dp50">
                <div class="block-item">
                    <label>Альтернатива/Прямая продажа:</label><br />                
                    <?php echo htmler::inputText('type_of_deal', $this->data->type_of_deal, '', false, $this->data->disabled); ?>
                </div>
            </div>
            <div class="clr"></div>
            <hr />
            <div class="dp25">
                <div class="block-item">
                    <label>Цена:</label><br />                
                    <?php echo htmler::inputText('price', $this->data->price, '', false, $this->data->disabled); ?>
                </div>
            </div>
            <div class="dp25">
                <div class="block-item">
                    <label>Комиссия:</label><br />                
                    <?php echo htmler::inputText('commission', $this->data->commission, '', false, $this->data->disabled); ?>
                </div>
            </div>
            <div class="dp25">
                <div class="block-item">
                    <label>Агент:</label><br />
                    <input type="text" name="agent_name" <?php echo $disabled; ?> class="autocomplete" value="<?php echo $this->data->agent_name; ?>">
                </div>
            </div>
            <div class="dp25">
                <div class="block-item">
                    <label>Телефон:</label><br />
                    <input type="text" name="agent_phone" <?php echo $disabled; ?> value="<?php echo $this->data->agent_phone; ?>">
                </div>  
            </div>          
            <div class="clr"></div>
            <div class="block-item">
                <label>Дополнительная информация:</label><br />
                <textarea <?php echo $disabled; ?> name="comment"><?php echo $this->data->comment; ?></textarea>
            </div>
            <hr />
            <div class="dp33">
                <div class="block-item">
                    <label>Автор:</label><br />
                    <?php echo htmler::inputText('author_name', $this->data->author_name, '', false, true); ?>
                </div>
            </div>
            <div class="dp33">
                <div class="block-item">
                    <label>Дата создания:</label><br />
                    <?php echo htmler::inputText('create_date', $this->data->create_date, 'class="datepicker"', false, true); ?>
                </div>
            </div>
            <div class="dp33">
                <div class="block-item">
                    <label>Дата последнего изменения:</label><br />
                    <?php echo htmler::inputText('last_edit', $this->data->last_edit, 'class="datepicker"', false, true); ?>
                </div>
            </div>
            <div class="clr"></div>
        </div>
        
        <div class="block block-big">
            <div class="block-title">Изображения</div>        
            <div class="block-item">                
                <ul class="images-list sortable">                                                        
                    <?php foreach ($this->data->images as $image) : ?>                    
                        <li id="item-<?php echo $image->id; ?>">
                            <a class="fancybox" rel="images1" href="<?php echo $this->config->realty_images_path.$image->image_name; ?>">
                                <img src="<?php echo $this->config->realty_images_path; ?>thumbs/<?php echo $image->image_name; ?>" alt="<?php echo $image->image_name; ?>" />
                            </a>
                            <?php if (!$this->data->disabled) : ?>
                                <a class="delete" href="/admin/realty/objects/images_delete/<?php echo $image->id; ?>" title="Удалить"></a>
                            <?php endif; ?>
                        </li>                    
                    <?php endforeach; ?>    
                </ul>
                <div class="uploaded-images">
                </div>
            </div>
            <div class="clr" style="height:20px;"></div>
            <?php if (!$this->data->disabled) : ?>
                <div class="block-item">        
                    <!-- <input type="file" name="images[]" <?php echo $disabled; ?> multiple />             -->                    
                </div>
            <?php endif; ?>            
        </div>

    </div>     

	<input type="hidden" name="id" value="<?php echo $this->data->id; ?>">

    <div class="buttons">
        <?php if ($this->data->deleted) : ?>
            <a href="/admin/realty/objects" class=" btn-green">< Вернуться к списку объектов</a>     
        <?php elseif ($this->data->archive) : ?>     
            <input type="submit" name="apply" <?php echo $disabled; ?> value="Сохранить">
            <input type="submit" name="save" <?php echo $disabled; ?> value="Сохранить и закрыть"> 
            <a href="/admin/realty/objects/archive" class=" btn-green">< Вернуться к списку объектов</a>            
        <?php else : ?>
            <input type="submit" name="apply" <?php echo $disabled; ?> value="Сохранить">
            <input type="submit" name="save" <?php echo $disabled; ?> value="Сохранить и закрыть">             
            <a href="/admin/realty/objects" class=" btn-green">< Вернуться к списку объектов</a>
        <?php endif; ?>            
    </div>

</form>

<div class="clr"></div>

<form action="/admin/realty/objects/upload_image" class="dropzone" id="realtyImages"></form>
<p>Обязательно <strong>сохраните ваш объект</strong>, чтобы не потерять загруженные изображения</p>