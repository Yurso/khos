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
                    url: '/admin/system/widgets/sort',                    
                }).done(function(){
                    $('.sortable td.handle span').each(function( index ) {
                        $( this ).text(index);
                    });
                });            
            }
        });

    });

</script>

<h2 class="content-title">Менеджер виджетов</h2>

<?php echo htmler::_tableFilters($this->filters); ?>

<form method="post" action="/admin/system/widgets/delete" class="adminform" name="adminForm">
	<table class="main-table">
		<thead>
			<tr>
				<th width="25"><input type="checkbox" onClick="toggle(this)"></th>
				<th width="25"><?php echo htmler::tableSort('ordering', ''); ?></th>
				<th style="text-align:left;"><?php echo htmler::tableSort('title', 'Заголовок'); ?></th>
				<th width="200"><?php echo htmler::tableSort('widget', 'Виджет'); ?></th>
				<th width="200"><?php echo htmler::tableSort('position', 'Позиция'); ?></th>
				<th width="100"><?php echo htmler::tableSort('state', 'Опубликован'); ?></th>				
				<th width="25"><?php echo htmler::tableSort('id', 'id'); ?></th>
			</tr>
		</thead>
		<tbody class="sortable">
			<?php foreach($this->items as $item) : ?>
				<tr id="item-<?php echo $item->id; ?>">
					<td style="text-align:center;"><input type="checkbox" name="checked[]" value="<?php echo $item->id; ?>"></td>
					<td class="handle"><i class="fa fa-arrows-v" aria-hidden="true"></i><span class="small"><?php echo $item->ordering; ?></span></td>    
					<td style="text-align:left;"><a href="/admin/system/widgets/edit/<?php echo $item->id; ?>"><?php echo $item->title; ?></a></td>
					<td><?php echo $item->widget; ?></td>
					<td><?php echo $item->position; ?></td>
					<td><?php echo htmler::YesNo($item->state); ?></td>					
					<td><?php echo $item->id; ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</form>

<div class="buttons">	
	<a href="/admin/system/widgets/create">Создать</a>
	<a href="#" onClick="adminForm.submit();return false;" title="Удалить элементы">Удалить</a>
</div>

<?php $this->pagination->display(); ?>