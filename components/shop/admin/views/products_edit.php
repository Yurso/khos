<script type="text/javascript">
    
    $(document).ready(function(){  
        
        $('.sortable').sortable({
            //handle: ".handle",
            cursor: "move",
            axis: 'y',
            placeholder: "ui-state-highlight",
            update: function (event, ui) {
                var data = $(this).sortable('serialize');

                $.ajax({
                    data: data,
                    type: 'POST',
                    url: '/admin/shop/products/images_sort',                    
                });            
            }
        });

        $(".images-table .delete").click(function(){
            
            var url = $(this).attr('href');

            $.ajax(url);

            $(this).parents("tr").animate({ backgroundColor: "#fbc7c7" }, "fast")
            .animate({ opacity: "hide" }, 250);

            return false;

        });

    });

</script>

<h2 class="content-title">Редактор продуктов магазина</h2>

<form method="post" action="/admin/shop/products/save" enctype="multipart/form-data" class="adminform">

    <div class="block" style="width:700px;">
    	<div class="block-title">Основное</div>
    	<div class="block-item">
            <label>Заголовок:</label><br />
            <input type="text" name="title" value="<?php echo $this->item->title; ?>" required autofocus>
        </div>
        <div class="block-item">
            <label>Алиас:</label><br />
            <input type="text" name="alias" value="<?php echo $this->item->alias; ?>">
        </div>
        <div class="block-item">
            <label>Опубликован:</label><br />
            <?php echo htmler::booleanSelect($this->item->state, 'state'); ?>
        </div>
        <div class="block-item">
            <label>Описание:</label><br />
            <textarea class="redactor" name="description"><?php echo $this->item->description; ?></textarea>
        </div>
    </div>

    <div class="block" style="width:250px;">
        <div class="block-title">Изображения</div>
        <p>Первое изображение в списке является <strong>основным</strong>.</p>
        <div class="block-item" style="overflow-y:scroll;height:300px;border: 1px solid #ddd;background-color: #fff;">
            <table class="images-table">
                <tbody class="sortable">
                    <?php $i=0; foreach ($this->item->images as $image) : $i++ ?>
                        <tr id="item-<?php echo $image->id; ?>">                            
                            <td><?php echo $i; ?>.</td>
                            <td width="50"><a class="fancybox" rel="images1" href="/public/images/shop/products/<?php echo $image->image_name; ?>"><img src="/public/images/shop/products/thumbs/<?php echo $image->image_name; ?>" alt="<?php echo $image->image_name; ?>" /></a></td>
                            <td><a class="delete" href="/admin/shop/products/images_delete/<?php echo $image->id; ?>">Удалить</a></td>
                        </tr>
                    <?php endforeach; ?>    
                </tbody>
            </table>
        </div>
        <div class="block-item">        
            <input type="file" name="images[]" multiple />            
        </div>
    </div>

    <div class="block">
        <div class="block-title">Дополнительно</div>
        <div class="block-item">
            <label>Цена:</label><br />
            <input type="text" name="price" value="<?php echo $this->item->price; ?>">
        </div>
        <div class="block-item">
            <label>Единица измерения:</label><br />
            <select name="unit_id">                
                <?php foreach ($this->units as $unit) : ?>
                    <option value="<?php echo $unit->id; ?>" <?php if ($unit->id == $this->item->unit_id) echo 'selected'; ?>><?php echo $unit->title; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="block-item">
            <label>Категории:</label><br />
            <select name="categories[]" size="10" style="width:95%;" multiple required>                
                <?php foreach ($this->categories as $category) : ?>
                    <option value="<?php echo $category->id; ?>" <?php if ($category->product_id > 0) echo 'selected'; ?>><?php echo $category->title; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>">

    <div class="buttons">
    	<input type="submit" value="Сохранить">
        <a href="/admin/shop/products" title="Закрыть">Закрыть</a>
    </div>

</form>

