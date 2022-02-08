<style>
    label {
        font-weight: bold;
    }
</style>

<script type="text/javascript">

    $(document).ready(function(){       

        // $( ".autocomplete" ).autocomplete({
        //     source: "/admin/realty/autocomplite?field=agent_name",
        //     minLength: 1
        // });

        // $('.sortable').sortable({
        //     //handle: ".handle",
        //     cursor: "move",
        //     //axis: 'y',
        //     placeholder: "ui-state-highlight",
        //     update: function (event, ui) {
        //         var data = $(this).sortable('serialаize');

        //         $.ajax({
        //             data: data,
        //             type: 'POST',
        //             url: '/admin/realty/images_sort',                    
        //         });            
        //     }
        // });

        // $(".images-list .delete").click(function(){
            
        //     var url = $(this).attr('href');

        //     $.ajax(url);

        //     $(this).parents("li").animate({ backgroundColor: "#fbc7c7" }, "fast")
        //     .animate({ opacity: "hide" }, 250);

        //     return false;

        // });

        //$(".comments").load('/comments/show/realty/<?php echo $this->data->id; ?>');

        $(".feedback_submit").click(function(){

            var text = $(".feedback_text").val();
            var id = $(".feedback_id").val()
                        
            $.ajax({
                data: 'id='+id+'&text='+text,
                type: 'POST',
                url: '/admin/realty/objects/feedback',                    
            }).done(function(result) {
               $(".feedback").html("<p>"+result+"</p>");
            });

            return false;

        });

        $('.fotorama').fotorama();

    });

</script>

<h2 class="content-title">Просмотр объекта
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
        <?php if (!$this->data->disabled) : ?>
            <a href="/admin/realty/objects/edit/<?php echo $this->data->id; ?>" class="btn-blue">Редактировать</a>
            <a href="/admin/realty/objects/archivate/<?php echo $this->data->id; ?>" class="btn-red">Поместить в архив</a>  
        <?php endif; ?>   
    <?php endif; ?>            
</div>

<div class="buttons-top mobile-hide">
    <?php if ($this->data->deleted == 0 && $this->data->archive == 0) : ?>   
        <a href="/admin/realty/objects/printview/<?php echo $this->data->id; ?>" target="blank">Печать</a> 
        <?php if (!$this->data->disabled) : ?>
            | <a href="/admin/realty/objects/edit/<?php echo $this->data->id; ?>">Редактировать</a> 
        <?php endif; ?>                             
    <?php endif; ?> 
</div>

