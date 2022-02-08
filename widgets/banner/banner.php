<?php
Class BannerWidget Extends WidgetBase {

	public function display() {

		$class = '';
		if (isset($this->params['class'])) {
			$class = $this->params['class']; 
		}
		
		$result = '<div class="wbanner '.$class.'">';

		if (isset($this->params['image'])) {

            $width = '';
            if (isset($this->params['width'])) {
                $width = 'width="'.$this->params['width'].'"';
            }

			$result .= '<img src="'.$this->params['image'].'" alt="'.$this->title.'" '.$width.'>';
		}

		$result .= '</div>';

		echo $result;

	}

    public function buildParamsForm($params) {

    	$result = '';

    	// parameter default value
        $class = '';
        if (isset($params['class'])) {
        	$class = $params['class'];
        }
        // parameter view
        $result .= '<div class="block-item">';
        $result .= '	<label>Класс:</label><br>';
       	$result .= htmler::inputText('params[class]', $class);
       	$result .= '</div>';

        // parameter default value
        $width = '100%';
        if (isset($params['width'])) {
            $width = $params['width'];
        }
        // parameter view
        $result .= '<div class="block-item">';
        $result .= '    <label>Ширина:</label><br>';
        $result .= htmler::inputText('params[width]', $width);
        $result .= '</div>';

       	// parameter default value
       	$text = '';
    	if (isset($params['text'])) {
    		$text = $params['text'];
    	}
    	// parameter view
    	$result .= '<div class="block-item" style="width:400px;">';
        $result .= '	<label>Изображение:</label><br>';
        if (isset($params['image']) && !empty($params['image'])) {
            $result .= '<p><img src="'.$params['image'].'" alt="image" width="100%"></p>';
            $result .= '<input type="hidden" name="params[image]" value="'.$params['image'].'">';
        }
        $result .= '	<input type="file" name="image">';
        $result .= '</div>';

    	return $result;

    }

    public function onBeforeWidgetSave() {

        // Saving image file
        $image = $_FILES['image_file'];

        $image_types = array('image/jpeg', 'image/gif', 'image/pjpeg', 'image/png');     
        $path = SITE_PATH . 'public' . DIRSEP . 'images' . DIRSEP . 'banners' . DIRSEP;
        $url_path = '/public/images/banners/';

        if ($image['error'] == UPLOAD_ERR_OK && in_array($image['type'], $image_types)) {

            if (!is_dir($path)) {            
                mkdir($path, 0755, true);
            }

            $filename = Main::str2url($image['name']);

            if (move_uploaded_file($image['tmp_name'], $path.$filename)) {
                $_POST['params']['image'] = $url_path.$filename;
            }

        }

    }
	
}