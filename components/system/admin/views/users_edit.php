<div class="usermanager-edit" style="position:relative;">
    <h2 class="content-title">Редактор пользователей</h2>
    <form method="post" action="/admin/system/users/save" class="adminform" enctype="multipart/form-data">
        
        <div class="block" style="width:300px;">
            <div class="block-title">Данные пользователя</div>
            <div class="block-item">
                <label>Имя</label><br />
                <input type="text" name="name" value="<?php echo $this->user->name; ?>" required autofocus />
            </div>
            <div class="block-item">
                <label>Логин</label><br />
                <input type="text" name="login" value="<?php echo $this->user->login; ?>" required />
            </div>
            <div class="block-item">
                <label>E-mail</label><br />
                <input type="text" name="email" value="<?php echo $this->user->email; ?>" required />
            </div>
            <div class="block-item">
                <label>Новый пароль</label><br />
                <input type="password" name="password" value="" />
            </div>
            <div class="block-item">
                <label>Еще раз новый пароль</label><br />
                <input type="password" name="password2" value="" />
            </div>
        </div>

        <div class="block" style="width:300px;">
            <div class="block-title">Дополнительная информация</div>           
            <div class="block-item">
                <label>Должность</label><br />
                <input type="text" name="position" value="<?php echo $this->user->position; ?>" />
            </div>
            <div class="block-item">
                <label>Телефон</label><br />
                <input type="text" name="phone" value="<?php echo $this->user->phone; ?>" />
            </div>
            <div class="block-item">
                <label>Сайт</label><br />
                <input type="text" name="website" value="<?php echo $this->user->website; ?>" />
            </div>
            <div class="block-item">
                <label>Рабочий email</label><br />
                <input type="text" name="work_email" value="<?php echo $this->user->work_email; ?>" />
            </div>
            <div class="block-item">
                <label>Фотография:</label><br />
                <div class="image-item">
                    <?php if (!empty($this->user->image)) : ?>
                        <img width="100" src="<?php echo $this->user->image; ?>" alt="">
                    <?php endif; ?><br />
                    <input type="file" name="image">    
                </div>
            </div>
        </div>
        
        <div class="block">
            <div class="block-title">Активция и доступ</div>
            <div class="block-item">
                <label>Доступ</label><br />
                <?php echo htmler::SelectTree($this->users_access, 'access', 'id', 'name', $this->user->access); ?>                
            </div>
            <div class="block-item">
                <label>Активирован</label><br />
                <?php echo htmler::booleanSelect((isset($this->user->activated)) ? $this->user->activated : 0, 'activated'); ?>
            </div>
        </div>

        <input type="hidden" name="id" value="<?php echo $this->user->id; ?>">

        <div class="buttons">
            <input type="submit" value="Сохранить">            
            <a href="/admin/system/users" title="Закрыть">Закрыть</a>
        </div>

    </form>    
</div>