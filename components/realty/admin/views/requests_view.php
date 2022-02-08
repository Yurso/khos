<script type="text/javascript">

    // function submitObject(id) {

    //     $.ajax({
    //         data: 'request_id=<?php echo $this->item->id; ?>&object_id='+id,
    //         type: 'POST',
    //         url: '/admin/realty/requests/select_object',                    
    //     }).done(function(result) {

    //         console.log(result);
            
    //         var obj = jQuery.parseJSON(result);

    //         console.log(obj);

    //         if (obj.success) {

    //             $("#item"+id).html("-");

    //             var line = $("#item"+id).parents("tr").html();        

    //             $("#item"+id).parents("tr").hide();

    //             $("#approvedObjectsBody").append("<tr>"+line+"</tr>");

    //         }

    //     });

    // }   

    // function searchObjects() {
        
    //     var data = $(".adminform").serialize();

    //     //console.log(data);

    //     $.ajax({
    //         data: data,
    //         type: 'POST',
    //         url: '/admin/realty/requests/object',                    
    //     }).done(function(result) {
            
    //         var obj = jQuery.parseJSON(result);
    //         var html = "";

    //         obj.forEach(function(item, i, arr) {
    //             html = html + "<tr>";
    //             html = html + "<td><span style=\"cursor:pointer;\" onclick=\"submitObject("+item.id+");\" id=\"item"+item.id+"\">+</span></td>";
    //             //html = html + "<td><img src=\"\" alt=\"\"></td>";                
    //             html = html + "<td style=\"text-align:left;\">";
    //             html = html + "<a class=\"open_ajax\" href=\"/admin/realty/view/"+item.id+"?theme=ajax\" target=\"_blank\">";
    //             html = html + item.adress;
    //             html = html + "</a>";
    //             html = html + "</td>";
    //             html = html + "<td>";
    //             html = html + item.total_area;
    //             html = html + "</td>";
    //             html = html + "<td>";
    //             html = html + item.price;
    //             html = html + "</td>";
    //             html = html + "</tr>";
    //         });
                        
    //         $(".results").html(html);
    //     });

    // }

    $(document).ready(function(){

        //$(".open_ajax").fancybox({type: 'ajax'});

        $(".open_ajax").fancybox({
            type: 'ajax',
            maxWidth: 740,
            helpers: {
                overlay: {
                    locked: false
                }
            }
        });

        $("#display_all_objects").click(function() {
            $("#approvedObjectsBody tr").slideDown();
            $(this).hide();
            return false;
        });

        var n = $(".item-others").length;
        if (n > 0) {
            $(".item-others").hide();
            $("#display_all_objects").html("Еще "+n+" "+declOfNum(n, ['объект', 'объекта', 'объектов'])+" "+declOfNum(n, ['подходит', 'подходят', 'подходят'])+" по параметрам");
        } else {
            $("#display_all_objects").hide();
        }

        $("#customer_information_button").click(function(){
            $("#customer_information").slideToggle("fast");
            return false;
        });
        //$("#customer_information").hide(); 

    });

</script>

<h2 class="content-title">Просмотр заявки</h2>

