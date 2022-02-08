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
                    url: '/admin/landing/items/sort',                    
                }).done(function(){
                    $('.sortable td.handle span').each(function( index ) {
                        $( this ).text(index);
                    });
                });            
            }
        });

    });

</script>

<h2 class="content-title">Лендинг</h2>

<?php echo htmler::_tableFilters($this->filters); ?>

<form method="post" action="/admin/landing/items" class="adminform" name="itemsForm">
    <table class="main-table" cellspacing="0" width="100%">
        <thead>
            <tr>            
                <th width="25"><input type="checkbox" onClick="toggle(this)"></th>
                <th width="25"><?php echo htmler::tableSort('i.ordering', ''); ?></th>
                <th style="text-align:left;"><?php echo htmler::tableSort('i.title', 'Заголовок'); ?></th>
                <th width="100"><?php echo htmler::tableSort('i.type', 'Тип'); ?></th>
                <th width="100"><?php echo htmler::tableSort('i.state', 'Опубликовано'); ?></th>
                <th width="25"><?php echo htmler::tableSort('i.id', 'id'); ?></th>
            </tr>
        </thead>
        <tbody class="sortable">
            <?php foreach ($this->items as $item) : ?>
                <tr id="item-<?php echo $item->id; ?>">   
                    <td align="center"><input type="checkbox" name="checked[]" value="<?php echo $item->id; ?>"></td>
                    <td class="handle"><i class="fa fa-arrows-v" aria-hidden="true"></i><span class="small"><?php echo $item->ordering; ?></span></td>    
                    <td style="text-align:left;">
                        <a href="/admin/landing/items/edit/<?php echo $item->id; ?>"><?php echo $item->title; ?></a><br />
                    </td>                   
                    <td align="center"><?php echo $item->type; ?></td>
                    <td><?php echo htmler::YesNo($item->state); ?></td>             
                    <td align="center"><?php echo $item->id; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- <div class="buttons">
    	<a href="/admin/landing/items/create" title="Создать новый элемент">Создать</a>
        <a href="#" onClick="return submitForm(itemsForm, '/duplicate');" title="Скопировать">Скопировать</a>
    	<a href="#" onClick="return submitForm(itemsForm, '/delete');" title="Удалить элементы">Удалить</a>
    </div> -->
    <div class="buttons">
        <?php echo htmler::formButtons($this->buttons, 'itemsForm'); ?>       
    </div>
</form>

<?php $this->pagination->display(); ?>