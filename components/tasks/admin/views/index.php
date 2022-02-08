<script type="text/javascript">

    function getMessagesList() {

        var response_status = $("#response_status").text();

        $.ajax({
            //type: 'POST',
            url:  "/admin/tasks/mail/flagged",            
            cache: false,
            beforeSend: function() {
                $('#response_status').text('Обновляю...');
            },
            success: function(data) {
                
                console.log(data);                

                var result = "";
                
                var messages = jQuery.parseJSON(data);                

                messages.forEach(function(message) {
                    
                    var url = '/admin/tasks/items/create/?msgno='+message.msgno;
                    var state = 0;
                    
                    // if (message.item !== undefined) {
                    //     url = '/admin/tasks/items/edit/'+message.item.id;
                    //     state = 1;
                    // }

                    result = result + '<tr>';
                    result = result + ' <td>'+message.date+'</td>';
                    result = result + ' <td style="text-align:left;">'+message.from_name+'</td>';
                    result = result + ' <td style="text-align:left;"><a href="'+url+'&ref=/admin/tasks" class="state'+state+'">'+message.subject+'</a></td>';
                    result = result + '</tr>';

                });

                $('#response').html(result);                                                  
                $('#response_status').text(response_status);     
            },
            error:  function(xhr, str) {
                $('#response_status').html('Возникла ошибка: ' + xhr.responseCode);
            }
        });

    }
    
    $(document).ready(function(){

        //getMessagesList();


        $("#refresh_table").click(function(){
            getMessagesList();
        });

    });

</script>

<h2 class="content-title"><?php echo $this->tmpl_page_title; ?></h2>

<?php include('menu.php'); ?>