<form method="post" action="/admin/realty/requests" class="adminform realty-request-form" name="itemForm" enctype="multipart/form-data">

    <div class="block" style="width:600px; float: none;">
    	<div class="block-title">Основное</div>
    	<div class="block-item">
            <strong>Заголовок:</strong><br />
            <?php echo $this->item->title; ?>
        </div>
        <div class="dp33">
            <div class="block-item">
                <strong>Автор:</strong><br />
                <?php echo $this->item->user_name; ?>
            </div>
        </div>
        <div class="dp33">
            <div class="block-item">
                <strong>Дата создания:</strong><br />
                <?php echo htmler::esc_date($this->item->create_date); ?>
            </div>
        </div>
        <div class="dp33">
            <div class="block-item">
                <strong>Дата изменения:</strong><br />
                <?php echo htmler::esc_date($this->item->edit_date); ?>
            </div>
        </div>
        <div class="clr"></div>
        <div class="block-item">
            <strong>Описание:</strong><br />
            <?php echo $this->item->description; ?>
        </div>
        <div class="block-item">
            <strong>Приоритет:</strong><br />
            <?php echo $this->params['priority'][$this->item->priority]; ?>
        </div>
        <hr />
        <?php if ($this->item->access) :?>
            <div class="block-item">
                <strong>Клиент:</strong><br />
                <?php echo $this->item->customer_name; ?>
            </div>
            <div id="customer_information" style="display:none;">
                <div class="block-item">
                    <strong>Адрес:</strong><br />
                    <?php echo $this->item->customer_adress; ?>
                </div>
                <div class="dp50">
                    <div class="block-item">
                        <strong>Телефон:</strong><br />
                        <?php echo $this->item->customer_phone; ?>
                    </div>
                </div>
                <div class="dp50">
                    <div class="block-item">
                        <strong>E-mail:</strong><br />
                        <?php echo $this->item->customer_email; ?>
                    </div>
                </div>
            </div>            
            <div class="block-item">
                <a href="#" id="customer_information_button"><span class="small">Дополнительная информация</span></a>
            </div>
            <hr />
        <?php endif; ?>
        <div class="block-item">
            <strong>Ищу:</strong><br />
            <?php foreach ($this->categories as $category) {
                if ($category->id == $this->item->params['category_id']) {
                    echo $category->title; 
                }
            } ?>
        </div>
        <div class="dp50">
            <div class="block-item">
                <strong>По цене:</strong><br />
                от <?php echo htmler::esc_price($this->item->params['price_from']); ?>
                до <?php echo htmler::esc_price($this->item->params['price_to']); ?>
            </div>
        </div>
        <div class="dp50">
            <?php if (isset($this->item->params['area_to']) && $this->item->params['area_to'] > 0) : ?>
                <div class="block-item">
                    <strong>С площадью:</strong><br />
                    от <?php echo $this->item->params['area_from']; ?>
                    до <?php echo $this->item->params['area_to']; ?>
                    кв. м
                </div>
            <?php endif; ?>
        </div>
        <div class="clr"></div>
        <?php if (isset($this->item->params['floor'])) : ?>
            <div class="block-item">
                <strong>Этаж:</strong>
                <?php if (count($this->item->params['floor']) == count($this->params['floor'])) : ?>
                    <span>Любой</span>
                <?php else : ?>
                    <?php foreach ($this->item->params['floor'] as $floor) : ?>
                        <span><?php echo $floor; ?></span>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php /* if (count($this->selected)) : ?>
        <div style="width:640px;text-align: center;">
            <p><strong>Объекты для показа клиенту</strong></p>
            <table class="main-table" id="approvedObjects">
                <thead>
                    <tr>
                        <th style="width:20px;"></th>
                        <!-- <th style="width:100px;">Фото</th> -->
                        <th>Адрес</th>
                        <th style="width:40px;">Площадь</th>
                        <th style="width:100px;">Цена</th>
                    </tr>
                </thead>
                <tbody id="approvedObjectsBody">
                    <?php foreach ($this->selected as $object) : ?>
                        <tr>
                            <td>
                                <input type="checkbox" checked name="selected[]" value="<?php echo $object->object_id; ?>">
                                <input type="hidden" name="objects[]" value="<?php echo $object->object_id; ?>">
                            </td>
                            <td style="text-align:left;">
                                <a class="open_ajax" href="/admin/realty/view/<?php echo $object->object_id; ?>?theme=ajax">
                                    <?php echo $object->adress; ?>
                                </a>
                            </td>
                            <td><?php echo $object->total_area; ?></td>
                            <td><?php echo htmler::esc_price($object->price); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div style="width:640px;text-align: center;">
            <p style="color:red;"><strong>Есть новые объекты, подходящие под ваш запрос</strong></p>
            <table class="main-table" id="approvedObjects">
                <thead>
                    <tr>
                        <th style="width:20px;"></th>
                        <th style="width:100px;">Фото</th>
                        <th>Адрес</th>
                        <th style="width:40px;">Площадь</th>
                        <th style="width:100px;">Цена</th>
                    </tr>
                </thead>
                <tbody id="approvedObjectsBody">
                    <?php foreach ($this->objects as $object) : ?>
                        <?php if ($object->selected == null) : ?> 
                            <tr>
                                <td>
                                    <input type="checkbox" name="selected[]" value="<?php echo $object->object_id; ?>">
                                    <input type="hidden" name="objects[]" value="<?php echo $object->id; ?>">
                                </td>
                                <td>
                                    <?php if (!empty($object->image_name)) : ?>
                                        <img src="<?php echo $this->config->realty_images_path.'thumbs/'.$object->image_name; ?>" alt="" width="100">
                                    <?php endif; ?>
                                </td>
                                <td style="text-align:left;">
                                    <a class="open_ajax" href="/admin/realty/view/<?php echo $object->id; ?>?theme=ajax" target="_blank">
                                        <?php echo $object->adress; ?>
                                    </a>
                                </td>
                                <td><?php echo $object->total_area; ?></td>
                                <td><?php echo htmler::esc_price($object->price); ?></td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php endif; */ ?>
    <div style="width:640px;text-align:center;">
    <?php if (count($this->objects) && $this->selected_count == 0) : ?>
        <p>Выберите объекты для показа клиенту из списка ниже, а затем нажмите "Сохранить выбор"</p>
    <?php else : ?>
        <p style="font-size:12pt;"><strong>Объекты для показа клиенту</strong></p>
    <?php endif; ?>
    </div>

    <?php if (count($this->objects)) : ?>

        <div style="width:640px;text-align:center;">
            
            <table class="main-table request-objects-table" id="approvedObjects">
                <thead>
                    <tr>
                        <?php if ($this->item->access) : ?>
                            <th style="width:20px;"></th>
                        <?php endif; ?>                        
                        <th style="width:100px;">Фото</th>
                        <th>Адрес</th>
                        <th style="width:40px;">Площадь</th>
                        <th style="width:100px;">Цена</th>
                    </tr>
                </thead>
                <tbody id="approvedObjectsBody">
                    <?php foreach ($this->objects as $object) : ?>
                        <?php
                            $checked = '';
                            if ($object->selected == 1) {
                                $checked = ' checked';
                            }
                            $new = '';
                            if ($this->selected_count > 0 && $object->selected == 0) {
                                $new = '<span class="small" style="color:#008CBD;">Новый объект</span>';
                                $new_line = '';
                            }
                            $archive = '';
                            if ($object->selected == 1 && $object->archive == 1) {
                                $archive = '<span class="small" style="color:red;">Объект помещен в архив</span>';
                            }
                        ?>
                        <tr class="item <?php if ($this->selected_count > 0 && $object->selected < 1) echo 'item-others'; ?> <?php if ($object->selected == 0) echo 'item-new'; ?>">
                            <?php if ($this->item->access) : ?>
                                <td>                                    
                                    <input type="checkbox" name="checked[]" value="<?php echo $object->id; ?>" <?php echo $checked; ?>>
                                    <input type="hidden" name="objects[]" value="<?php echo $object->id; ?>">
                                </td>
                            <?php endif; ?>
                            <td>                                
                                <?php if (!empty($object->image_name)) : ?>
                                    <div class="request-objects-image">
                                        <img src="<?php echo $this->config->realty_images_path.'thumbs/'.$object->image_name; ?>" alt="" width="100">
                                    </div>
                                <?php endif; ?>
                            </td>                            
                            <td style="text-align:left;">
                                <div style="float:left;margin-right:5px;"><img src="<?php echo $object->agency_logo; ?>" alt="<?php echo $object->agency_name; ?>" title="<?php echo $object->agency_name; ?>" width="25"></div>
                                <div>                                    
                                    <a class="open_ajax" href="/admin/realty/objects/view/<?php echo $object->id; ?>?theme=ajax&amp;miniview=1" target="_blank">
                                        <?php echo $object->adress; ?>
                                    </a>                                    
                                </div>
                                <div>
                                    <span class="small"><?php echo htmler::esc_date($object->last_edit); ?></span>
                                </div>
                                <?php echo $new; ?>
                                <?php echo $archive; ?>
                            </td>
                            <td><?php echo $object->total_area; ?></td>
                            <td><?php echo htmler::esc_price($object->price); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php if ($this->item->access && $this->selected_count > 0) : ?>
                <p><a href="#" id="display_all_objects"></a></p>
            <?php endif; ?>
        </div>
    <?php else : ?>
        <p>Ни один объект не подошел по вашему запросу. Попробуйте изменить его параметры.</p>
    <?php endif; ?>

    <div style="width:640px;text-align:right;margin:20px 0;display:none;">
        <?php if ($this->item->access) : ?>
            <a href="#"><i class="fa fa-plus-circle" aria-hidden="true" style="color:green;"></i> Добавить другой объект</a>
        <?php else : ?>
            <a href="#"><i class="fa fa-plus-circle" aria-hidden="true" style="color:green;"></i> Предложить объект</a>
        <?php endif; ?>
    </div>

	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>">

    <div class="buttons">
        <?php if ($this->item->access) : ?>
            <a href="#" onclick="return submitForm(itemForm, '/save_objects');">Сохранить выбор</a>  
            <a href="/admin/realty/requests/edit/<?php echo $this->item->id; ?>" title="Закрыть">Редактировать запрос</a>
            <a href="#" onclick="return submitForm(itemForm, '/printlist', '_blank');">Печать</a>  
        <?php endif; ?>
        <a href="/admin/realty/requests" title="Закрыть">Закрыть</a>        
    </div>

</form>

