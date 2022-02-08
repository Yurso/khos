<script type="text/javascript">
    
    $(document).ready(function(){  

        $('.sortable').sortable({
            handle: ".handle",
            cursor: "move",
            axis: 'y',
            placeholder: "ui-state-highlight",
            update: function (event, ui) {
                var data = $(this).sortable('serialize');

                $.ajax({
                    data: data,
                    type: 'POST',
                    url: '/admin/content/gallery/sort',                    
                }).done(function(){
                    $('.sortable td.handle span').each(function( index ) {
                        $( this ).text(index);
                    });
                });            
            }
        });

    });

</script>

<h2 class="content-title">Галерея изображений</h2>

<div>
    <form method="post" action="/admin/content/gallery/multiplesave" enctype="multipart/form-data" class="adminform">
        <div class="block" style="width:270px;float:none;">
            <div class="block-title">Загрузка изображений</div>
            <div class="block-item">
                <label>Категория для загрузки:</label><br />
                <select name="category_id">
                    <option value="">- Выберите категорию -</option>                
                    <?php foreach ($this->categories as $key => $value) : ?>
                        <option value="<?php echo $value->id; ?>"><?php echo $value->title; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="block-item">
                <input type="file" name="images[]" multiple /> 
            </div>
            <div class="block-item">
                <input type="submit" value="Сохранить" /> 
            </div>
        </div>
    </form>
</div>

<?php echo htmler::_tableFilters($this->filters); ?>

<form method="post" action="/admin/content/gallery" class="adminform" name="itemsForm">
    <table class="main-table" border="0">
        <thead>
            <tr>
                <th width="25"><input type="checkbox" onClick="toggle(this)"></th>
                <th width="25"><?php echo htmler::tableSort('ordering', '<i class="fa fa-arrows-v" aria-hidden="true"></i>'); ?></th>
                <th width="50">Из-ие</th>
                <th style="text-align:left;"><?php echo htmler::tableSort('title', 'Заголовок'); ?></th>            
                <th width="180"><?php echo htmler::tableSort('category_id', 'Категория'); ?></th>
                <th width="100"><?php echo htmler::tableSort('state', 'Опубликовано'); ?></th>                
                <th width="25"><?php echo htmler::tableSort('id', 'id'); ?></th>
            </tr>
        </thead>
        <tbody class="sortable">
            <?php foreach ($this->items as $item) : ?>
                <tr id="item-<?php echo $item->id; ?>">
                    <td style="text-align:center;"><input type="checkbox" name="checked[]" value="<?php echo $item->id; ?>"></td>
                    <td class="handle"><i class="fa fa-arrows-v" aria-hidden="true"></i><span class="small"><?php echo $item->ordering; ?></span></td>
                    <td><img src="<?php echo $item->pathway.'thumbs/'.$item->filename; ?>" width="50" alt=""></td>
                    <td style="text-align:left;vertical-align:middle;">                        
                        <a href="/admin/content/gallery/edit/<?php echo $item->id; ?>"><?php echo $item->title; ?></a>
                    </td>   
                    <td><?php echo $item->category_title; ?></td>                             
                    <td><?php echo htmler::YesNo($item->state); ?></td>
                    <td align="center"><?php echo $item->id; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="buttons">
    	<a href="/admin/content/gallery/create" title="Создать новый элемент">Создать</a>
        <a href="#" onClick="return submitForm(itemsForm, '/duplicate');" title="Скопировать">Скопировать</a>
        <a href="#" onClick="return submitForm(itemsForm, '/delete');" title="Удалить элементы">Удалить</a>
    </div>
</form>

<?php $this->pagination->display(); ?>

