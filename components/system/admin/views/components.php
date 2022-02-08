<h2 class="content-title">Менеджер компонентов</h2>

<?php if (count($this->components) > 0) : ?>

	<form method="post" action="/admin/system/components/register" class="adminform">
		<div class="block" style="width:300px;">
			<div class="block-title">Новые компоненты</div>
			<div class="block-item">
				<select name="components[]" size="10" multiple style="width:90%;">
					<?php foreach ($this->components as $component) : ?>
						<option value="<?php echo $component; ?>"><?php echo $component; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<input type="hidden" name="type" value="component">
			<input type="submit" value="Зарегистрировать">
		</div>
	</form>

<?php endif; ?>

<?php if (count($this->widgets) > 0) : ?>

	<form method="post" action="/admin/system/components/register" class="adminform">
		<div class="block" style="width:300px;">
			<div class="block-title">Не зарегестрированные виджеты</div>
			<div class="block-item">
				<select name="widgets[]" size="10" multiple style="width:90%;">
					<?php foreach ($this->widgets as $widget) : ?>
						<option value="<?php echo $widget; ?>"><?php echo $widget; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<input type="hidden" name="type" value="widget">
			<input type="submit" value="Зарегистрировать">
		</div>
	</form>

<?php endif; ?>

<?php if (count($this->themes) > 0) : ?>

    <form method="post" action="/admin/system/components/register" class="adminform">
        <div class="block" style="width:300px;">
            <div class="block-title">Не зарегестрированные темы</div>
            <div class="block-item">
                <select name="themes[]" size="10" multiple style="width:90%;">
                    <?php foreach ($this->themes as $theme) : ?>
                        <option value="<?php echo $theme; ?>"><?php echo $theme; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <input type="hidden" name="type" value="theme">
            <input type="submit" value="Зарегистрировать">
        </div>
    </form>

<?php endif; ?>

<div class="clr"></div>

<h4>Зарегестрированные компоненты</h4>

<div class="table-filters-block" style="margin-bottom: 20px;">
    <?php echo htmler::_tableFilters($this->filters); ?>
</div>

<form method="post" action="/admin/system/components" class="adminform1" name="itemsForm">
    <table class="main-table" border="0">
        <thead>
            <tr>
                <th width="25"><input type="checkbox" onClick="toggle(this)"></th>
                <th style="text-align:left;"><?php echo htmler::tableSort('c.name', 'Название'); ?></th>
                <th width="180"><?php echo htmler::tableSort('c.type', 'Тип'); ?></th>
                <th width="100"><?php echo htmler::tableSort('c.state', 'Активен'); ?></th>                
                <th width="180"><?php echo htmler::tableSort('c.register_date', 'Дата регистрации'); ?></th>
                <th width="25"><?php echo htmler::tableSort('c.id', 'id'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this->registred as $item) : ?>
                <tr>
                    <td style="text-align:center;"><input type="checkbox" name="checked[]" value="<?php echo $item->id; ?>" <?php if ($item->protected) echo 'disabled'; ?>></td>
                    <td style="text-align:left;">
                        <?php if ($item->protected) : ?>
                            <?php echo $item->name; ?>
                        <?php else : ?>
                            <a href="/admin/system/components/edit/<?php echo $item->id; ?>"><?php echo $item->name; ?></a>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $item->type; ?></td>
                    <td>
                        <?php 
                        	if ($item->state == -1) {
                        		echo 'Файл не найден';
                        	} else {
                        		echo htmler::YesNo($item->state); 	
                        	}            	
                        ?>
                    </td>
                    <td><?php echo $item->register_date; ?></td>
                    <td align="center"><?php echo $item->id; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="buttons">
        <a href="#" onClick="return submitForm(itemsForm, '/enable');">Включить</a>
        <a href="#" onClick="return submitForm(itemsForm, '/disable');">Отключить</a>        
        <a href="#" onClick="return submitForm(itemsForm, '/unreg');">Удалить</a>
    </div>
</form>

<?php $this->pagination->display(); ?>