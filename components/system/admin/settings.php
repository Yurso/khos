<?php
Class SystemSettingsController Extends ControllerBase {

	function index() {

		$conf = new Configuration;

		$model = $this->getModel('configuration');

		$tmpl = new Template;

		$tmpl->setVar('conf', $conf);
		$tmpl->setVar('themes', $model->getThemesList());

		$tmpl->setTitle('Общие настройки');

		$tmpl->display('settings');

	}

	function save() {

		$conf = new Configuration;
		
		if (isset($_POST['SiteName'])) {

			// Replace conf with new values
			foreach ($conf as $key => $value) {

				if(isset($_POST[$key])) {
					$conf->$key = $_POST[$key];								
				}

			}
		
			$text  = '<?php' . "\n";
			$text .= 'Class Configuration {' . "\n";

			foreach ($conf as $key => $value) {			
				$text .= '	public $' . $key . ' = ' . "'" . $value . "';" . "\n";
			}

			$text .= '}';

			$file = SITE_PATH . 'classes' . DIRSEP . 'configuration.php';

			if (file_put_contents($file, $text)) {
				Main::redirect('/admin/system/settings', 'Изменения успешно сохранены.');
			} else {
				Main::redirect('/admin/system/settings', 'Ошибка! Не удалось записать изменения в файл.');
			}
		}

	}

}