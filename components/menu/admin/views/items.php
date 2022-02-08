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
                    url: '/admin/menu/items/sort',                    
                }).done(function(){
                    $('.sortable td.handle span').each(function( index ) {
                        $( this ).text(index);
                    });
                });            
            }
        });

    });

</script>

<h2 class="content-title">Менеджер пунктов меню</h2>

<?php echo htmler::_tableFilters($this->filters); ?>

<form method="post" action="/admin/menu/items" class="adminform" name="adminForm" id="itemsForm">
    <table class="main-table" border="0">
        <thead>
            <tr>
                <th width="25"><input type="checkbox" onClick="toggle(this)"></th>
                <th width="25"><?php echo htmler::tableSort('items.ordering', ''); ?></th>
                <th style="text-align:left;"><?php echo htmler::tableSort('items.title', 'Заголовок'); ?></th>                                
                <th width="100"><?php echo htmler::tableSort('items.component', 'Компонент'); ?></th>
                <th width="120"><?php echo htmler::tableSort('items.controller', 'Контроллер'); ?></th>
                <th width="120"><?php echo htmler::tableSort('items.action', 'Действие'); ?></th>
                <th width="100"><?php echo htmler::tableSort('user_access.name', 'Доступ'); ?></th>
                <th width="100"><?php echo htmler::tableSort('items.state', 'Опубликовано'); ?></th>
                <th width="25"><?php echo htmler::tableSort('items.id', 'id'); ?></th>
            </tr>
        </thead>
        <tbody class="sortable">
            <?php foreach ($this->items as $item) : ?>
                <tr id="item-<?php echo $item->id; ?>">
                    <td><input type="checkbox" name="checked[]" value="<?php echo $item->id; ?>"></td>
                    <td class="handle"><i class="fa fa-arrows-v" aria-hidden="true"></i><span class="small"><?php echo $item->ordering; ?></span></td>
                    <td style="text-align:left;">
                        <a href="/admin/menu/items/edit/<?php echo $item->id; ?>"><?php echo $item->title; ?></a>
                        <?php if ($item->frontpage) echo '<span style="font-size:10px;">(Начальная страница)</span>'; ?>
                    </td>                                        
                    <td><?php echo $item->component; ?></td>
                    <td><?php echo $item->controller; ?></td>
                    <td><div style="overflow:hidden;height:19px;"><?php echo $item->action; ?></div></td>
                    <td><?php echo $item->access_name; ?></td>
                    <td><?php echo htmler::YesNo($item->state); ?></td>
                    <td><?php echo $item->id; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="buttons">
        <a href="/admin/menu/items/create" title="Создать новый пункт меню">Создать</a>
        <a href="#" onClick="return submitForm(itemsForm, '/frontpageitem');" title="Сделать начальной страницей">Сделать начальной страницей</a>
        <a href="#" onClick="return submitForm(itemsForm, '/delete');" title="Удалить элементы">Удалить</a>
    </div>
</form>