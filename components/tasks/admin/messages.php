<?php 

Class TasksMessagesController extends ControllerBase {

    public function index() {}

    public function get() {

        $messages = array();

        if (isset($_GET['task_id'])) {

            $task_id = intval($_GET['task_id']);

            if ($task_id > 0) {

                $m_messages = $this->getModel('messages');

                $m_messages->setFilter('task_id', '=', $task_id);
                
                $messages = $m_messages->getItems();

            }

        }

        $tmpl = new Template();
        $tmpl->setVar('messages', $messages);
        $tmpl->display('items_view_messages', 'ajax');

    }

    public function save() {

        $result = array(
            'success' => false,
            'messages' => array()
        );

        $m_messages = $this->getModel('messages');
        $m_items = $this->getModel('items');
        $user = Registry::get('user');

        if (isset($_POST['id']) && isset($_POST['message-text']) && !empty($_POST['message-text'])) {

            $task_id = intval($_POST['id']);

            if ($m_items->itemExist($task_id)) {

                $task_item = $m_items->getItem($task_id);

                // Task params
                $params = array();

                $columns = $m_items->getTableColumns();   

                foreach ($columns as $column) {
                    if (isset($_POST[$column->Field])) {                
                        if (gettype($_POST[$column->Field]) == 'array') {
                            $params[$column->Field] = serialize($_POST[$column->Field]);
                        } else {
                            $params[$column->Field] = trim($_POST[$column->Field]);
                        }
                    }
                } 

                // if status changed
                if (isset($task_item) && isset($_POST['status']) && $task_item->status != $_POST['status']) {

                    if ($_POST['status'] == 'paid' && !isset($_POST['paid_date'])) {
                        $params['paid_date'] = date("Y-m-d H:i:s");
                        //$params['paid'] = 1;
                    }

                    if ($_POST['status'] == 'paid' && isset($_POST['paid_date']) && $_POST['paid_date'] == '0000-00-00 00:00:00') {
                        $params['paid_date'] = date("Y-m-d H:i:s");
                        //$params['paid'] = 1;
                    }

                    if ($_POST['status'] == 'complete' && !isset($_POST['complete_date'])) {
                        $params['complete_date'] = date("Y-m-d H:i:s");
                    }

                    if ($_POST['status'] == 'complete' && isset($_POST['complete_date']) && $_POST['complete_date'] == '0000-00-00 00:00:00') {
                        $params['complete_date'] = date("Y-m-d H:i:s");
                    }

                }

                // saving task params
                if (!$m_items->SaveItem($task_id, $params)) { 
                    $result['messages'][] = 'Не удалось сохранить изменения по задаче';
                } 

                // Message params
                $params = array(
                    'user_id' => $user->id,
                    'task_id' => $task_id,
                    'name' => $user->name,
                    'text' => $_POST['message-text']
                );

                if ($m_messages->SaveNewItem($params)) {
                    $result['success'] = true;
                }

            } else {
                $result['messages'][] = 'Задачи с таким id не существует';
            }

        } else {
            $result['messages'][] = 'Не хватает данных';
        }

        echo json_encode($result, JSON_UNESCAPED_UNICODE);

    }

}