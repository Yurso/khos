<div class="blog-post">
	<div class="blog-item-title">
		<h2 class="blog-item-title"><?php echo $this->item->title; ?></h2>
		<div class="blog-item-category">in <a href="/blog/category/<?php echo $this->item->category_id . '-' . $this->item->category_alias; ?>"><?php echo $this->item->category_title; ?></a></div>
	</div>
	<div class="blog-post-content"><?php echo $this->item->content; ?></div>
	<?php if (count($this->item->tags)) : ?>
		<div class="blog-item-tags"><span>Tags:</span>
			<?php $i=0; foreach ($this->item->tags as $tag) : $i++ ?>
				<a href="/blog/tag/<?php echo $tag; ?>" title="<?php echo $tag; ?>"><?php echo $tag; ?></a><?php if (count($this->item->tags) > $i) echo ', '; ?>					
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>
<?php include('blog_comments.php'); ?>