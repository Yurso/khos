<?php $params = $item->params; ?>

<div class="l-blogitem <?php echo isset($params['class']) ? $params['class'] : ''; ?>" id="<?php echo isset($params['id']) ? $params['id'] : ''; ?>" <?php echo isset($params['attrs']) ? $params['attrs'] : ''; ?>>

	<?php if (isset($params['fullscreen']) && $params['fullscreen'] == 0) : ?>
		<div class="wrapper">
	<?php endif; ?>

		<?php if (isset($params['show_title']) && $params['show_title']) : ?>
			<h2 class="l-item-title"><?php echo $item->title; ?></h2>
		<?php endif; ?>

		<div class="l-item-content">
			<?php foreach ($item->content as $blog_item) : ?>
				<div class="lic-blog-item">
					<div class="lic-blog-item-content">
						<?php echo $blog_item->content; ?>
					</div>
					<div class="lic-blog-item-title">						
						<?php echo $blog_item->title; ?>
					</div>					
					<div class="clr"></div>
				</div>
			<?php endforeach; ?>
		</div>

	<?php if (isset($params['fullscreen']) && $params['fullscreen'] == 0) : ?>
		</div>
	<?php endif; ?>

	<div class="clr"></div>

</div>