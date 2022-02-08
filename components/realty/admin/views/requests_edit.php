<script type="text/javascript">

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

        $(".open_ajax").fancybox({type: 'ajax'});

        $( ".btn-search-objects" ).click(function() {    
            searchObjects();
        });

        $( ".item" ).click(function() {    
            $(this).hide();
            console.log("ok");
        });

        $( "#params_floor_select_all" ).click(function() {  

            $(".params_floor").prop( "checked", true );

            return false;

        });

        $( "#params_floor_select_middle" ).click(function() {  

            $(".params_floor").prop( "checked", true );
            $("#params_floor1").prop( "checked", false );
            $("#params_floor16").prop( "checked", false );

            return false;

        });

        $( "#params_floor_select_none" ).click(function() {  

            $(".params_floor").prop( "checked", false );

            return false;

        });

        $( "#add_new_customer" ).click(function() {  

            $( "#customer_id" ).val("0");
            $( "#customer_name" ).val("");
            $( "#customer_adress" ).val("");
            $( "#customer_phone" ).val("");
            $( "#customer_email" ).val("");
            $( "#customer_flag" ).val("new");

            $( "#customer_name" ).prop( "disabled", false );
            $( "#customer_adress" ).prop( "disabled", false );
            $( "#customer_phone" ).prop( "disabled", false );
            $( "#customer_email" ).prop( "disabled", false );

            $( "#customer_info" ).slideDown();

        });

        $( "#edit_customer" ).click(function() {  

            var id = $( "#customer_id" ).val();

            if (id > 0) {
                $( "#customer_flag" ).val("edit");

                $( "#customer_adress" ).prop( "disabled", false );
                $( "#customer_phone" ).prop( "disabled", false );
                $( "#customer_email" ).prop( "disabled", false );

                $( "#customer_info" ).slideDown();
            }

        });

        $( "#cancel_edit_customer" ).click(function() {  

            $( "#customer_flag" ).val("");

            $( "#customer_adress" ).prop( "disabled", true );
            $( "#customer_phone" ).prop( "disabled", true );
            $( "#customer_email" ).prop( "disabled", true );

            $( "#customer_info" ).slideUp();

        });

        $( "#params_show0" ).change(function() {    
            
            var val = $(this).prop( "checked" );

            $(".params_show").prop( "checked", val );

        });

        $( ".params_show:not(#params_show0)" ).change(function() {   

            $("#params_show0").prop( "checked", false );

        }); 

        $( "#customer_name" ).autocomplete({
            source: "/admin/realty/customers/autocomplite",
            minLength: 1,
            select: function( event, ui ) {
                
                // console.log(event);
                // console.log(ui);

                $( "#customer_id" ).val(ui.item.id);
                $( "#customer_name" ).val(ui.item.name);
                $( "#customer_adress" ).val(ui.item.adress);
                $( "#customer_phone" ).val(ui.item.phone);
                $( "#customer_email" ).val(ui.item.email);

                return false;
            }
        }); 

         // Hide floor inputs if it house or same
        $( "#params_category_id" ).change(function() {    
            
            var val = $(this).val();

            if (val == 2 || val == 3 || val == 4 || val == 12) {
                $(".block-item-floor").slideDown("fast");
                $(".params_floor").prop( "disabled", false );
            } else {
                $(".block-item-floor").slideUp("fast");
                $(".params_floor").prop( "disabled", true );
            }

        }); 
        
        var val = $( "#params_category_id" ).val();

        if (val == 2 || val == 3 || val == 4 || val == 12) {
            $(".block-item-floor").show();
            $(".params_floor").prop( "disabled", false );
        } else {
            $(".block-item-floor").hide();
            $(".params_floor").prop( "disabled", true );
        }

    });

</script>

<h2 class="content-title">Редактор заявки</h2>

