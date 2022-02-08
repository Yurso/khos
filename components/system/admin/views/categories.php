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
                    url: '/admin/system/categories/sort',                    
                }).done(function(){
                    $('.sortable td.handle span').each(function( index ) {
                        $( this ).text(index);
                    });
                });            
            }
        });

    });

</script>

<h2 class="content-title">Категории</h2>

<?php echo htmler::_tableFilters($this->filters); ?>

<form method="post" action="/admin/system/categories/delete" class="adminform" name="adminForm">
    <table class="main-table" border="0">
        <thead>
            <tr>
                <th width="25"><input type="checkbox" onClick="toggle(this)"></th>
                <th width="25"><?php echo htmler::tableSort('ordering', '<i class="fa fa-arrows-v" aria-hidden="true"></i>'); ?></th>
                <th style="text-align:left;"><?php echo htmler::tableSort('title', 'Заголовок'); ?></th>            
                <th width="200"><?php echo htmler::tableSort('component', 'Компонент'); ?></th>               
                <th width="100"><?php echo htmler::tableSort('state', 'Опубликовано'); ?></th>
                <th width="25"><?php echo htmler::tableSort('id', 'id'); ?></th>
            </tr>
        </thead>       
        <tbody class="sortable">
            <?php foreach ($this->items as $item) : ?>
                <tr id="item-<?php echo $item->id; ?>">
                    <td align="center"><input type="checkbox" name="checked[]" value="<?php echo $item->id; ?>"></td>
                    <td class="handle"><i class="fa fa-arrows-v" aria-hidden="true"></i><span class="small"><?php echo $item->ordering; ?></span></td>    
                    <td style="text-align:left;">
                        <a href="/admin/system/categories/edit/<?php echo $item->id; ?>"><?php echo $item->title; ?></a>
                        <?php if (!empty($item->alias)) : ?>
                            <span style="font-size:10px;">(<?php echo $item->alias; ?>)</span>
                        <?php endif; ?>
                    </td>   
                    <td><?php echo $item->component; ?></td>                                     
                    <td><?php echo htmler::YesNo($item->state); ?></td>
                    <td align="center"><?php echo $item->id; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="buttons">
    	<a href="/admin/system/categories/create" title="Создать новый элемент">Создать</a>
    	<a href="#" onClick="adminForm.submit();return false;" title="Удалить элементы">Удалить</a>
    </div>
</form>

<?php #$this->pagination->display(); ?>