<form method="post" action="/admin/realty/objects/save" class="adminform" name="itemForm" enctype="multipart/form-data">
    <div class="block-big-outer">
        <div class="block block-big">
        	<div class="block-title">Основное</div>
            <div class="block-item">
                <label>Адрес:</label><br />
                <?php echo $this->data->adress; ?>
            </div>
            <div class="dp33">
                <div class="block-item">
                    <label>Опубликован:</label><br />
                    <?php echo htmler::YesNo($this->data->state); ?>
                </div>
            </div>
            <div class="dp33">
                <div class="block-item">
                    <label>Категория:</label><br />
                    <?php echo $this->data->category_title; ?>
                </div>
            </div>
            <div class="dp33">
                <div class="block-item">
                    <label>Агентство:</label><br />
                    <?php echo $this->data->agency_name; ?>
                </div>
            </div>
            <hr />
            <div class="dp33">
                <div class="block-item">
                    <label>Тип дома:</label><br />
                    <?php echo $this->params['house_type'][$this->data->house_type]; ?>
                </div>
            </div>
            <div class="dp33">
                <div class="block-item">
                    <label>Этаж:</label><br />
                    <?php echo $this->params['floor'][$this->data->floor];  ?>
                </div>
            </div>
            <div class="dp33">
                <div class="block-item">
                    <label>Этажность:</label><br />
                    <?php echo $this->params['floors'][$this->data->floors];  ?>
                </div>
            </div>
            <div class="clr"></div> 
            <div class="dp33"> 
                <div class="block-item">
                    <label>Общая площадь:</label><br />                
                    <?php echo $this->data->total_area; ?>
                </div>
            </div>
            <div class="dp33">    
                <div class="block-item">
                    <label>Жилая площадь:</label><br />                
                    <?php echo $this->data->living_area; ?>                    
                </div>
            </div>
            <div class="dp33"> 
                <div class="block-item">
                    <label>Площадь кухни:</label><br /> 
                    <?php echo $this->data->kitchen_area; ?>                                    
                </div>
            </div>
            <div class="clr"></div>
            <div class="dp33"> 
                <div class="block-item">
                    <label>Санузел:</label><br />
                    <?php echo $this->params['wc_type'][$this->data->wc_type];  ?>                    
                </div>
            </div>
            <div class="dp33"> 
                <div class="block-item">
                    <label>Лоджия:</label><br />        
                    <?php echo $this->params['loggia_type'][$this->data->loggia_type]; ?>                                            
                </div> 
            </div>
            <div class="dp33">         
                <div class="block-item">
                    <label>Эксклюзив:</label><br />
                    <?php echo htmler::YesNo($this->data->param_exclusive); ?>                    
                </div>
            </div>
            <hr />
            <div class="dp33"> 
                <div class="block-item">
                    <label>Угловая:</label><br />
                    <?php echo htmler::YesNo($this->data->param_uglovaya); ?>                    
                </div> 
            </div>
            <div class="dp33"> 
                <div class="block-item">
                    <label>Трубы:</label><br />                            
                    <?php echo $this->params['param_pipes'][$this->data->param_pipes]; ?>                    
                </div>
            </div>
            <div class="dp33"> 
                <div class="block-item">
                    <label>Окна:</label><br />                                                
                    <?php echo $this->params['param_windows'][$this->data->param_windows]; ?>
                </div>
            </div>
            <div class="clr"></div>
            <div class="dp33"> 
                <div class="block-item">
                    <label>Полы:</label><br />                                                
                    <?php echo $this->params['param_flooring'][$this->data->param_flooring]; ?>
                </div>
            </div>
            <div class="dp33"> 
                <div class="block-item">
                    <label>Входная дверь:</label><br />                                                
                    <?php echo $this->params['param_main_door'][$this->data->param_main_door]; ?>
                </div>
            </div>
            <div class="dp33"> 
                <div class="block-item">
                    <label>Межкомнатные двери:</label><br />                                                
                    <?php echo $this->params['param_room_doors'][$this->data->param_room_doors]; ?>
                </div>
            </div>
            <hr />
            <div class="dp50">
                <div class="block-item">
                    <label>Статус/Правоустановка:</label><br />                
                    <?php echo $this->data->rights; ?>
                </div>
            </div>
            <div class="dp50">
                <div class="block-item">
                    <label>Альтернатива/Прямая продажа:</label><br />                                    
                    <?php echo $this->data->type_of_deal; ?>
                </div>
            </div>
            <hr />
            <div class="dp25">
                <div class="block-item">
                    <label>Цена:</label><br />                                    
                    <?php echo htmler::esc_price($this->data->price); ?>
                </div>
            </div>
            <div class="dp25">
                <div class="block-item">
                    <label>Комиссия:</label><br />                                    
                    <?php echo htmler::esc_price($this->data->commission); ?>
                </div>
            </div>
            <div class="dp25">
                <div class="block-item">
                    <label>Агент:</label><br />
                    <?php echo $this->data->agent_name; ?>
                </div>
            </div>
            <div class="dp25">
                <div class="block-item">
                    <label>Телефон:</label><br />
                    <?php echo $this->data->agent_phone; ?>
                </div> 
            </div>
            <div class="clr"></div>
            <div class="block-item">
                <label>Дополнительная информация:</label><br />
                <?php echo $this->data->comment; ?>
            </div>
            <hr />
            <div class="dp33">
                <div class="block-item">
                    <label>Автор:</label><br />
                    <?php echo $this->data->author_name; ?>
                </div>
            </div>
            <div class="dp33">
                <div class="block-item">
                    <label>Дата создания:</label><br />
                    <?php echo $this->data->create_date; ?>
                </div>
            </div>
            <div class="dp33">
                <div class="block-item">
                    <label>Дата последнего изменения:</label><br />
                    <?php echo $this->data->last_edit; ?>
                </div>
            </div>
            <div class="clr"></div>
        </div>
        
        <?php if (count($this->data->images)) : ?>        

            <div class="block block-big">
                <div class="block-title">Изображения</div>        
                <div class="block-item">                
                    
                    <div class="fotorama" data-nav="thumbs" data-width="100%" data-maxheight="500px">                                                        
                        <?php foreach ($this->data->images as $image) : ?>  
                            <a rel="images1" href="<?php echo $this->config->realty_images_path.$image->image_name; ?>">
                                <img src="<?php echo $this->config->realty_images_path.'thumbs/'.$image->image_name; ?>" alt="<?php echo $image->image_name; ?>" />
                            </a>
                        <?php endforeach; ?>    
                    </div>
                </div>
                <div class="clr" style="height:20px;"></div>
            </div>

            <?php /*
            <div class="block block-big">
                <div class="block-title">Изображения</div>        
                <div class="block-item">                
                    <ul class="images-list">                                                        
                        <?php foreach ($this->data->images as $image) : ?>                    
                            <li id="item-<?php echo $image->id; ?>">
                                <a class="fancybox" rel="images1" href="<?php echo $this->config->realty_images_path; ?><?php echo $image->image_name; ?>">
                                    <img src="<?php echo $this->config->realty_images_path; ?>thumbs/<?php echo $image->image_name; ?>" alt="<?php echo $image->image_name; ?>" />
                                </a>
                            </li>                    
                        <?php endforeach; ?>    
                    </ul>
                </div>
                <div class="clr" style="height:20px;"></div>
            </div>
            */ ?>

        <?php endif;?>
    </div>

    <?php if (!$this->miniview) : ?>  

        <?php if ($this->data->id > 0 && $this->data->author_id != User::getUserData('id')) : ?>
            <div class="block block-big mobile-hide">
                <div class="block-title">Задать вопрос автору</div>
                <div class="block-item">
                    <div class="feedback">
                        <p>Автору будет отправлено сообщение на электронную почту.</p><p>Ответ на сообщение придет на ваш электронный адрес.</p>
                        <label>Текст вопроса:</label>
                        <textarea name="feedback_text" class="feedback_text"></textarea>
                        <input type="hidden" name="feedback_id" class="feedback_id" value="<?php echo $this->data->id; ?>">
                        <p><input type="button" class="feedback_submit" value="Отправить"></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="clr"></div>

    	<input type="hidden" name="id" value="<?php echo $this->data->id; ?>">
    
        <div class="buttons mobile-only buttons-mobile">
            <?php if ($this->data->deleted) : ?>
                <a href="/admin/realty/objects/trash" class="btn-green" title="Закрыть">< Вернуться к списку объектов</a>     
            <?php elseif ($this->data->archive) : ?>   
                <a href="/admin/realty/objects/objects/archive" class="btn-green" title="Закрыть">< Вернуться к списку объектов</a>     
            <?php else : ?>
                <a href="/admin/realty/objects/printview/<?php echo $this->data->id; ?>" class="mobile-hide" target="blank">Печать</a> 
                <?php if (!$this->data->disabled) : ?>
                    <a href="/admin/realty/objects/edit/<?php echo $this->data->id; ?>" class="btn-blue">Редактировать</a> 
                <?php endif; ?>                        
                <a href="/admin/realty/objects" title="Закрыть" class="btn-green">< Вернуться к списку объектов</a>          
            <?php endif; ?>            
        </div>
        
    <?php endif; ?>

</form>


