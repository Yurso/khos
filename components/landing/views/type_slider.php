<?php $params = $item->params; ?>

<div class="l-slider-item <?php echo isset($params['class']) ? $params['class'] : ''; ?>" id="<?php echo isset($params['id']) ? $params['id'] : ''; ?>">

	<?php if (isset($params['fullscreen']) && $params['fullscreen'] == 0) : ?>
		<div class="wrapper">
	<?php endif; ?>

		<?php if (isset($params['show_title']) && $params['show_title']) : ?>
			<h2 class="l-item-title"><?php echo $item->title; ?></h2>
		<?php endif; ?>

		<div class="l-item-content">
			<div class="fotorama" data-arrows="true" data-click="false" data-swipe="true" data-autoplay="true">
				<?php foreach ($item->content as $image) : ?>
					<img src="<?php echo $image->pathway.$image->filename; ?>" alt="<?php echo $image->title; ?>">
				<?php endforeach; ?>
			</div>
		</div>

	<?php if (isset($params['fullscreen']) && $params['fullscreen'] == 0) : ?>
		</div>
	<?php endif; ?>

</div>