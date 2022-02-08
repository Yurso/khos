<script type="text/javascript">
    
    $(function() {

        $.widget( "custom.catcomplete", $.ui.autocomplete, {
            _create: function() {
                this._super();
                this.widget().menu( "option", "items", "> :not(.ui-autocomplete-category)" );
            },
            _renderMenu: function( ul, items ) {
                var that = this,
                currentCategory = "";
                $.each( items, function( index, item ) {
                    var li;
                    if ( item.category != currentCategory ) {
                        ul.append( "<li class='ui-autocomplete-category'>" + item.category + "</li>" );
                        currentCategory = item.category;
                    }
                    li = that._renderItemData( ul, item );
                    if ( item.category ) {
                        li.attr( "aria-label", item.category + " : " + item.label );
                    }
                });
            }
        });

        $( "#actions" ).focus(function() {
            
            var controller = $("#controller").val();

            var url = '/' + controller + '/actions';
            
            $(this).catcomplete({            
                source: url,
                minLength: 0
            });

            $(this).keydown();

        });

        $("#controller").change(function() {
            $("#actions").val("");
        });        

    });
</script>
    
<div class="menuitem-edit" style="position:relative;">
    
    <h2 class="content-title">Редактор элементов меню</h2>

    <form method="post" action="/admin/menu/items/save" class="adminform">

        <div class="block" style="width:300px;">
            <div class="block-title">Основное</div>
            <div class="block-item">
                <label>Заголовок:</label><br />
                <input type="text" name="title" value="<?php echo $this->item->title; ?>" required>
            </div>
            <div class="block-item">
                <label>Опубликован:</label><br />
                <?php echo htmler::booleanSelect($this->item->state, 'state'); ?>
            </div>
            <div class="block-item">
                <label>Меню:</label><br />
                <select name="menu_id" <?php if ($this->item->id > 0) echo 'disabled'; ?> >
                    <?php foreach ($this->menus as $key => $value) : ?>
                        <option value="<?php echo $value->id; ?>" <?php if($value->id == $this->item->menu_id) echo 'selected'; ?>><?php echo $value->name; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>            
            <div class="block-item">
                <label>Доступ</label><br />
                <?php echo htmler::SelectTree($this->users_access, 'access_id', 'id', 'name', $this->item->access_id); ?>                
            </div>
            <div class="block-item">
                <label>Родительский пункт:</label><br />
                <select name="parent_id">
                    <option value="0">- Нет родителя -</option>
                    <?php foreach ($this->parents as $key => $value) : ?>
                        <option value="<?php echo $value->id; ?>" <?php if($value->id == $this->item->parent_id) echo 'selected'; ?>><?php echo $value->title; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="block-item">
                <label>Компонент:</label><br />
                <select name="component" id="component">
                    <option value="">Простая ссылка</option>
                    <option value="separator">Разделитель</option>
                    <option disabled>---</option>
                    <?php foreach ($this->components as $key => $value) : ?>
                        <option value="<?php echo $value->name; ?>" <?php if ($value->name == $this->item->component) echo 'selected'; ?>><?php echo $value->name; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="block-item">
                <label>Контроллер:</label><br />
                <select name="controller" id="controller">
                    <option value="">Простая ссылка</option>
                    <option disabled>---</option>
                    <?php foreach ($this->controllers as $key => $value) : ?>
                        <option value="<?php echo $value->name; ?>" <?php if ($value->name == $this->item->controller) echo 'selected'; ?>><?php echo $value->name; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="block-item">
                <label>Действие (сслыка):</label><br />
                <input type="text" name="action" id="actions" value="<?php echo $this->item->action; ?>" >
            </div>
        </div>

        <div class="block" style="width:300px;">
            <div class="block-title">Прочее</div>
            <div class="block-item">
                <label>Открывать в:</label><br />
                <?php 
                    $targets = array(
                        '_self' => 'Текущей вкладке',
                        '_blank' => 'Новой вкладке'                        
                    );
                ?>
                <?php echo htmler::SelectList($targets, 'target', null, null, $this->item->target); ?>
            </div>
            <div class="block-item">
                <label>Изображение:</label><br />
                <input type="text" name="image" value="<?php echo $this->item->image; ?>">
            </div>
            <div class="block-item">
                <label>Комментарий:</label>
                <textarea rows="3" cols="40" name="description"><?php echo $this->item->description; ?></textarea>
            </div>
            <div class="block-item">
                <label>Запрос для счетчика:</label>
                <textarea rows="3" cols="40" name="counter_query"><?php echo $this->item->counter_query; ?></textarea>
            </div>
        </div>

        <input type="hidden" name="id" value="<?php echo $this->item->id; ?>">
        
        <div class="buttons">
            <input type="submit" name="save" value="Сохранить">        
            <input type="submit" name="apply" value="Применить">        
            <a href="/admin/menu/items" title="Закрыть">Закрыть</a>
        </div>

    </form>
</div>
