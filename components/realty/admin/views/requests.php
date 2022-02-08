<h2 class="content-title"><?php echo $this->tmpl_page_title; ?></h2>

<div class="controller-menu mobile-only">
    <a href="/admin/realty/objects?limitstart=0">Недвижимость</a>
    | <a href="/admin/realty/objects/archive?limitstart=0">Архив объектов</a>
    | <a href="/admin/realty/objects/trash?limitstart=0">Корзина</a>
    | <a href="/admin/realty/requests" class="mobile-only">Заявки</a>
    <?php if (User::getUserData('access_name') == 'administrator') : ?>
      | <a href="/admin/realty/agencys">Агентства</a>
      | <a href="/admin/realty/params">Параметры</a>
      | <a href="/admin/realty/params_values">Значения параметров</a>      
    <?php endif; ?>
</div>

<div class="table-filters-block">
    <?php echo htmler::_tableFilters($this->filters); ?>
</div>

<form method="post" action="/admin/realty/requests" class="adminform" name="itemsForm" style="white-space: nowrap;">
    <?php if (count($this->items)) : ?>
        <div class="content-body">
            <table class="main-table realty-requests-list" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th width="25" class="mobile-hide"><input type="checkbox" onClick="toggle(this)"></th>
                        <th width="25"><?php echo htmler::tableSort('r.create_date', 'Дата'); ?></th>
                        <th style="text-align:left;"><?php echo htmler::tableSort('r.title', 'Заголовок'); ?></th>
                        <th width="25" class="mobile-hide"><?php echo htmler::tableSort('c.name', 'Клиент'); ?></th>                                                
                        <th width="25" class="mobile-hide"><?php echo htmler::tableSort('users.name', 'Автор'); ?></th>
                        <th width="25" class="mobile-hide"><?php echo htmler::tableSort('r.edit_date', 'Изменено'); ?></th>                                                                        
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($this->items as $item) : ?>
                    <?php 
                        $archive_class="";
                        if ($item->archive > 0) $archive_class="archive"; 
                    ?>
                    <tr class="<?php echo 'priority'.$item->priority . ' ' . $archive_class; ?>">
                        <td align="center" class="mobile-hide"><input type="checkbox" name="checked[]" value="<?php echo $item->id; ?>"></td>                                              
                        <td><?php echo date("d.m.y", strtotime($item->create_date)); ?></td>  
                        <td style="text-align:left;">
                            <a href="/admin/realty/requests/view/<?php echo $item->id; ?>"><?php echo $item->title; ?></a>
                            <?php if ($item->user_id == $this->user->id && $item->new_objects_count > 0) : ?>
                                <span class="new_objects_count"><?php echo Main::declOfNum($item->new_objects_count, array('новый', 'новых', 'новых')); ?> </span>
                            <?php endif; ?>
                            <br>
                            <?php if ($item->archive > 0) : ?>
                                <span class="small">В архиве</span>
                            <?php else : ?>
                                <span class="small">Приоритет: <?php echo $this->params['priority'][$item->priority]; ?></span>
                            <?php endif; ?>
                        </td>                           
                        <td class="mobile-hide"><div class="small_cell" style="font-size:11px;"><?php echo $item->customer_name; ?></div></td>                                                  
                        <td class="mobile-hide"><div class="small_cell" style="font-size:11px;"><?php echo $item->user_name; ?></div></td>
                        <td class="mobile-hide"><?php echo date("d.m.y H:i", strtotime($item->edit_date)); ?></td>  
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else : ?>
        <p>Нет элементов для отображения</p>
    <?php endif; ?>

    <input type="hidden" name="ref_page" value="<?php echo $_GET['route']; ?>">

    <div class="buttons">
        <a href="/admin/realty/requests/create" class="btn-blue">Создать</a>
        <a href="#" onclick="return submitForm(itemsForm, '/archivate');" class="mobile-hide">Поместить в архив</a>
        <a href="#" onClick="return submitForm(itemsForm, '/delete');" class="mobile-hide">Удалить</a>  
    </div>
</form>

<?php $this->pagination->display(); ?>