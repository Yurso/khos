<div class="blog">
	<?php foreach ($this->items as $item) : ?>
		<div class="blog-item">
			<div class="blog-item-title">
				<h2 class="blog-item-title"><a href="/blog/post/<?php echo $item->id . '-' . $item->alias; ?>" title="<?php echo $item->title; ?>"><?php echo $item->title; ?></a></h2>
				<div class="blog-item-category">
					in <a href="/blog/category/<?php echo $item->category_id . '-' . $item->category_alias; ?>"><?php echo $item->category_title; ?></a>
					<span class="small">(<?php echo date("d F Y", strtotime($item->public_date)); ?>)</span>
				</div>
			</div>
			
			<div class="blog-item-content"><?php echo $item->content; ?></div>
			<?php if (count($item->tags)) : ?>
				<div class="blog-item-tags"><span>Tags:</span>
					<?php $i=0; foreach ($item->tags as $tag) : $i++ ?>
						<a href="/blog/tag/<?php echo $tag; ?>" title="<?php echo $tag; ?>"><?php echo $tag; ?></a><?php if (count($item->tags) > $i) echo ', '; ?>					
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
</div>
