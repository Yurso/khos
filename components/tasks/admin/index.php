<?php 

Class TasksIndexController extends ControllerBase {

    public function index() {  

       	$model = $this->getModel('items');

       	$summary = $model->statisticsSummary();
       	
       	$date1 = date("Y-m-01 00:00:00");
       	$date2 = date("Y-m-d 23:59:59", strtotime(date("Y-m-01", strtotime($date1." +1 month"))." -1 day"));
       	$current_month = $model->statisticsPreiod($date1, $date2);

       	$date1 = date("Y-m-01 00:00:00", strtotime($date1." -1 month"));
       	$date2 = date("Y-m-d 23:59:59", strtotime(date("Y-m-01", strtotime($date1." +1 month"))." -1 day"));
       	$month_before = $model->statisticsPreiod($date1, $date2);
            
            $date2 = date("Y-m-d 23:59:59", strtotime($date1." -1 day"));
            $date1 = date("2000-01-01 00:00:00");                       

            $all_before = $model->statisticsPreiod($date1, $date2);         

            // FILTERS STATE
            $model->setFilter('i.status', '=', 'new');            

            $model->initUserOrdering();

            $current_items = $model->getItems();
            
       	$tmpl = new template;
       	
       	$tmpl->setVar('summary', $summary);
       	$tmpl->setVar('current_month', $current_month);
       	$tmpl->setVar('month_before', $month_before);
            $tmpl->setVar('all_before', $all_before);
            $tmpl->setVar('current_items', $current_items);
            $tmpl->setVar('statuses', $model->getStatuses());
       	
       	$tmpl->setTitle('Рабочий стол');
       	
       	$tmpl->display('index');

    }

}