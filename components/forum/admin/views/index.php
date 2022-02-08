<h2 class="content-title">Форум</h2>

<p>Откройте интересующий вас раздел, после чего вы сможете создать или просмотреть темы, созданные ранее.</p>

<div class="forum-search">
    <form method="post" action="/admin/forum/index/search"> 
        <label>Поиск:</label>
        <input type="text" name="query" required="required">
        <input type="submit" name="submit" value="Искать">
    </form>
</div>

<h3>Разделы форума</h3>
<?php if (count($this->items)) : ?>    

    <table class="main-table" border="0">
        <tbody>
            <tr>
                <th style="width:32px;"></th>
                <th style="text-align: left;">Название раздела</th>
            </tr>
            <?php foreach ($this->items as $item) : ?>
            <tr>                
                <td><img src="/public/images/icons/icon-32-forum.png" alt="" width="32" height="32"></td>
                <td style="text-align:left;">
                    <a href="/admin/forum/topics/view/<?php echo $item->id; ?>"><?php echo $item->title; ?></a>
                    <?php if (!empty($item->description)) : ?>
                        <div><?php echo $item->description; ?></div>
                    <?php endif; ?>
                </td>                                     
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php else : ?>

    <p>На форуме пока нет ни одной категории</p>
    
<?php endif; ?>

<div class="forum-last-messages">
    <h3>Последние сообщения</h3>
    <table class="main-table" border="0">
        <tbody>
            <tr>
                <th style="width:32px;"></th>
                <th style="text-align: left;">Сообщение</th>
                <th>Автор</th>
                <th>Тема</th>
                <th>Раздел</th>
            </tr>
            <?php foreach ($this->last_messages as $message) : ?>
            <tr> 
                <td><img src="/public/images/icons/icon-24-message.png" alt="" width="24" height="24"></td>
                <td style="text-align: left;">
                    <div class="forum-short-messsage">
                        <a href="/admin/forum/messages/view/<?php echo $message->topic_id; ?>#m<?php echo $message->id; ?>">
                            <?php echo mb_substr($message->message, 0, 100); ?>...
                        </a>
                    </div>
                </td>                                                 
                <td><?php echo $message->author_name; ?></td>     
                <td>
                    <a href="/admin/forum/messages/view/<?php echo $message->topic_id; ?>">
                        <?php echo $message->topic_title; ?>
                    </a>
                </td> 
                <td>
                    <a href="/admin/forum/topics/view/<?php echo $message->category_id; ?>">
                        <?php echo $message->category_title; ?>
                    </a>
                </td> 
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
