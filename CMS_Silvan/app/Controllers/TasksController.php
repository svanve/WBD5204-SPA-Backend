<?php

namespace App\Controllers;

use App\Interfaces\BaseController;
use App\Traits\RouteGuards\UserOnly;
use App\Request;
use App\Models\Session;
use App\Models\Task;
use App\Models\FormValidation;
use App\Models\FileValidation;
use App\Models\Sanitization;

class TasksController extends BaseController {
    use UserOnly;

    public function index(Request $request) 
    {
        $id = $this->user->getId();
        $this->task = new Task($this->db);
        $tasks = $this->task->getUserTasks($id, 1);

        $this->renderView('tasks',  [ 'tasks' => $tasks ]);
    }

    public function detail(Request $request) {
        $user_id = $this->user->getId();
        $task_id = $request->getInput()[0] ?? '0';

        $task = new Task($this->db);
        $details = $task->getTaskDetails($user_id, $task_id);

        $this->renderJson(200, $details);
    }

    public function check(Request $request)
    {
        $id = $this->user->getId();
        $task_id = $request->getInput()[0] ?? "0";
        
        // task als state=2('done') markieren:
        $this->db->query(
            'UPDATE tasks ' . 
            'SET tasks.state_id = 2 ' . 
            'WHERE tasks.id = :task_id',
            [ ':task_id' => $task_id ]
        ); 

        $task = new TAsk($this->db);
        $title = $task->getTaskDetails($id, $task_id)['title'];

        $titleQuery = $this->db->query(
            'SELECT t.title ' .
            'FROM tasks AS t ' .
            'WHERE t.id = :task_id',
            [ ':task_id' => $task_id ]
        );       

        $title = $titleQuery->first()['title'];
    
        Session::flash('message', 'Du hast den Task "' . $title . '" als erledigt markiert.');
        $this->redirect('/tasks');
    }

    public function delete(Request $request)
    {
        $id = $this->user->getId();
        $task_id = $request->getInput()[0] ?? "0";
        
        $task = new Task($this->db);
        $details = $task->getTaskDetails($id, $task_id);
        $title = $details['title'];
        
        $this->db->query(
            'DELETE FROM tasks ' .  
            'WHERE tasks.id = :task_id',
            [ ':task_id' => $task_id ]
        ); 
    
        Session::flash('message', 'Du hast den Task "' . $title . '" unwiderruflich gelöscht.');
        $this->redirect('/tasks');
    }

    public function edit(Request $request) 
    {
        $id = $this->user->getId();
        $task_id = $request->getInput()[0] ?? "0";

        $task = new Task($this->db);
        $inputValues = $task->getValueAttr($task_id);
            
        Sanitization::sanitize($_POST);
        
        if(!empty($_POST)) {
            $formValidation = new FormValidation($this->db, $_POST);
            $formValidation->setRules([
                'title' => 'required|min:5|max:255', 
                'body' => 'required|min:10',
                'deadline' => 'required',
                'assigned' => 'required',
                'tasks_step' => 'min:5|max:200',
                'tasks_step2' => 'min:5|max:200',
                'tasks_step3' => 'min:5|max:200',
                'tasks_step4' => 'min:5|max:200',
                'tasks_step5' => 'min:5|max:200',
                'tasks_step6' => 'min:5|max:200'
            ]);

            $formValidation->validate();

            $fileValidation = new FileValidation($_POST);
            $fileValidation->setRules([
                'image' => 'type:image|maxsize:2097152'
            ]);

            if (!empty($_POST['image'])) {
                $fileValidation->validate();
            }

            if ($formValidation->fails() || $fileValidation->fails()) {
                return $this->renderView('edit', [
                    'errors' => array_merge(
                        $formValidation->getErrors(),
                        $fileValidation->getErrors()
                    ),
                    'taskData' => $inputValues
                ]);
            }

            $task->update($task_id, $_POST);
            $title = $task->getTaskDetails($id, $task_id)['title'];
            Session::flash('message', 'Du hast den Task "' . $title . '" erfolgreich überschrieben.');
            $this->redirect('/tasks');
        }

        
        $this->renderView( 'edit', [ 
            'taskData' => $inputValues
        ]);
    }
}