<?php
Class HTMLer {

	// Generate boolean select
	static public function booleanSelect($value = 0, $name = 'booleanSelect', $class = 'booleanSelect', $disabled = false) {

		$html  = '<select name="' . $name . '" class="' . $class . '"';

		if ($disabled) $html .= ' disabled';
		
		$html .= '>';		
		
		if ($value == 1) {
			$html .= '	<option value="1" selected>Да</option>';		
			$html .= '	<option value="0">Нет</option>';	
		} else {
			$html .= '	<option value="1">Да</option>';		
			$html .= '	<option value="0" selected>Нет</option>';	
		}

		$html .= '</select>';

		return $html;

	}

	// Generate select values list 
	static public function SelectList($values, $name = 'SelectList', $class = 'SelectList', $first = '', $selected = NULL, $disabled = false, $required = false) {

		$html  = '<select name="' . $name . '" class="' . $class . '"';
		if ($disabled) $html .= ' disabled';
		if ($required) $html .= ' required';
		$html .='>';

		if (!empty($first)) {

			$html .= '<option value=""> - ' . $first . ' - </option>';

		}

		foreach ($values as $key => $value) {

			if ($key == $selected) {
				$sel = ' selected';
			} else {
				$sel = '';
			}

			$html .= '<option value="' . $key . '"' . $sel . '>' . $value . '</option>';

		}

		$html .= '</select>';

		return $html;

	}

	static public function selectListByObjectsArray($items, $value_field = 'id', $title_field = 'title', $name = '', $selected = NULL, $attr = '') {

		$html = '<select name="'.$name.'" '.$attr.'>';

		foreach ($items as $item) {

			$value = $item->$value_field;
			$title = $item->$title_field;

			$option_attr = '';

			if ($value == $selected) {
				$option_attr = 'selected';
			}
			
			$html .= '<option value="'.$value.'" '.$option_attr.'>'.$title.'</option>';

		}

		$html .= '</select>';

		return $html;

	}

	static public function radioList($values, $name = 'radioList', $selected = NULL, $vertical = false) {

		$html = '';

		foreach ($values as $key => $value) {
			
			if ($key == $selected) {
				$checked = ' checked';
			} else {
				$checked = '';
			}

			$html .= '<input type="radio" name="'.$name.'" value="'.$key.'"'.$checked.'> '.$value;

			if ($vertical) $html .= '<br />';

		}

		return $html;

	}

	static public function checkboxList($values, $name = 'checkbox_list', $class = "checkbox_list", $selected = array(), $vertical = false) {

		$html = '';

		foreach ($values as $key => $value) {

			$checked = "";
			
			if (in_array($key, $selected)) {
				$checked = " checked";
			}

			$id = $class.$key;

			$html .= '<input type="checkbox" class="'.$class.'" id="'.$id.'" name="'.$name.'" value="'.$key.'"'.$checked.'>';
			$html .= '<label for="'.$id.'">'.$value.'</label>';

			if ($vertical) $html .= '<br />';

		}

		return $html;

	}

	static public function inputText($name, $value, $attr = '', $required = false, $disabled = false, $autofocus = false) {

		$html  = '<input type="text" name="'.$name.'" value="'.$value.'" '.$attr;

		if ($required) $html .= ' required';
		if ($disabled) $html .= ' disabled';
		if ($autofocus) $html .= ' autofocus';

		$html .= '>';

		return $html;

	}

	static public function YesNo($value) {

		if ($value > 0) {
			$html = 'Да';
		} else {
			$html = 'Нет';
		} 

		return $html;

	}

	static public function RadioTree($items, $name) {

		$html = '<ul>';

		foreach ($items as $key => $item) {
			
			$html .= '<li>';

			$html .= '<input type="radio" name="'.$name.'" value="'.$item->id.'" />' . $item->name . '<br />';

			$html .= self::RadioTree($item->childrens, $name);

			$html .= '</li>';

		}

		$html .= '</ul>';

		return $html;

	}

	static public function SelectTree($items, $name, $value_key = 'value', $title_key = 'title', $selected = '') {

		$html  = '<select name="'.$name.'" size="1">';

		$html .= self::OptionsTree($items, $value_key, $title_key, $selected);

		$html .= '</select>';

		return $html;

	}

	static public function OptionsTree($items, $value_key, $title_key, $selected, $prefix = '') {

		$html = '';

		foreach ($items as $key => $item) {

			if (gettype($item) == 'object') {

				$value = $item->$value_key;
				$title = $item->$title_key;

			} elseif (gettype($item) == 'array') {

				$value = $item[$value_key];
				$title = $item[$title_key];

			} else {

				continue;

			}

			if ($value == $selected) {
				$sel = ' selected';
			} else {
				$sel = '';
			}
			
			$html .= '<option value="'.$value.'"'.$sel.'>'.$prefix.' '.$title.'</option>';

			if (isset($item->childrens)) {

				$prefix .= '--';

				$html .= self::OptionsTree($item->childrens, $value_key, $title_key, $selected, $prefix);			
				
			}

		}

		return $html;

	}

	public static function tableSort($column, $title) {

		$sort = 'ASC';
		$sort_img = '';

		$ordering = array('column' => '', 'sort' => 'ASC');
		$ordering = Main::getState('current_ordering', $ordering);

	    if ($column == $ordering['column']) {
	    	
	    	$sort_img = ' <img src="/public/images/system/sort'.$ordering['sort'].'.png" alt="">';    

		    if ($ordering['sort'] == 'ASC') $sort = 'DESC';
		    if ($ordering['sort'] == 'DESC') $sort = 'ASC';

	    }

		$uri = '?ordering_column='.$column.'&ordering_sort='.$sort;

		$result = '<a href="'.$uri.'">'.$title.$sort_img.'</a>';

		return $result;

	}

	public static function tableFilter($column, $title) {

		$html = '';
		$value = '';

		$filters = Main::getState('filters');

		if (isset($filters[$column])) {
			$value = $filters[$column]; 
		}

		$html .= '<div class="table-filter">';
		$html .= '	<label>' . $title . '</label>';
		$html .= '	<input type="text" value="'.$value.'" name="filter['.$column.']" />';
		$html .= '</div>';

		return $html;

	}

	public static function tableFilters($filters) {

		$result  = '<div class="table-filters">';
		$result .= '	<h4 style="">Фильтры</h4>';
		$result .= '	<form method="post" name="filtersForm" id="filtersForm">';

		foreach ($filters as $filter) {

			if (count($filter->values)) {

				$result .= '		<label>'.$filter->title.':</label>';
				$result .= '		<select name="filters['.$filter->column.']" onchange="filtersForm.submit()">';				

					if ($filter->first_empty_value)
						$result .= '			<option value="">Все</option>';					

					foreach ($filter->values as $key => $value) {

						$selected = '';

						if ($filter->value == $key) {
							$selected = 'selected';
						}

						$result .= '			<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
					}					

				$result .= '		</select>';

			} else {
				
				$result .= '		<label>'.$filter->title.':</label>';
				$result .= '		<input type="text" name="filters['.$filter->column.']" value="'.$filter->value.'" />';

			}

		}

		$result .= '		<input type="submit" value="Искать" />';		
		$result .= '		<input type="button" value="Очистить" class="form-reset" />';		
		$result .= '	</form>';
		$result .= '</div>';

		return $result;		

	}

	public static function _tableFilters($filters) {

		// gets advanced filters to another array
		$advansed_filters = array();
		$show_advansed_filters = false;

		foreach ($filters as $key => $filter) {

			if ($filter->hidden) {
				unset($filters[$key]);
				continue;
			}
			
			if ($filter->advansed == true) {
				// Show advansed filters if there have input values
				if (!empty($filter->value)) $show_advansed_filters = true;				
				// Copy filter to advansed array
				$advansed_filters[] = $filter;
				// Delete this filter from main array
				unset($filters[$key]);
			}

		}
		
		if (!count($filters)) {
			return '';
		}

		$result  = '<div class="block-filters">';		
		$result .= '<div class="block-title">Фильтры</div>';
		$result .= '<form method="post" name="filtersForm" id="filtersForm" action="'.$_SERVER['SCRIPT_URL'].'">';

		// Main filters array
		foreach ($filters as $filter) {

			$result .= '<div class="block-item">';

			if (count($filter->values)) {

				$result .= '<label>'.$filter->title.':</label><br />';
				$result .= '<select name="filters['.$filter->name.']" onchange="filtersForm.submit()">';				

					if ($filter->first_empty_value)
						$result .= '<option value="">Все</option>';					

					foreach ($filter->values as $key => $value) {

						$selected = '';

						if ($filter->value == $key) {
							$selected = 'selected';
						}

						$result .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
					}					

				$result .= '</select>';

			} else {
				
				$result .= '<label>'.$filter->title.':</label><br />';
				$result .= '<input type="text" name="filters['.$filter->name.']" value="'.$filter->value.'" />';

			}

			$result .= '</div>'; #block-item

		}

		$result .= '<div class="clr"></div>';

		// Advansed filters array
		if ($show_advansed_filters) {
			$result .= '<div class="advansed_filters" style="display:block;">';	
		} else {
			$result .= '<div class="advansed_filters" style="display:none;">';	
		}		
		
		foreach ($advansed_filters as $filter) {

			$result .= '<div class="block-item">'; #block-item 

			if (count($filter->values)) {

				$result .= '<label>'.$filter->title.':</label><br />';
				$result .= '<select name="filters['.$filter->name.']" onchange="filtersForm.submit()">';				

					if ($filter->first_empty_value)
						$result .= '<option value="">Все</option>';					

					foreach ($filter->values as $key => $value) {

						$selected = '';

						if ($filter->value == $key) {
							$selected = 'selected';
						}

						$result .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
					}					

				$result .= '</select>';

			} else {
				
				$result .= '<label>'.$filter->title.':</label><br />';
				$result .= '<input type="text" name="filters['.$filter->name.']" value="'.$filter->value.'" />';

			}

			$result .= '</div>'; #block-item

		}

		$result .= '</div>';

		$result .= '<div class="clr"></div>';	

		$result .= '<input type="hidden" name="limitstart" value="0" />';	
		$result .= '<input type="submit" value="Искать" />';		
		$result .= '<input type="button" value="Очистить" class="form-reset" />';

		if (count($advansed_filters)) {
			$result .= '<input type="button" value="Расширенный фильтр" class="toggle_advansed_filters" />';	
		}	
		
		$result .= '</form>';
		$result .= '</div>';

		return $result;		

	}

	public static function levelPrefix($level, $prefix = '-- ') {

		$html = '';

		for ($i = 1; $i < $level; $i++) {
			$html .= $prefix;
		}

		return $html;

	}

	public static function esc_price($number, $postfix = ' руб.', $prefix = '') {

		$number = intval($number);

		$result  = $prefix;
		$result .= number_format($number, 0, ',', ' '); 
		$result .= $postfix;

		return $result;

	}

	public static function esc_date($date) {

		return date("d.m.y H:i", strtotime($date));

	}

	public static function highlight($string, $values) {

		if (gettype($values) == 'array') {

			foreach ($values as $key => $value) {
				
				$string = str_ireplace($value, '<span class="highlight">'.$value.'</span>', $string);

			}

		} else {

			$string = str_ireplace($values, '<span class="highlight">'.$values.'</span>', $string);

		}

		return $string;

	}

	public static function highlightStr($haystack, $needles) {
	    
	    // return $haystack if there is no highlight color or strings given, nothing to do.
	    if (strlen($haystack) < 1) {
	        return $haystack;
	    }

	    foreach ($needles as $needle) {
	    	    
		    preg_match_all("/$needle+/i", $haystack, $matches);
		    
		    if (is_array($matches[0]) && count($matches[0]) >= 1) {
		        foreach ($matches[0] as $match) {
		            $haystack = str_replace($match, '<span class="highlight">'.$match.'</span>', $haystack);
		        }
		    }

		}
	    
	    return $haystack;

	}

	public static function formButtons($buttons, $form_name) {

		$result = '';

		foreach ($buttons as $button) {
			$onclick = "return submitForm($form_name, '".$button['action']."')";
			$result .= '<a href="#" onClick="'.$onclick.'" title="'.$button['title'].'">'.$button['title'].'</a>';
		}

		return $result;

	}

}