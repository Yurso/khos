<?php
Class RealtyImagesCleanerController Extends ControllerBase {

	public function index() {

		$tmpl = new template;
		$tmpl->display('admin_realty_images_cleaner');

	}

	public function tmp() {

		$dir = SITE_PATH . 'public' . DIRSEP .'images' . DIRSEP . 'tmp';
		$files = scandir($dir);

		array_shift($files);
		array_shift($files);

		foreach ($files as $key => $filename) {
			$file = $dir.DIRSEP.$filename;
			echo "Файл $filename в последний раз был изменен: " . date("Ymd", filectime($file)) . "\n";
			if (!file_exists($file)) {
				continue;
			}
			if (date("Ymd", filectime($file)) == date("Ymd")) {
				continue;
			}
			if (unlink($file)) {
				echo "Файл $filename успешно удален";
			} else {
				echo "Не удалось удалить файл $filename";
			}
		}
 
	}

	public function old() {

		$model = $this->getModel('realty_images');
		$images = $model->getNoUsedImages();

		//print_r($images);

		foreach ($images as $key => $image) {
			if($model->deleteItem($image->id)) {
				echo "Файл $image->image_name успешно удален";
			} else {
				echo "Не удалось удалить файл $image->image_name";
			}
		}

	}

	public function trash() {

		$model = $this->getModel('realty_images');
		$images = $model->getObjectInTrashImages();

		print_r($images);

		$size = 0;
		$dir = SITE_PATH . 'public' . DIRSEP .'images' . DIRSEP . 'realty' . DIRSEP;

		foreach ($images as $key => $image) {

			$file = $dir.$image->image_name;

			if (file_exists($file)) {
				$size = $size + filesize($file);
			}
			// if($model->deleteItem($image->id)) {
			// 	echo "Файл $image->image_name успешно удален";
			// } else {
			// 	echo "Не удалось удалить файл $image->image_name";
			// }
		}

		$size = $size / 1024 / 1024;

		echo "Размер найденных файлов: $size";

	}

}