<script type="text/javascript">
	
	$(document).ready(function(){

		$(".message-buttons .delete").click(function(){
	            
	            var url = $(this).attr('href');	            

	            $.ajax(url).done(function( data ) {
			    	
			    	var obj = $.parseJSON(data);

			    	if (obj.success == true) {			    					    		
			    		$("#m"+obj.item_id).animate({ backgroundColor: "#fbc7c7" }, "fast").animate({ opacity: "hide" }, 250);
			    	} else {
			    		alert(obj.message);			    	
			    	}
					
			  	});	    	                   

	           	return false;

	        });
	});

</script>

<h2 class="content-title" style="margin-top:20px;">
	<?php echo $this->topic->title; ?>
	<?php if ($this->topic->state == 0) : ?>
		<span style="color:#aaa;font-weight: normal;font-size: 12pt;">(тема закрыта)</span>
	<?php endif; ?>
</h2>

<p>
	<?php if ($this->topic->subscription) : ?>
		<a href="/admin/forum/topics/unsubscribe/<?php echo $this->topic->id; ?>">Отписаться от темы</a>
	<?php else : ?>
		<a href="/admin/forum/topics/subscribe/<?php echo $this->topic->id; ?>">Подписаться на тему</a>
	<?php endif; ?>

	<?php if ($this->user->access_name == 'administrator') : ?>
		<?php if ($this->topic->state) : ?>
			| <a href="/admin/forum/topics/close/<?php echo $this->topic->id; ?>">Закрыть тему</a>
		<?php else : ?>
			| <a href="/admin/forum/topics/open/<?php echo $this->topic->id; ?>">Открыть тему</a>
		<?php endif; ?>	
	<?php endif; ?>
</p>

<table class="forum-topic">
	<tbody border="0" cellpadding="0" cellspacing="0">
	    <?php foreach ($this->items as $item) : ?>
	        <tr id="m<?php echo $item->id; ?>">
	        	<td class="message-author">	        		
	        		<div class="author-avatar" style="background-image: url(<?php echo $item->author_avatar; ?>);"></div>				        
	        		<div class="author-name"><?php echo $item->author_name; ?></div>	        		
	        		<?php if ($item->author_access == 'administrator') : ?>
	        			<div class="author-agency">(Администратор)</div>
	        		<?php elseif (!empty($item->agency_name)) : ?>
	        			<div class="author-agency">(<?php echo $item->agency_name; ?>)</div>
	        		<?php endif; ?>
	        	</td>
	        	<td class="message-data">
	        		<div class="message-info">
	        			Дата создания: <?php echo date("d.m.y в H:i", strtotime($item->create_date)); ?>
	        			| Дата редактирования: <?php echo date("d.m.y в H:i", strtotime($item->edit_date)); ?>
	        		</div>
	        		<div class="message-text">
	        			<?php echo $item->message_html; ?>
	        		</div>	
	        		<?php if (count($item->files)) : ?>
		        		<div class="message-files">
		        			<?php foreach ($item->files AS $file) : ?>
		        				<a href="<?php echo $file->path.$file->name; ?>" target="_blank" class="<?php echo $file->type; ?>"><?php echo $file->name; ?></a>
		        			<?php endforeach; ?>
		        		</div>
		        	<?php endif; ?>
	        		<div class="message-buttons">
	        			<?php if (strtotime($item->create_date) == strtotime($this->topic->last_message_date) && $this->user->id == $item->author_id && $this->topic->messages_count > 1 && $this->topic->state) : ?>	
	        				<a href="/admin/forum/messages/edit/<?php echo $item->id; ?>">Редактировать</a>
	        				| <a href="/admin/forum/messages/delete/<?php echo $item->id; ?>" class="delete">Удалить</a>
	        			<?php endif; ?>
	        		</div>	
	        	</td>
	        </tr>
	    <?php endforeach; ?>
	</tbody>
</table>

<?php $this->pagination->display(); ?>
<?php if ($this->topic->state) : ?>
	<div class="forum-new-message">
		<h4>Новое сообщение</h4>
		<form method="post" action="/admin/forum/messages/save" id="forum-new-message" class="adminform" enctype="multipart/form-data">	
			<textarea name="message" placeholder="Текст нового сообщения" required></textarea><br /><br />
			<label>Прикрепить файлы:</label>
			<input type="file" name="files[]" multiple /><br /><br />
			<input type="hidden" name="id" value="0">
			<input type="hidden" name="topic_id" value="<?php echo $this->topic->id; ?>">
			<input type="submit" name="submit" value="Отправить">
		</form>
	</div>
<?php endif; ?>


