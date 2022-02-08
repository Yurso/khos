<?php
Class TasksMaintenanceController Extends ControllerBase {

	public function index() {

		$tmpl = new template;

		$tmpl->setTitle('Обслуживание');
		
		$tmpl->display('maintenance_index');

	}

	public function clear_files_on_date() {

		if (!isset($_POST['date_before']) OR empty($_POST['date_before'])) {
			main::redirect('/admin/tasks/maintenance', 'Не указана дата очистки');
		}

		$m_maintenance = $this->getModel('maintenance');
		$m_files = $this->getModel('files');

		$items = $m_maintenance->getFilesBeforeDate($_POST['date_before']);

		$i = 0;

		foreach ($items as $item) {
			
			$m_files->deleteItem($item->id);

			$i++;

		}

		main::redirect('/admin/tasks/maintenance', "Удалено файлов: $i");

	}

}