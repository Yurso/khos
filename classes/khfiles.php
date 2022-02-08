<?php
Class KhFiles {

	public static function ScanFiles($dir, $pattern = '*.*', $recursion = false) {		
		
		$result = array();

		$filelist = scandir($dir);
		array_shift($filelist);
		array_shift($filelist);

		foreach ($filelist as $name) {
			
			if (is_dir($dir.$name)) {
				if ($recursion) 
					$result = array_merge($result, self::ScanFiles($dir.$name.DIRSEP, $pattern, true));									
				continue;
			}

			$file = array();
			$file['folder'] = str_replace(SITE_PATH, '/', $dir);
			$file['pathway'] = $dir;
			$file['name'] = $name;

			$result[] = $file;

		}

		return $result;

	}

}