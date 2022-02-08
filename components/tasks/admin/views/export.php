<script type="text/javascript">
    
    $(document).ready(function(){

        $( "#current_month" ).click(function() {  

            var daysInMonth = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

            var date = new Date();

            var year = date.getFullYear();
            var month = date.getMonth();
            var last_day = daysInMonth[month];

            month = month + 1;
            if (month < 10) month = "0" + month;

            $("#date_from").val(year+"-"+month+"-01 00:00:00");
            $("#date_to").val(year+"-"+month+"-"+last_day+" 23:59:59");

            return false;

        });

        $( "#before_month" ).click(function() {  

            var daysInMonth = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

            var date = new Date();

            var year = date.getFullYear();
            var month = date.getMonth();

            if (month == 0) {
                year = year - 1;
                month = 11;
            }

            var last_day = daysInMonth[month-1];

            month = month;
            if (month < 10) month = "0" + month;

            $("#date_from").val(year+"-"+month+"-01 00:00:00");
            $("#date_to").val(year+"-"+month+"-"+last_day+" 23:59:59");

            return false;

        });

        $( "#all_states_and_paids" ).click(function() {  

            $("#state0").prop( "checked", true );
            $("#state1").prop( "checked", true );
            $("#paid1").prop( "checked", true );
            $("#paid0").prop( "checked", true );
            $("#deleted1").prop( "checked", true );
            $("#deleted0").prop( "checked", true );

            return false;

        });

        $( "#all_customers" ).click(function() {  

            $(".customers").prop( "checked", true );

            return false;

        });

        $( "#clear_customers" ).click(function() {  

            $(".customers").prop( "checked", false );

            return false;

        });

    });

</script>

<h2 class="content-title"><?php echo $this->tmpl_page_title; ?></h2>

<?php include('menu.php'); ?>

<p>&nbsp;</p>

<form method="post" action="/admin/tasks/export/submit" class="adminform" name="itemForm" enctype="multipart/form-data">

    <div class="block" style="">
        <div class="block-title">Период</div>
        <div class="block-item">
            <label>С даты:</label><br />
            <input type="input" name="date_from" id="date_from" class="datepicker">
        </div>
        <div class="block-item">
            <label>По дату:</label><br />
            <input type="input" name="date_to" id="date_to" class="datepicker">
        </div>
        <div class="block-item">
            <span class="small"><a href="#" id="current_month">Текущий месяц</a></span> /
            <span class="small"><a href="#" id="before_month">Предыдущий месяц</a></span>
        </div>
    </div>

    <div class="block">
        <div class="block-title">Клиенты</div>
        <div class="items" style="max-height: 300px;overflow-y: scroll;">
            <?php foreach ($this->customers as $customer) : ?>
                <div class="block-item">
                    <input type="checkbox" name="customers[]" value="<?php echo $customer->id; ?>" class="customers" id="customer<?php echo $customer->id; ?>" />
                    <label for="customer<?php echo $customer->id; ?>"><?php echo $customer->name; ?></label>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="block-item">
            <span class="small"><a href="#" id="all_customers">Выбрать все</a></span> /
            <span class="small"><a href="#" id="clear_customers">Очистить</a></span>
        </div>
    </div>

    <div class="block" style="width:230px;">
        <div class="block-title">Дополнительные признаки</div>
        <div class="block-item">
            <input type="checkbox" name="state[]" value="1" id="state1" checked="checked" />
            <label for="state1">Выполнено</label>
        </div>
        <div class="block-item">
            <input type="checkbox" name="state[]" value="0" id="state0" />
            <label for="state0">Не выполнено</label>
        </div>
        <div class="block-item">
            <input type="checkbox" name="paid[]" value="1" id="paid1" />
            <label for="paid1">Оплачено</label>
        </div>
        <div class="block-item">
            <input type="checkbox" name="paid[]" value="0" id="paid0" checked="checked" />
            <label for="paid0">Не оплачено</label>
        </div>
        <div class="block-item">
            <input type="checkbox" name="deleted[]" value="1" id="deleted1" />
            <label for="paid1">Удалено</label>
        </div>
        <div class="block-item">
            <input type="checkbox" name="deleted[]" value="0" id="deleted0" checked="checked" />
            <label for="paid0">Не удалено</label>
        </div>         
        <div class="block-item">
            <span class="small"><a href="#" id="all_states_and_paids">Выбрать все</a></span>
        </div>
    </div>

    <div class="block" style="">
        <div class="block-title">Формат</div>
        <div class="block-item">
            <select name="format">
                <?php foreach ($this->formats as $key => $format) : ?>
                    <option value="<?php echo $format; ?>"><?php echo $format; ?></option>                
                <?php endforeach; ?>
            </select>            
        </div>
    </div>

    <div class="buttons">
        <input type="submit" name="export" value="Экспорт">         
    </div>

</div>