<?php
Class LandingHtmlType extends LandingTypeBase {

	public function getEditForm($item) {

		$result =  '
			<div class="block-item">
			    <label>Текст:</label><br />
			    <textarea class="" name="content" id="content">'.htmlspecialchars($item->content).'</textarea>
			</div>

			<!-- Create a simple CodeMirror instance -->
			<link rel="stylesheet" href="/public/js/codemirror-5.25.0/lib/codemirror.css">
			<script src="/public/js/codemirror-5.25.0/lib/codemirror.js"></script>
			<script src="/public/js/codemirror-5.25.0/addon/selection/selection-pointer.js"></script>
			<script src="/public/js/codemirror-5.25.0/mode/xml/xml.js"></script>
			<script src="/public/js/codemirror-5.25.0/mode/javascript/javascript.js"></script>
			<script src="/public/js/codemirror-5.25.0/mode/css/css.js"></script>
			<script src="/public/js/codemirror-5.25.0/mode/htmlmixed/htmlmixed.js"></script>
			<script>
		      // Define an extended mixed-mode that understands vbscript and
		      // leaves mustache/handlebars embedded templates in html mode
		      var mixedMode = {
		        name: "htmlmixed",
		        scriptTypes: [{matches: /\/x-handlebars-template|\/x-mustache/i,
		                       mode: null}]
		      };
		      var editor = CodeMirror.fromTextArea(document.getElementById("content"), {
		        mode: mixedMode,
		        lineNumbers: true,
		        selectionPointer: true
		      });
		    </script>';

		return $result;

	}

	public function getParamsForm($params) {

		// SHOW TITLE
		$param_name = 'show_title';

		if (!isset($params[$param_name])) {
			$params[$param_name] = 1;
		}

		$result = '
			<div class="block-item">
	            <label>Показывать заголовок:</label><br />
	            '.htmler::booleanSelect($params[$param_name], 'params['.$param_name.']').'
	        </div>';

	    // FULLSCREEN
	    $param_name = 'fullscreen';

		if (!isset($params[$param_name])) {
			$params[$param_name] = 0;
		}

		$result .= '
			<div class="block-item">
	            <label>Во весь экран:</label><br />
	            '.htmler::booleanSelect($params[$param_name], 'params['.$param_name.']').'
	        </div>';

		return $result;

	}

	public function onBeforeItemSave() {

	}

	public function onAfterItemSave($item) {

	}

	public function onItemDelete($item) {
		
	}	

	public function onItemDuplicate($item) {
		
	}

}