<form method="post" action="/admin/tasks/items" class="adminform" name="itemsForm">

    <div style="margin-bottom:40px;">        
        <h3 class="content-title">Текущие задачи</h3>
        <table class="main-table tasks" cellspacing="0" width="100%">
            <thead>
                <tr>                  
                    <th width="60"><?php echo htmler::tableSort('i.date', 'Дата'); ?></th>                   
                    <th style="text-align:left;"><?php echo htmler::tableSort('i.title', 'Заголовок'); ?></th>            
                    <th width="180"><?php echo htmler::tableSort('i.author_name', 'Автор'); ?></th>
                    <th width="180"><?php echo htmler::tableSort('c.name', 'Клиент'); ?></th>
                    <th width="80" class="mobile-hide"><?php echo htmler::tableSort('i.price', 'Сумма'); ?></th>                     
                    <th width="25" class="mobile-hide"><?php echo htmler::tableSort('i.id', 'id'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->current_items as $item) : ?>
                <tr>                                    
                    <td><?php echo date("d.m.y", strtotime($item->date)); ?></td>
                    <td style="text-align:left;">
                        <a href="/admin/tasks/items/edit/<?php echo $item->id; ?>?ref=/admin/tasks"><?php echo empty($item->title) ? 'Без заголовка' : $item->title; ?></a><br />
                        <span class="small"><?php echo $item->type_title; ?></span>
                    </td>   
                    <td><?php echo $item->author_name; ?></td>
                    <td><?php echo $item->customer_name; ?></td>
                    <td class="mobile-hide"><?php echo htmler::esc_price($item->price*$item->count); ?></td>                                        
                    <td align="center" class="mobile-hide"><?php echo $item->id; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div> 

    <h3 class="content-title" style="margin:30px 0;">Задолженность клиентов</h3>

    <div class="block" style="width:280px;padding:20px 10px 0px;">             
        <div class="block-title">Общая задолженость</div>
        <?php $sum = 0; $tasks = 0; ?>
        <table class="main-table" style="background-color: #fff;">
            <thead>
                <th>Клиент</th>
                <th>Сумма</th>
            </thead>
            <tbody>
            <?php foreach ($this->summary as $item) : ?>
                <?php if ($item->sum == 0) continue; ?>
                <?php $sum = $sum + $item->sum; $tasks = $tasks + $item->tasks;?>
                <tr>
                    <td style="text-align:left;"><?php echo $item->customer_name; ?></td>
                    <td><?php echo htmler::esc_price($item->sum); ?></td>
                <tr>
            <?php endforeach; ?>
            </tbody>
        </table>        
        <div class="block-item" style="margin-top: 10px;font-size: 10px;">
            <strong>Итого: </strong> <?php echo htmler::esc_price($sum); ?><br />
            <strong>Выполнено заданий: </strong> <?php echo $tasks; ?>
        </div>        
    </div>

    <?php if (count($this->current_month)) : ?>
        <div class="block" style="width:280px;padding:20px 10px 0px;">
            <div class="block-title">Текущий месяц</div>
            <?php $sum = 0; $tasks = 0; ?>
            <table class="main-table" style="background-color: #fff;">
                <thead>
                    <th>Клиент</th>
                    <th>Сумма</th>
                </thead>
                <tbody>
                <?php foreach ($this->current_month as $item) : ?>
                    <?php if ($item->sum == 0) continue; ?>
                    <?php $sum = $sum + $item->sum; $tasks = $tasks + $item->tasks;?>
                    <tr>
                        <td style="text-align:left;"><?php echo $item->customer_name; ?></td>
                        <td><?php echo htmler::esc_price($item->sum); ?></td>
                    <tr>
                <?php endforeach; ?>
                </tbody>
            </table>        
            <div class="block-item" style="margin-top: 10px;font-size: 10px;">
                <strong>Итого: </strong> <?php echo htmler::esc_price($sum); ?><br />
                <strong>Выполнено заданий: </strong> <?php echo $tasks; ?>
            </div>        
        </div>
    <?php endif; ?>

    <?php if (count($this->month_before)) : ?>
        <div class="block" style="width:280px;padding:20px 10px 0px;">
            <div class="block-title">Предыдущий месяц</div>
            <?php $sum = 0; $tasks = 0; ?>
            <table class="main-table" style="background-color: #fff;">
                <thead>
                    <th>Клиент</th>
                    <th>Сумма</th>
                </thead>
                <tbody>
                <?php foreach ($this->month_before as $item) : ?>
                    <?php if ($item->sum == 0) continue; ?>
                    <?php $sum = $sum + $item->sum; $tasks = $tasks + $item->tasks;?>
                    <tr>
                        <td style="text-align:left;"><?php echo $item->customer_name; ?></td>
                        <td><?php echo htmler::esc_price($item->sum); ?></td>
                    <tr>
                <?php endforeach; ?>
                </tbody>
            </table>        
            <div class="block-item" style="margin-top: 10px;font-size: 10px;">
                <strong>Итого: </strong> <?php echo htmler::esc_price($sum); ?><br />
                <strong>Выполнено заданий: </strong> <?php echo $tasks; ?>
            </div>        
        </div>
    <?php endif; ?>

    <?php if (count($this->all_before)) : ?>
        <div class="block" style="width:280px;padding:20px 10px 0px;">
            <div class="block-title">Ранее</div>
            <?php $sum = 0; $tasks = 0; ?>
            <table class="main-table" style="background-color: #fff;">
                <thead>
                    <th>Клиент</th>
                    <th>Сумма</th>
                </thead>
                <tbody>
                <?php foreach ($this->all_before as $item) : ?>
                    <?php if ($item->sum == 0) continue; ?>
                    <?php $sum = $sum + $item->sum; $tasks = $tasks + $item->tasks;?>
                    <tr>
                        <td style="text-align:left;"><?php echo $item->customer_name; ?></td>
                        <td><?php echo htmler::esc_price($item->sum); ?></td>
                    <tr>
                <?php endforeach; ?>
                </tbody>
            </table>        
            <div class="block-item" style="margin-top: 10px;font-size: 10px;">
                <strong>Итого: </strong> <?php echo htmler::esc_price($sum); ?><br />
                <strong>Выполнено заданий: </strong> <?php echo $tasks; ?>
            </div>        
        </div>
    <?php endif; ?>

    <div class="clr"></div>    
    <?php /*
    <div>        
        <h3 class="content-title" style="margin-bottom:0;">Новые задачи на вашей почте</h3>
        <p><a href="#" id="refresh_table"><i class="fa fa-refresh" aria-hidden="true"></i> <span id="response_status">Обновить</span></a> </p>        
        <table class="main-table mail-messages" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <!-- <th width="25"><input type="checkbox" onClick="toggle(this)"></th> -->
                    <th width="60">Дата</th>
                    <th width="150">Отправитель</th>
                    <th style="text-align:left;">Тема</th>
                </tr>
            </thead>
            <tbody id="response"></tbody>
        </table>
    </div>

    <div class="clr"></div>  
    */ ?>
    <div class="buttons">
        <a href="/admin/tasks/items/create" title="Создать новый элемент">Создать</a>
        <a href="#" onClick="return submitForm(itemsForm, '/duplicate');" title="Скопировать">Скопировать</a>
        <a href="#" onClick="return submitForm(itemsForm, '/paided');" title="Оплачено">Оплачено</a>
        <a href="#" onClick="return submitForm(itemsForm, '/delete');" title="Удалить элементы">Удалить</a>
    </div>
</form>

    <div class="buttons">
        <a href="/admin/tasks/items/create" title="Создать новый элемент">Создать задачу</a>
    </div>
        
</form>