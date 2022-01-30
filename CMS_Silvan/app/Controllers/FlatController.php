<?php

namespace App\Controllers;

use App\Interfaces\BaseController;
use App\Traits\RouteGuards\UserOnly;
use App\Models\FormValidation;
use App\Models\Flat;
use App\Request;
use App\Models\Session;
use App\Models\Task;

class FlatController extends BaseController {
    use UserOnly;

    public function index(Request $request) 
    {   
        $userId = $this->user->getId();
        $flat = new Flat($this->db);
        $formData = $request->getInput();


        if(!$this->user->isFlatMember($userId) && !$request->hasInput())
        {
            $this->renderView('createFlat');
        } 
        elseif (!$this->user->isFlatMember($userId) && $request->hasInput()) 
        {
            $formValidation = new FormValidation($this->db, $formData);
            $formValidation->setRules([
                'name' => 'required|min:5|max:65|available:flats', 
                'flats_members' => 'required|min:5', 
            ]);

            $formValidation->validate();

            if ($formValidation->fails()) {
                return $this->renderView('createFlat', [
                    'errors' => $formValidation->getErrors()
                ]);
            }

            $flat->create($userId, $formData);
            $this->user->setAdmin($userId);
            Session::flash('message', 'Du hast die WG "' . $formData['name'] . '" erfolgreich gegründet.');     
            $this->redirect('/flat');
            return;
        } 
        elseif ($this->user->isFlatMember($userId) && !$request->hasInput()) 
        {
            $name = $flat->getName();
            $flatData = $flat->getAllFlatTasks();
            $isAdmin = $this->user->isAdmin($userId);

            $this->renderView('flat', [ 
                'flatTasks' => $flatData[0],
                'flatMembers' => $flatData[1],
                'name' => $name,
                'isAdmin' => $isAdmin
            ]);
        }
        elseif ($this->user->isFlatMember($userId) && $request->hasInput()) 
        {
            $idToAdd = $this->user->getIdByAlias($formData);

            $flat->addMate($idToAdd['add_mate'], $formData['add_mate']);
            
            $name = $flat->getName();
            $flatData = $flat->getAllFlatTasks();
            $isAdmin = $this->user->isAdmin($userId);

            $this->renderView('flat', [ 
                'flatTasks' => $flatData[0],
                'flatMembers' => $flatData[1],
                'name' => $name,
                'isAdmin' => $isAdmin
            ]);
        }
    }    

    public function detail(Request $request) {
        $user_id = $this->user->getId();
        $task_id = $request->getInput()[0] ?? '0';

        $task = new Task($this->db);
        $details = $task->getTaskDetails($user_id, $task_id);

        $this->renderJson(200, $details);
    }
}