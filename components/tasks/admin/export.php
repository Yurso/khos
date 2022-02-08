<?php
Class TasksExportController Extends ControllerBase {

	function index() {

		$customers_model = $this->getModel('customers');
		$customers = $customers_model->getItems();

		// scan formats templates folder
		$formats = array();		
		$export_dir = __DIR__.DIRSEP.'export'.DIRSEP;
		$files = scandir($export_dir);
		// unset not php files and clear file type
		foreach ($files as $key => $file) {
			if (stripos($file, '.php') === false) {
				unset($files[$key]);
			} else {
				$formats[] = str_replace('.php', '', $file);
			}
		}

		// set template variables
		$tmpl = new template;
		$tmpl->setVar('customers', $customers);
		$tmpl->setVar('formats', $formats);
		$tmpl->setTitle('Экспорт данных');
		$tmpl->display('export');

	}

	function submit() {

		// print_r($_POST);
		// exit();

		// scan formats templates folder
		$formats = array();		
		$export_dir = __DIR__.DIRSEP.'export'.DIRSEP;
		$files = scandir($export_dir);
		// unset not php files and clear file type
		foreach ($files as $key => $file) {
			if (stripos($file, '.php') === false) {
				unset($files[$key]);
			} else {
				$formats[] = str_replace('.php', '', $file);
			}
		}

		if (isset($_POST['format']) && in_array($_POST['format'], $formats)) {

			$model = $this->getModel('items');

			$items = $model->getItemsByParams($_POST);

			$file = $export_dir.$_POST['format'].'.php';

			if (is_file($file)) {
				include($file);
			}

		}

		// exit();

		// // $params = array();
		// // $params['date_from'] = $_POST['date_from'];
		// // $params['date_to'] = $_POST['date_to'];
		// // $params['customers'] = $_POST['customers'];
		// // $params['state'] = $_POST['state'];
		// // $params['paid'] = $_POST['paid'];
		// // $params['format'] = $_POST['format'];

		// $model = $this->getModel('items');

		// $items = $model->getItemsByParams($_POST);

		// header('Content-Type: text/csv; charset=UTF-8');		
		// header('Content-Disposition: attachment; filename="realty-export.csv"');

		// $sep = ";";
		// $csv = "";
		// // headers
		// $csv .= "Дата" . $sep;                                
  //       $csv .= "Заголовок" . $sep;
  //       $csv .= "Клиент" . $sep;
  //       $csv .= "Цена" . $sep;

  //       $csv .= "\r\n";

		// foreach ($items as $key => $item) {
			
		// 	// Выводим данные по каждой линии
		// 	$csv .= date("d.m.y", strtotime($item->date)) . $sep;                                
  //           $csv .= $item->type_title . ' - ' . $item->title . $sep;
  //           $csv .= $item->customer_name . $sep;
  //           $csv .= $item->price * $item->count . $sep;

  //           $csv .= "\r\n";

		// }

		// $csv = mb_convert_encoding($csv, 'cp1251', 'UTF-8');

		// echo $csv;	

	}

	function bill() {

		// basic includes
		$model = $this->getModel('bills');

		// Search formats in export directory
		$formats = array();		
		$export_dir = __DIR__.DIRSEP.'export'.DIRSEP;
		$files = scandir($export_dir);
		// unset not php files and clear file type
		foreach ($files as $key => $file) {
			if (stripos($file, '.php') === false) {
				unset($files[$key]);
			} else {
				$formats[] = str_replace('.php', '', $file);
			}
		}

		// Not find formats
		if (!count($formats)) {
			Main::Redirect('/admin/tasks/bills', 'В каталоге export не найдено ни одного файла формата экпорта');
		}

		// No bill_id value
		if (!isset($_GET['bill_id'])) {
			Main::Redirect('/admin/tasks/bills', 'Не указан номер счета');
		}

		// Wrong bill_id
		$bill_id = intval($_GET['bill_id']);	
		if (!$model->itemExist($bill_id)) {
			Main::Redirect('/admin/tasks/bills', 'Счета с таким id не существует');
		}

		// export if fromat slected or show format select form
		if (isset($_GET['format']) && in_array($_GET['format'], $formats)) {
			
			$format = $_GET['format'];							

			$items = $model->getBillItems($bill_id);

			$file = $export_dir.$format.'.php';

			include($file);

		} else {

			$tmpl = new template;
			$tmpl->setVar('formats', $formats);
			$tmpl->setVar('bill_id', $_GET['bill_id']);
			$tmpl->display('export_choose');

		}

	}

}