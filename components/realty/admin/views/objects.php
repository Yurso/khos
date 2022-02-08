<script type="text/javascript">
    $(document).ready(function(){
        
        $(".open_ajax").fancybox({
            type: 'ajax',
            maxWidth: 740,
            helpers: {
                overlay: {
                    locked: false
                }
            }
        });

    });
</script>

<h2 class="content-title"><?php echo $this->tmpl_page_title; ?></h2>

<?php include('menu.php'); ?>

<?php if (Registry::get('route')->action == 'archive') : ?>
    <p>В данном разделе находятся ваши объекты, помещенные в архив.<br />Объекты архивируются автоматически, если по ним небыло активности в течение 14 дней.</p>
    <p>Чтобы восстановить объект выделите его и нажмите кнопку "Восстановить"</p>
<?php endif; ?>

<?php if (Registry::get('route')->action == 'trash') : ?>
    <p>В данном разделе находятся объекты, которые вы удалили.</p>
    <p>Чтобы восстановить объект выделите его и нажмите кнопку "Восстановить"</p>
<?php endif; ?>

<div class="table-filters-block">
    <?php echo htmler::_tableFilters($this->filters); ?>
</div>

<form method="post" action="/admin/realty/objects" class="adminform" name="itemsForm" style="white-space: nowrap;">
    <?php if (count($this->items)) : ?>
        <div class="content-body">
            <table class="main-table" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th width="25" class="mobile-hide"><input type="checkbox" onClick="toggle(this)"></th>                                                
                        <th width="25"></th>
                        <th width="25" class="mobile-hide"><?php echo htmler::tableSort('r.last_edit', 'Дата'); ?></th>
                        <th style="text-align:left;"><?php echo htmler::tableSort('r.adress', 'Адрес'); ?></th>            
                        <th width="25" class="mobile-hide"><?php echo htmler::tableSort('categories.title', 'Категория'); ?></th>
                        <th width="25"><?php echo htmler::tableSort('r.price', 'Цена'); ?></th>  
                        <th width="25" class="mobile-hide"><?php echo htmler::tableSort('r.floor', 'Эт'); ?></th>
                        <th width="25" class="mobile-hide"><?php echo htmler::tableSort('r.house_type', 'Дом'); ?></th>
                        <th width="25" class="mobile-hide"><?php echo htmler::tableSort('r.total_area', 'Общ. пл.'); ?></th>
                        <th width="25" class="mobile-hide"><?php echo htmler::tableSort('r.living_area', 'Жил. пл.'); ?></th>
                        <th width="25" class="mobile-hide"><?php echo htmler::tableSort('r.kitchen_area', 'Пл. кухни'); ?></th>                                        
                        <th width="25" class="mobile-hide"><?php echo htmler::tableSort('r.wc_type', 'Сан. узел'); ?></th>
                        <th width="25" class="mobile-hide"><?php echo htmler::tableSort('r.loggia_type', 'Л/Б'); ?></th>
                        <th width="25" class="mobile-hide"><?php echo htmler::tableSort('r.rights', 'Статус'); ?></th>                                        
                        <th width="25" class="mobile-hide"><?php echo htmler::tableSort('r.agent_name', 'Агент'); ?></th>
                        <th width="25" class="mobile-hide"><?php echo htmler::tableSort('r.commission', 'Комиссия'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($this->items as $item) : ?>
                    <tr class="<?php if ($item->param_exclusive) echo 'exclusive'; ?>">
                        <td align="center" class="mobile-hide"><input type="checkbox" name="checked[]" value="<?php echo $item->id; ?>"></td>                                              
                        <td width="25"><img src="<?php echo $item->agency_logo; ?>" alt="<?php echo $item->agency_name; ?>" title="<?php echo $item->agency_name; ?>" width="25"></td>
                        <td class="mobile-hide"><?php echo date("d.m.y", strtotime($item->last_edit)); ?></td>                                
                        <td style="text-align:left;">                            
                            
                            <a href="/admin/realty/objects/view/<?php echo $item->id; ?>?theme=ajax" class="open_ajax mobile-hide"><?php echo $item->adress; ?></a>
                            <a href="/admin/realty/objects/view/<?php echo $item->id; ?>" class="open_ajax mobile-only"><?php echo $item->adress; ?></a>

                            <br />
                            
                            <span class="mobile-only"><?php echo $item->category_title; ?></span>
                            
                            <?php if ($item->param_exclusive) : ?>
                                <span class="mobile-only">/</span>
                                <span style="font-size:10px;">Эксклюзив</span>                                
                            <?php endif; ?>                    

                        </td> 
                        <td class="mobile-hide"><?php echo $item->category_title; ?></td>  
                        <td><?php echo htmler::esc_price($item->price); ?></td>
                        <td class="mobile-hide"><?php echo $item->floor; ?>/<?php echo $item->floors; ?></td>
                        <td class="mobile-hide"><?php echo $this->params['house_type'][$item->house_type]; ?></td>
                        <td class="mobile-hide"><?php echo $item->total_area; ?></td>
                        <td class="mobile-hide"><?php echo $item->living_area; ?></td>
                        <td class="mobile-hide"><?php echo $item->kitchen_area; ?></td>
                        <td class="mobile-hide"><?php echo $this->params['wc_type'][$item->wc_type]; ?></td>
                        <td class="mobile-hide"><?php echo $this->params['loggia_type'][$item->loggia_type]; ?></td>
                        <td class="mobile-hide"><div class="small_cell" title="<?php echo $item->rights; ?>"><?php echo $item->rights; ?><div></td>                                        
                        <td class="mobile-hide"><div class="small_cell" style="font-size:11px;" title="<?php echo $item->agent_name; ?>"><?php echo $item->agent_name; ?><div></td>
                        <td class="mobile-hide"><?php echo htmler::esc_price($item->commission); ?></td>
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
    	<?php if (Registry::get('route')->action == 'index') : ?>
            <a href="/admin/realty/objects/create" class="btn-blue" title="Создать новый элемент">Создать</a>
            <!-- <a href="#" onClick="return submitForm(itemsForm, '/duplicate');" title="Скопировать">Скопировать</a> -->
        	<a href="#" class="mobile-hide" onClick="return submitForm(itemsForm, '/archivate');">Поместить в архив</a>
            <a href="#" class="mobile-hide" onClick="return submitForm(itemsForm, '/delete');">Удалить</a>
            <a href="#" class="mobile-hide" onClick="return submitForm(itemsForm, '/export', '_blank');">Экспорт</a>        
            <a href="#" class="mobile-hide" onClick="return submitForm(itemsForm, '/printlist', '_blank');">Печать</a> 
        <?php elseif (Registry::get('route')->action == 'archive') : ?>
            <a href="#" class="mobile-hide" onClick="return submitForm(itemsForm, '/recover');">Восстановить</a>
            <a href="#" class="mobile-hide" onClick="return submitForm(itemsForm, '/delete');">Удалить</a>
        <?php elseif (Registry::get('route')->action == 'trash') : ?>
            <a href="#" class="mobile-hide" onClick="return submitForm(itemsForm, '/recover');">Восстановить</a>
            <a href="#" class="mobile-hide" onClick="return submitForm(itemsForm, '/delete');">Удалить навсегда</a>
        <?php endif; ?>       
    </div>
</form>

<?php $this->pagination->display(); ?>

<p class="mobile-only" style="text-align: center;padding: 20px 0 0;">
    <a href="/user/logout">Выход</a>
</p>