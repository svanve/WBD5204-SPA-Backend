<?php

namespace App\Controllers;

use App\Interfaces\BaseController;
use App\Traits\RouteGuards\UserOnly;
use App\Request;
use App\Models\Session;
use App\Models\Task;

class DoneController extends BaseController {
    use UserOnly;

    public function index(Request $request) 
    {
        $id = $this->user->getId();
        $task = new Task($this->db);
        $tasks = $task->getUserTasks($id, 2);

        $this->renderView('done',  [ 'tasks' => $tasks ]);
    }

    public function detail(Request $request)
    {
        $user_id = $this->user->getId();
        $task_id = $request->getInput()[0] ?? '0';// wieso '0'?

        $taskQuery = $this->db->query(
            'SELECT t.id, t.title, t.body, ti.image, u.alias ' . 
            'FROM tasks AS t ' .
            'LEFT JOIN tasks_images AS ti ON t.id = ti.task_id ' .
            'LEFT JOIN users AS u ON u.id = t.author_id ' .
            'WHERE t.author_id = :user_id AND t.id = :task_id', 
            [ ':user_id' => $user_id, ':task_id' => $task_id ]
        );
        $task = $taskQuery->results()[0];

        $taskStepsQuery = $this->db->query( 
            "SELECT body FROM tasks_steps WHERE task_id = :task_id",
            [ ":task_id" => $task[ "id" ] ]
        );

        $taskSteps = $taskStepsQuery->results();

        $task[ "steps" ] = $taskSteps;

        $this->renderJson(200, $task);
    }

    public function unCheck(Request $request)
    {
        $id = $this->user->getId();
        $task_id = $request->getInput()[0] ?? "0";
        
        //task als state=1('open') markieren
        $this->db->query(
            'UPDATE tasks ' . 
            'SET tasks.state_id = 1 ' . 
            'WHERE tasks.id = :task_id',
            [ ':task_id' => $task_id ]
        ); 

        $titleQuery = $this->db->query(
            'SELECT t.title ' .
            'FROM tasks AS t ' .
            'WHERE t.id = :task_id',
            [ ':task_id' => $task_id ]
        );       

        $title = $titleQuery->first()['title'];
    
        Session::flash('message', 'Du hast den Task "' . $title . '" erfolgreich wieder geöffnet.');
        $this->redirect('/done');
    }

    public function delete(Request $request)
    {
        $id = $this->user->getId();
        $task_id = $request->getInput()[0] ?? "0";
        
        $titleQuery = $this->db->query(
            'SELECT t.title ' .
            'FROM tasks AS t ' .
            'WHERE t.id = :task_id',
            [ ':task_id' => $task_id ]
        );       

        $title = $titleQuery->first()['title'];

        $this->db->query(
            'DELETE FROM tasks ' .  
            'WHERE tasks.id = :task_id',
            [ ':task_id' => $task_id ]
        ); 
    
        Session::flash('message', 'Du hast den Task "' . $title . '" unwiderruflich gelöscht.');
        $this->redirect('/done');
    }
}