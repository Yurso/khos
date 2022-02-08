<h2 class="content-title"><?php echo $this->category->title; ?></h2>

<p style="font-size: 11pt;"><a href="/admin/forum/topics/create/<?php echo $this->category->id; ?>">Создать тему</a></p>

<?php if (count($this->items)) : ?>    

    <table class="main-table" border="0">
        <tbody>
            <tr>          
                <th style="width:20px;"></th>      
                <th style="text-align:left;"><?php echo htmler::tableSort('t.title', 'Тема'); ?></th>                     
                <th width="110"><?php echo htmler::tableSort('t.create_date', 'Дата создания темы'); ?></th>                 
                <th width="110"><?php echo htmler::tableSort('t.last_message_date', 'Дата последнего сообщения'); ?></th>                 
                <th width="80"><?php echo htmler::tableSort('t.messages_count', 'Всего сообщений'); ?></th>                 
                <th width="180"><?php echo htmler::tableSort('u.name', 'Автор'); ?></th>            
                <th width="25"><?php echo htmler::tableSort('t.id', 'id'); ?></th>
            </tr>
            <?php foreach ($this->items as $item) : ?>
            <tr>       
                <td><img src="/public/images/icons/icon-24-message.png" alt="" width="24" height="24"></td>         
                <td style="text-align:left;">
                    <a href="/admin/forum/messages/view/<?php echo $item->id; ?>?limitstart=0"><?php echo $item->title; ?></a>
                    <?php if ($item->state == 0) : ?>
                        <span style="font-size:10px;">(тема закрыта)</span>
                    <?php endif; ?>
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

    <?php $this->pagination->display(); ?>

<?php else : ?>
    <p>Здесь появятся темы которые вы создадите.</p>
<?php endif; ?>

