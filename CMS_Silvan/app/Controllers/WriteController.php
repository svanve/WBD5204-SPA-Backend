<?php
namespace App\Controllers;

use App\request;
use App\Models\Task;
use App\Interfaces\BaseController;
use App\Models\FileValidation;
use App\Models\FormValidation;
use App\Traits\RouteGuards\UserOnly;

class WriteController extends BaseController {
    use UserOnly;

    public function index(Request $request)
    {
        if (!$request->hasInput()) {
            $this->renderView('write');
        }

        $formData = $request->getInput();
        
        $formValidation = new FormValidation($this->db, $formData);
        $formValidation->setRules([
            'title' => 'required|min:5|max:255|available:tasks', 
            'body' => 'required|min:10',
            'deadline' => 'required',
            'assigned' => 'required',
            'tasks_step' => 'min:5|max:70',
            'tasks_step2' => 'min:5|max:70',
            'tasks_step3' => 'min:5|max:70',
            'tasks_step4' => 'min:5|max:70',
            'tasks_step5' => 'min:5|max:70',
            'tasks_step6' => 'min:5|max:70'
        ]);

        $formValidation->validate();

        $fileValidation = new FileValidation($formData);
        $fileValidation->setRules([
            'image' => 'type:image|maxsize:2097152'
        ]);

        if (!empty($request->getInput()['image']['name'])) {
            $fileValidation->validate();
        }
        
        if ($formValidation->fails() || $fileValidation->fails()) {
            return $this->renderView('write', [
                'errors' => array_merge(
                    $formValidation->getErrors(),
                    $fileValidation->getErrors()
                )
            ]);
        }

        $task = new Task($this->db);

        $task->create($this->user->getId(), $formData);
        $this->redirect('/tasks');
    }
}