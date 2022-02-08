<h2 class="content-title">Результаты поиска</h2>

<div class="forum-search">
    <form method="post" action="/admin/forum/index/search"> 
        <label>Поиск:</label>
        <input type="text" name="query" required="required" value="<?php echo $this->query; ?>">
        <input type="submit" name="submit" value="Искать">
    </form>
</div>

<h3>Поиск по темам</h3>
<?php if (count($this->topics)) : ?>
	<table class="main-table" border="0">
		<thead>
			<tr>          
	            <th style="width:20px;"></th>      
	            <th style="text-align:left;">Тема</th>                     
	            <th width="110">Дата создания</th>                 
	            <th width="110">Дата последнего сообщения</th>                 
	            <th width="80">Всего сообщений</th>                 
	            <th width="180">Автор</th>            
	            <th width="25">id</th>
	        </tr>
		</thead>
	    <tbody>
	        <?php foreach ($this->topics as $item) : ?>
	        <tr>       
	            <td><img src="/public/images/icons/icon-24-message.png" alt="" width="24" height="24"></td>         
	            <td style="text-align:left;">
	                <a href="/admin/forum/messages/view/<?php echo $item->id; ?>?limitstart=0"><?php echo htmler::highlight($item->title, $this->query_parts); ?></a>
	            </td>                                     
	            <td><?php echo date("d.m.y в H:i", strtotime($item->create_date)); ?></td>  
	            <td><?php echo date("d.m.y в H:i", strtotime($item->last_message_date)); ?></td>  
	            <td align="center"><?php echo $item->messages_count; ?></td>
	            <td align="center"><?php echo $item->user_name; ?></td>
	            <td align="center"><?php echo $item->id; ?></td>
	        </tr>
	        <?php endforeach; ?>
	    </tbody>
	</table>
<?php else : ?>
	<p>Ничего не найдено</p>
<?php endif; ?>

<p>&nbsp;</p>

<h3>Поиск по сообщениям</h3>
<?php if (count($this->messages)) : ?>
	<table class="forum-topic">
		<tbody border="0" cellpadding="0" cellspacing="0">
		    <?php foreach ($this->messages as $item) : ?>
		    	<tr>
		    		<td colspan="2">Тема: <a href="/admin/forum/messages/view/<?php echo $item->topic_id; ?>#m<?php echo $item->id; ?>"><?php echo $item->topic_title; ?></a></td>
		    	</tr>
		        <tr id="m<?php echo $item->id; ?>">
		        	<td class="message-author">	        		
		        		<div class="author-avatar" style="background-image: url(<?php echo $item->author_avatar; ?>);"></div>				        
		        		<div class="author-name"><?php echo $item->author_name; ?></div>	        		
		        		<?php if (!empty($item->agency_name)) : ?>
		        			<div class="author-agency">(<?php echo $item->agency_name; ?>)</div>
		        		<?php endif; ?>
		        	</td>
		        	<td class="message-data">
		        		<div class="message-info">
		        			Дата создания: <?php echo date("d.m.y в H:i", strtotime($item->create_date)); ?>
		        			| Дата редактирования: <?php echo date("d.m.y в H:i", strtotime($item->edit_date)); ?>
		        		</div>
		        		<div class="message-text">
		        			<?php echo htmler::highlight($item->message_html, $this->query_parts); ?>
		        		</div>	
		        	</td>
		        </tr>
		    <?php endforeach; ?>
		</tbody>
	</table>
<?php else : ?>
	<p>Ничего не найдено</p>
<?php endif; ?>


