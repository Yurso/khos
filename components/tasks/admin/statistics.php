<?php
Class TasksStatisticsController Extends stdController {

	public function index() {

		$tmpl = new template();

		$tmpl->display('statistics_menu');

	}

	public function paid_year() {

		$model = $this->getModel('statistics');

		$items = $model->getPaidByYearData('2016-01-01', '2018-12-31');

		$columns = array(
			'year' => array(
				'title' => 'Год',
				'th_style' => 'width:50px;',
				'td_style' => 'text-align:center;'
			),
			'customer_name' => array(
				'title' => 'Клиент',
				'th_style' => '',
				'td_style' => 'text-align:left;'
			),
			'price' => array(
				'title' => 'Сумма',
				'th_style' => 'width:50px;',
				'td_style' => 'text-align:center;'
			),
		);

		$tmpl = new template();

		$tmpl->setVar('items', $items);
		$tmpl->setVar('columns', $columns);

		$tmpl->display('statistics_paid_year');

	}

}