<div class="controller-menu">
    <a href="/admin/realty/objects?limitstart=0">Объекты</a>
    | <a href="/admin/realty/objects/archive?limitstart=0">Архив объектов</a>
    | <a href="/admin/realty/objects/trash?limitstart=0">Корзина объектов</a>
    <span class="mobile-only">|</span> <a href="/admin/realty_requests" class="mobile-only">Заявки</a>
    <?php if (User::getUserData('access_name') == 'administrator') : ?>
      | <a href="/admin/realty/agencys">Агентства</a>
      | <a href="/admin/realty/params">Параметры</a>
      | <a href="/admin/realty/params_values">Значения параметров</a>      
    <?php endif; ?>
</div>