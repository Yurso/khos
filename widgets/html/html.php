<?php
Class HtmlWidget Extends WidgetBase {

	public function display() {

		$class = '';
		if (isset($this->params['class'])) {
			$class = $this->params['class']; 
		}
		
		$result = '<div class="whtml '.$class.'">';

		if (isset($this->params['text'])) {
			$result .= $this->params['text'];
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
       	$text = '';
    	if (isset($params['text'])) {
    		$text = $params['text'];
    	}
    	// parameter view
    	$result .= '<div class="block-item">';
        $result .= '	<label>Текст:</label><br>';
        $result .= '	<textarea name="params[text]" class="wysywig">'.$text.'</textarea>';
        $result .= '</div>';

    	return $result;

    }

    public function onAfterWidgetSave() {

        echo 'hello world';

    }
	
}