<?php $params = $item->params; ?>

<?php if (isset($params['fullscreen']) && $params['fullscreen'] == 0) : ?>
	<div class="wrapper">
<?php endif; ?>

<?php if (isset($params['show_title']) && $params['show_title']) : ?>
	<div class="l-item-title"><?php echo $item->title; ?></div>
<?php endif; ?>

<?php echo $item->content; ?>

<?php if (isset($params['fullscreen']) && $params['fullscreen'] == 0) : ?>
	</div>
<?php endif; ?>