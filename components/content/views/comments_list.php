<?php foreach ($this->comments as $comment) : ?>
	<div class="comments-item">
		<div class="comments-item-head"><strong><?php echo $comment->name; ?></strong> <span style="font-size:10px;">(<?php echo date("d.m.y h:i", strtotime($comment->create_date)); ?>)</span></div>
		<p class="comments-item-body"><?php echo $comment->comment; ?></p>
		<div class="comments-item-buttons">
			<?php if (User::getUserAccessName() == 'administrator' || User::getUserData('id') == $comment->user_id) : ?>
				<a class="delete" onclick="return delete_comment('/comments/delete/<?php echo $comment->id; ?>');" href="/comments/delete/<?php echo $comment->id; ?>" title="Удалить комментарий"><span class="ui-icon ui-icon-close"></span></a>
			<?php endif; ?>
		</div>
	</div>
<?php endforeach; ?>	