<form method="post" action="/admin/realty/requests/save" class="adminform realty-request-form" name="itemForm" enctype="multipart/form-data">

    <div class="block" style="width:600px; float: none;">
    	<div class="block-title">Основное</div>
    	<div class="block-item">
            <label>Заголовок:</label><br />
            <input type="text" name="title" value="<?php echo $this->item->title; ?>" required autofocus>
        </div>
        <?php if ($this->item->id > 0) : ?>
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
        <?php endif; ?>
        <div class="block-item">
            <label>Описание:</label><br />
            <textarea name="description"><?php echo $this->item->description; ?></textarea>
        </div>
        <div class="block-item">
            <label>Приоритет:</label><br />
            <?php echo htmler::radioList($this->params['priority'], 'priority', $this->item->priority); ?>
        </div>
        <hr />
        <div class="block-item">
            <label>Клиент <span class="small">(начните вводить имя клиента для выбора из списка)</span>:</label><br />
            <input type="hidden" name="customer_id" id="customer_id" value="<?php echo $this->item->customer_id; ?>">
            <input type="hidden" name="customer_flag" id="customer_flag" value="">
            <input type="text" name="customer_name" id="customer_name" value="<?php echo $this->item->customer_name; ?>">
            <div>
                <span class="small" id="add_new_customer" style="cursor:pointer;">Новый клиент</span>
                <span class="separator">/</span>
                <span class="small" id="edit_customer" style="cursor:pointer;">Редактировать</span>
            </div>
        </div>
        <div id="customer_info" style="display:none;">
            <div class="block-item">
                <strong>Адрес:</strong><br />
                <input type="text" name="customer_adress" disabled id="customer_adress" value="<?php echo $this->item->customer_adress; ?>">
            </div>
            <div class="dp50">
                <div class="block-item">
                    <strong>Телефон:</strong><br />
                    <input type="text" name="customer_phone" disabled id="customer_phone" value="<?php echo $this->item->customer_phone; ?>">
                </div>
            </div>
            <div class="dp50">
                <div class="block-item">
                    <strong>E-mail:</strong><br />
                    <input type="text" name="customer_email" disabled id="customer_email" value="<?php echo $this->item->customer_email; ?>">
                </div>
            </div>
            <div class="block-item">
                <span class="small" id="cancel_edit_customer" style="cursor:pointer;">Отмена</span>
            </div>
        </div>
        <div class="clr"></div>
    </div>

    <div class="block" style="width:600px; float: none;">
        <div class="block-title">Параметры запроса</div>
        <div class="block-item">
            <label>Я ищу:</label><br />
            <select name="params[category_id]" id="params_category_id">
                <?php $param_name = 'category_id'; ?>
                <?php $param_value = isset($this->item->params[$param_name]) ? $this->item->params[$param_name] : ''; ?>
                <?php foreach ($this->categories as $key => $category) : ?>                    
                    <option value="<?php echo $category->id; ?>" <?php if ($category->id == $param_value) echo 'selected'; ?>><?php echo $category->title; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="dp33">
            <div class="block-item">
                <label>По цене от</label><br />
                <?php $param_name = 'price_from'; ?>
                <?php $param_value = isset($this->item->params[$param_name]) ? $this->item->params[$param_name] : ''; ?>
                <?php echo htmler::inputText('params['.$param_name.']', $param_value, '', false, false); ?>                
            </div>
        </div>
        <div class="dp33">
            <div class="block-item">
                <label>до (руб)</label><br />                
                <?php $param_name = 'price_to'; ?>
                <?php $param_value = isset($this->item->params[$param_name]) ? $this->item->params[$param_name] : ''; ?>
                <?php echo htmler::inputText('params['.$param_name.']', $param_value, '', false, false); ?>                
            </div>
        </div>
        <div class="clr"></div>
        <div class="dp33">
            <div class="block-item">
                <label>Площадью от</label><br />                
                <?php $param_name = 'area_from'; ?>
                <?php $param_value = isset($this->item->params[$param_name]) ? $this->item->params[$param_name] : ''; ?>
                <?php echo htmler::inputText('params['.$param_name.']', $param_value, '', false, false); ?>             
            </div>
        </div>
        <div class="dp33">
            <div class="block-item">
                <label>до (кв.м)</label><br />                
                <?php $param_name = 'area_to'; ?>
                <?php $param_value = isset($this->item->params[$param_name]) ? $this->item->params[$param_name] : ''; ?>
                <?php echo htmler::inputText('params['.$param_name.']', $param_value, '', false, false); ?>               
            </div>
        </div>
        <div class="clr"></div>
        <div class="block-item block-item-floor">
            <label>Этаж:</label>
            <a href="" id="params_floor_select_all"><span class="small">Выбрать все</span></a> /
            <a href="" id="params_floor_select_middle"><span class="small">Кроме первого и последнего</span></a> /
            <a href="" id="params_floor_select_none"><span class="small">Очистить выбор</span></a><br />
            <div class="checkbox-line">
                <?php $param_value = isset($this->item->params['floor']) ? $this->item->params['floor'] : array(); ?>
                <?php echo htmler::checkboxList($this->params['floor'], 'params[floor][]', 'params_floor', $param_value, false); ?>   
            </div>       
        </div>
        <div class="block-item">
            <label>Показывать объекты:</label>
            <div class="checkbox-line">
                <?php 
                    $params_show = array();
                    $params_show[] = 'Все';
                    $params_show[] = 'Мои';
                    $params_show[] = 'Моей компании';
                    $params_show[] = 'Дргуих компаний';                    
                    $param_value = isset($this->item->params['show']) ? $this->item->params['show'] : array();
                    echo htmler::checkboxList($params_show, 'params[show][]', 'params_show', $param_value, false);
                ?>
            </div>
        </div>
        <div class="clr"></div>
        <!-- <input type="button" class="btn-search-objects" value="Подобрать объекты">     -->
    </div>
    <p>Нажмите "Сохранить", чтобы перейти к подбору объектов</p>
    <?php /*
    <div style="width:640px;text-align: center;">
        <p><strong>Объекты к показу</strong></p>
        <table class="main-table" id="approvedObjects">
            <thead>
                <tr>
                    <th style="width:20px;"></th>
                    <!-- <th style="width:100px;">Фото</th> -->
                    <th>Адрес</th>
                    <th style="width:40px;">Площадь</th>
                    <th style="width:40px;">Цена</th>
                </tr>
            </thead>
            <tbody id="approvedObjectsBody">
                <?php foreach ($this->objects as $object) : ?>
                    <tr>
                        <td><span style="cursor:pointer;" onclick="submitObject(<?php echo $object->object_id; ?>);" id="item<?php echo $object->object_id; ?>">-</span></td>
                        <td style="text-align:left;"><a class="open_ajax" href="/admin/realty/edit/<?php echo $object->object_id; ?>?theme=ajax" target="_blank"><?php echo $object->adress; ?></a></td>
                        <td><?php echo $object->total_area; ?></td>
                        <td><?php echo $object->price; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div style="width:640px;text-align: center;">
        
        <p><strong>Выберите объекты для показа клиенту</strong></p>
        <table class="main-table">
            <thead>
                <tr>
                    <th style="width:20px;"></th>
                    <!-- <th style="width:100px;">Фото</th> -->
                    <th>Адрес</th>
                    <th style="width:40px;">Площадь</th>
                    <th style="width:40px;">Цена</th>
                </tr>
            </thead>
            <tbody class="results"></tbody>
        </table>    
    </div>
    
    */ ?>

	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>">

    <div class="buttons">
    	<input type="submit" name="save" value="Сохранить">        
        <input type="submit" name="apply" value="Применить">
        <?php if ($this->item->id > 0) : ?>
            <a href="/admin/realty/requests/view/<?php echo $this->item->id; ?>" title="Закрыть">Закрыть</a>
        <?php else : ?>
            <a href="/admin/realty/requests" title="Закрыть">Закрыть</a>   
        <?php endif; ?> 
    </div>

</form>

