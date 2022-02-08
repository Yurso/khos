<h2 class="content-title">Найтройки</h2>

<form action="/admin/system/settings/save" method="post" class="adminform">

	<div class="block" style="width:300px;">
		<div class="block-title">Основное</div>
		<div class="block-item">
	        <label>Название сайта:</label><br />
	        <input type="text" name="SiteName" value="<?php echo $this->conf->SiteName; ?>" required>
	    </div>
	    <div class="block-item">
	        <label>Базовый URL:</label><br />
	        <input type="text" name="BaseURL" value="<?php echo $this->conf->BaseURL; ?>" required>
	    </div>
	    <div class="block-item">
	        <label>Email сайта:</label><br />
	        <input type="text" name="SiteMail" value="<?php echo $this->conf->SiteMail; ?>">
	    </div>
	    <div class="block-item">
	        <label>Email для оповещений:</label><br />
	        <input type="text" name="NotificationMail" value="<?php echo $this->conf->NotificationMail; ?>">
	    </div>
	    <div class="block-item">
	        <label>MetaDescription:</label><br />
	        <textarea name="MetaDescription" rows="7" style="width:93%;"><?php echo $this->conf->MetaDescription; ?></textarea>	        
	    </div>
	    <div class="block-item">
	        <label>MetaKeywords:</label><br />
	        <textarea name="MetaKeywords" rows="7" style="width:93%;"><?php echo $this->conf->MetaKeywords; ?></textarea>	        
	    </div>
	</div>

	<div class="block">
		<div class="block-title">Дополнительно</div>
	    <div class="block-item">
            <label>Включить API:</label><br />
            <?php echo htmler::booleanSelect($this->conf->EnableApi, 'EnableApi'); ?>
        </div>
        <div class="block-item">
            <label>Шаблон по-умолчанию:</label><br />
            <select name="DefaultTheme">
            	<?php foreach ($this->themes as $theme) : ?>
            		<option value="<?php echo $theme->name; ?>" <?php if ($theme->name == $this->conf->DefaultTheme) echo ' selected';?>><?php echo $theme->name; ?></option>
            	<?php endforeach; ?>	
            </select>            
        </div>
       	<div class="block-item">
            <label>Главная страница:</label><br />
            <input type="text" name="fpdirection" value="<?php echo $this->conf->fpdirection; ?>">
        </div>
        <div class="block-item">
            <label>Время активности сессии:</label><br />
            <input type="text" name="sessions_live_time" value="<?php echo $this->conf->sessions_live_time; ?>">
        </div>
	</div>

	<div class="block">
		<div class="block-title">База данных</div>
		<div class="block-item">
	        <label>Тип базы данных:</label><br />	        
	        <input type="text" name="dbtype" value="<?php echo $this->conf->dbtype; ?>" required>
	    </div>
	    <div class="block-item">
	        <label>Сервер базы данных:</label><br />
	        <input type="text" name="dbhost" value="<?php echo $this->conf->dbhost; ?>" required>
	    </div>
	    <div class="block-item">
	        <label>Имя базы данных:</label><br />
	        <input type="text" name="dbname" value="<?php echo $this->conf->dbname; ?>">
	    </div>
	    <div class="block-item">
	        <label>Имя пользователя:</label><br />
	        <input type="text" name="dbuser" value="<?php echo $this->conf->dbuser; ?>">
	    </div>
	    <div class="block-item">
	        <label>Пароль:</label><br />
	        <input type="password" name="dbpassword" value="<?php echo $this->conf->dbpassword; ?>">
	    </div>
	    <div class="block-item">
	        <label>Префикс:</label><br />
	        <input type="text" name="dbprefix" value="<?php echo $this->conf->dbprefix; ?>">
	    </div>
	    <div class="block-item">
	        <label>Строка замены на префикс:</label><br />
	        <input type="text" name="dbreplace" value="<?php echo $this->conf->dbreplace; ?>">
	    </div>
	</div>

	<div class="buttons">
		<input type="submit" value="Сохранить">
	</div>

</form>