<?php

$bills_model = $this->getModel('bills');
$user = User::getUserData();

$params = array();
$params['title'] = 'Счет от '.date("d.m.y");
$params['create_date'] = date("Y-m-d H:i:s");
$params['modify_date'] = date("Y-m-d H:i:s");
$params['author_id'] = $user->id;
$params['description'] = '';
$params['state'] = 1;
$params['params'] = serialize(array());

$bill_id = $bills_model->saveNewItem($params);

if ($bill_id > 0) {

	foreach ($items as $key => $item) {
		
		$bills_model->addItemToBill($bill_id, $item->id);
		echo $bill_id.' - '.$item->id;

	}

	Main::Redirect('/admin/tasks/bills/edit/'.$bill_id, 'Счет успешно создан');

}

Main::Redirect('/admin/tasks/bills/export', 'Произошла ошибка при создании счета');