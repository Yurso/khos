<?php $params = $item->params; ?>

<div class="l-wysywig-item <?php echo isset($params['class']) ? $params['class'] : ''; ?>" id="<?php echo isset($params['id']) ? $params['id'] : ''; ?>" <?php echo isset($params['attrs']) ? $params['attrs'] : ''; ?> >

	<div class="wrapper">

		<?php if (isset($params['show_title']) && $params['show_title']) : ?>
			<h2 class="l-item-title"><?php echo $item->title; ?></h2>
		<?php endif; ?>

		<div class="l-wysywig-content">
			<?php echo $item->content; ?>
		</div>

	</div>

</div>