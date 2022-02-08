<?php 
	$cols = $this->columns;
?>

<h2 class="content-title">Отчеты</h2>

<?php include('menu.php'); ?>

<h3>Статистика за год</h3>

<?php if (count($this->items)) : ?>
	<table class="main-table" cellspacing="0" style="width:auto;">
	    <thead>
	        <tr>
	        	<?php foreach ($this->items[0] AS $key => $value) : ?>
	        		
	        		<?php
	        			$style = '';
	        			if (isset($cols[$key]['th_style'])) {
	        				$style = $cols[$key]['th_style'];
	        			}
	        			$title = $key;
	        			if (isset($cols[$key]['title'])) {
	        				$title = $cols[$key]['title'];
	        			}
	        		?>

	            	<th style="<?php echo $style; ?>"><?php echo $title; ?></th>                

	        	<?php endforeach; ?>
	        </tr>
	    </thead>
	    <tbody>
	        <?php foreach ($this->items as $item) : ?>
	        <tr>
	            <?php foreach ($item as $key => $value) : ?>

	            	<?php
	        			$style = '';
	        			if (isset($cols[$key]['td_style'])) {
	        				$style = $cols[$key]['td_style'];
	        			}
	        		?>

	            	<td style="<?php echo $style; ?>"><?php echo $value; ?></td>

	            <?php endforeach; ?>
	        </tr>
	        <?php endforeach; ?>
	    </tbody>
	</table>
<?php else : ?>
	<p>Нет данных для вывода</p>
<?php endif; ?>