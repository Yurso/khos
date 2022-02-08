
<?php
    $types = array('seller' => 'Продавец', 'buyer' => 'Покупатель');
?>

<script type="text/javascript">
   function close_window() {
      if (confirm("Close Window?")) {
        close();
      }
    }
</script>

<h2 class="content-title">Редактор клиентов</h2>

<form method="post" action="/admin/realty/customers/save" class="adminform" name="itemForm" enctype="multipart/form-data">

    <div class="block">
    	<div class="block-title">Основное</div>
    	<div class="block-item">
            <label>Имя:</label><br />
            <input type="text" name="name" value="<?php echo $this->item->name; ?>" required autofocus>
        </div>
        <div class="block-item">
            <label>Тип:</label><br />
            <?php echo htmler::SelectList($types, 'type', '', '', $this->item->type); ?>
        </div>
        <div class="block-item">
            <label>Адрес:</label><br />
            <input type="text" name="adress" value="<?php echo $this->item->adress; ?>">
        </div>
        <div class="block-item">
            <label>Телефон:</label><br />
            <input type="text" name="phone" value="<?php echo $this->item->phone; ?>">
        </div>
        <div class="block-item">
            <label>E-mail:</label><br />
            <input type="text" name="email" value="<?php echo $this->item->email; ?>">
        </div>        
        <div class="block-item">
            <label>День рождения:</label><br />
            <input type="text" class="datepicker_date" name="birthday" value="<?php echo $this->item->birthday; ?>">
        </div>
    </div>

	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>">

    <div class="buttons">
        <input type="submit" name="save" value="Сохранить">        
        <input type="submit" name="apply" value="Применить">
        <a href="/admin/realty/customers" title="Закрыть">Закрыть</a>        
    </div>
    
</form>

