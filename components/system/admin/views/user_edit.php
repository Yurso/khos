<div class="usermanager-edit" style="position:relative;">
    <h2 class="content-title">Информация о пользователе</h2>
    <form method="post" action="/admin/system/user/save" class="adminform" enctype="multipart/form-data">
        
        <div class="block" style="width:300px;">
            <div class="block-title">Данные пользователя</div>
            <div class="block-item">
                <label>Имя:</label><br />
                <input type="text" name="name" value="<?php echo $this->user->name; ?>" disabled />
            </div>
            <div class="block-item">
                <label>E-mail для авторизации:</label><br />
                <input type="text" name="email" value="<?php echo $this->user->email; ?>" disabled />
            </div>
            <div class="block-item">
                <label>Новый пароль:</label><br />
                <input type="password" name="password" value="" />
            </div>
            <div class="block-item">
                <label>Еще раз новый пароль:</label><br />
                <input type="password" name="password2" value="" />
            </div>
        </div>

        <div class="block" style="width:300px;">
            <div class="block-title">Дополнительная информация</div>
            <div class="block-item">
                <label>Агенство:</label><br />
                <strong><?php echo $this->user->agency_name; ?></strong>
            </div>
            <div class="block-item">
                <label>Должность:</label><br />
                <input type="text" name="position" value="<?php echo $this->user->position; ?>" />
            </div>
            <div class="block-item">
                <label>Телефон:</label><br />
                <input type="text" name="phone" value="<?php echo $this->user->phone; ?>" />
            </div>
            <div class="block-item">
                <label>Сайт:</label><br />
                <input type="text" name="website" value="<?php echo $this->user->website; ?>" />
            </div>
            <div class="block-item">
                <label>Рабочий E-mail:</label><br />
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

        <input type="hidden" name="id" value="<?php echo $this->user->id; ?>">

        <div class="buttons">
            <input type="submit" value="Сохранить">           
        </div>

    </form>    
</div>