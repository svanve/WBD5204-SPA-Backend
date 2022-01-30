<?php

namespace App\Models;

use Exception;
use App\Models\User;
use App\Models\Str;
use App\Models\Database;

class Task {
    private $db;
    private $id;
    private $title;
    private $slug;
    private $body;
    private $created;
    private $author_id;
    private $image;
    private $tasks_steps;
    private $aliasResults;
    private $errors = [ ];
    private $user;
    

    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->user = new User($this->db);
    }

    public function find($identifier)
    {
        $field = is_numeric($identifier) ? 'id' : 'slug';
        $taskQuery = $this->db->query("SELECT * FROM tasks WHERE {$field} = ?", [ $identifier ]);

        if ($taskQuery->count()) {
            $taskData = $taskQuery->first();

            foreach ($taskData as $field => $value) {
                $this->{$field} = $value;
            }

            return true;
        }

        return false;
    }

    public function getUserTasks(int $id, int $state_id)
    {
        $taskQuery = $this->db->query(
            'SELECT t.id, t.title, t.body, t.deadline, t.author_id, ti.image, u.alias ' . 
            'FROM tasks AS t ' .
            'LEFT JOIN tasks_images AS ti ON t.id = ti.task_id ' .
            'LEFT JOIN users AS u ON u.id = t.author_id ' .
            'WHERE t.author_id = :id AND t.state_id = :state_id ' .
            'ORDER BY t.deadline ASC', 
            [ ':id' => $id, ':state_id' => $state_id ]
        );
        $tasks = $taskQuery->results();

        foreach( $tasks as $i => $task ) {
            $taskStepsQuery = $this->db->query( 
                "SELECT body FROM tasks_steps WHERE task_id = :task_id",
                [ ":task_id" => $task[ "id" ] ]
            );

            $taskSteps = $taskStepsQuery->results();
            $tasks[ $i ][ "steps" ] = $taskSteps;

            $assignedUsersQuery = $this->db->query(
                'SELECT user_id FROM assigned_users WHERE task_id = :task_id',
                [ ':task_id' => $task['id'] ]
            );

            $taskAssignedUsers = $assignedUsersQuery->results();

            $aliasUsers = [];

            foreach($taskAssignedUsers as $ii => $user) {
                $aliasQuery = $this->db->query(
                    'SELECT alias FROM users WHERE id = :userid',
                    [ ':userid' => $user['user_id'] ]
                );

                $result = $aliasQuery->first()['alias'];
                $aliasUsers[ $ii ][ "alias" ] = $result;
                         
            }

            $tasks[ $i ][ "assigned" ] = $aliasUsers;
        }

        return $tasks;
    }

    public function getTaskDetails(int $user_id, int $task_id)
    {
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

        return $task;
    }
    
    public function create(int $userId, array $formData)
    {
        extract($formData);

        $slug = Str::slug($title);
        $taskData = [
            'title' => $title,
            'slug' => $slug,
            'body' => $body,
            'author_id' => $userId,
            'deadline' => $deadline 
        ];

        $this->db->table('tasks')->store($taskData);

        $taskIdQuery = $this->db->query(
            'SELECT id FROM tasks WHERE slug = :slug',
            [ ':slug' => $slug ]
        );

        $taskid = $taskIdQuery->first()['id'];

        //step
        $taskSteps = [
            'tasks_step' => $tasks_step,
            'tasks_step2' => $tasks_step2,
            'tasks_step3' => $tasks_step3,
            'tasks_step4' => $tasks_step4,
            'tasks_step5' => $tasks_step5,
            'tasks_step6' => $tasks_step6
        ];

        foreach($taskSteps as $step) {
            if(!empty($step)) {
                $this->db->query(
                    'INSERT INTO tasks_steps (task_id, body) VALUES (:task_id, :body)',
                    [ ':task_id' => $taskid, ':body' => $step ]
                );
            }
        }

        $usersToAssign = $this->explodeAssigned($assigned);

        $usersToAssign = array_filter(
            $usersToAssign,
            function ($u) {
                return $this->user->exists($u);
            }
        );

        foreach($usersToAssign as $alias) {
            $searchUserQuery = $this->db->query(
                'SELECT id FROM users WHERE alias = :alias',
                [ ':alias' => $alias ]
            );

            $assignedId = $searchUserQuery->first()['id'];

            var_dump($taskid);
            $insertQuery = $this->db->query(
                'INSERT INTO assigned_users (task_id, user_id) VALUES (:task_id, :user_id)', 
                [ ':task_id' => $taskid, ':user_id' => $assignedId ]
            );  
        }

        //image
        if (!isset($image)) return;

        $this->find($slug);
        $fileStorage = new FileStorage($image);
    
        try{
            $fileStorage->saveIn('images');
            $imageReference = $fileStorage->getGeneratedName();

            $this->db->table('tasks_images')->store([
                'task_id' => $this->id,
                'image' => $imageReference
            ]);
        } catch (Exception $e) {
            $this->errors['image_upload'] = $e->getMessage();
        }
    }

    public function getValueAttr(string $task_id)
    {
        $mainTaskQuery = $this->db->query(
            'SELECT t.title, t.body, t.deadline, ti.image ' .
            'FROM tasks AS t ' .
            'LEFT JOIN tasks_images AS ti ON ti.task_id = t.id ' . 
            'WHERE t.id = :task_id',
            [ ':task_id' => $task_id ]
        );

        $inputValues['main_data'] = $mainTaskQuery->first();

        $stepsBodyQuery = $this->db->query(
            'SELECT body FROM tasks_steps AS ts WHERE ts.task_id = :task_id',
            [ ':task_id' => $task_id ]
        );

        $stepsBodies = $stepsBodyQuery->results();
        
        foreach($stepsBodies as $i => $body) {
            $inputValues['tasks_steps'][$i]= $body;
        }

        $assignedUsersQuery = $this->db->query(
            'SELECT au.user_id FROM assigned_users AS au ' .
            'LEFT JOIN tasks AS t ON t.id = au.task_id ' . 
            'WHERE t.id = :task_id',
            [ ':task_id' => $task_id ]
        );

        $userResults = $assignedUsersQuery->results();

        $aliasResults = $this->user->getAliasById($userResults);

        foreach($aliasResults as $i => $alias) {
            $inputValues['assigned_users'][$i]['alias'] = $alias;
        }

        return $inputValues;
    }

    public function update(string $taskid, array $formData) 
    {
        extract($formData);

        $slug = Str::slug($title);
    
        $this->db->query(
            'UPDATE tasks SET title = :title, slug = :slug, body = :body, deadline = :deadline ' .
            'WHERE id = :task_id',
            [ ':title' => $title, ':slug' => $slug, ':body' => $body, ':deadline' => $deadline, ':task_id' => $taskid ]
        );

        if(isset($image)){
            $this->db->query(
                'UPDATE tasks_images SET image = :ti_image WHERE task_id = :task_id',
                [ ':ti_image' => $image, ':task_id' => $taskid ]
            );
        }

        $taskSteps = [
            'tasks_step' => $tasks_step1,
            'tasks_step2' => $tasks_step2,
            'tasks_step3' => $tasks_step3,
            'tasks_step4' => $tasks_step4,
            'tasks_step5' => $tasks_step5,
            'tasks_step6' => $tasks_step6
        ];

        $taskSteps = array_map( function($a) {
            return trim($a);
        }, $taskSteps);

        $taskSteps = array_values($taskSteps);

        // delete old steps
        $this->db->query(
            'DELETE FROM tasks_steps WHERE task_id = :task_id',
            [ ':task_id' => $taskid ]
        );

        // insert new steps
        foreach ($taskSteps as $step) {
            if (!empty($step)) {
                $this->db->query(
                    'INSERT INTO tasks_steps(task_id, body) VALUES (:task_id, :body)',
                    [ ':task_id' => $taskid, ':body' => $step ]
                );
            }
        }
    
        // delete old assigned_users
        $this->db->query(
            'DELETE FROM assigned_users WHERE task_id = :task_id',
            [ ':task_id' => $taskid ]
        );

        // insert new assigned_users
        $usersToAssign = $this->explodeAssigned($assigned);

        $assignedIds = $this->user->getIdByAlias($usersToAssign);
        
        foreach ($assignedIds as $id) {
            try {
                $insertQuery = $this->db->query(
                    'INSERT INTO assigned_users(user_id, task_id) VALUES (:userid, :taskid)',
                    [ ':userid' => $id, ':taskid' => $taskid ]
                );    

                if(!$insertQuery->count()) {
                    throw new Exception('Nicht alle eingegebenen Usernamen sind teil deiner WG.');
                }
            } catch (Exception $e) {
                $this->$errors['assigned'] = $e->getMessage();
            }
        }
    }
    
    public function explodeAssigned($toAssign) 
    {        
        $usersToAssign = rtrim($toAssign, ',');
        $usersToAssign = explode(',', $usersToAssign);

        $usersToAssign = array_map(
            function($u) {
                return trim($u);
            }, 
            $usersToAssign
        );

        return $usersToAssign;
    }

    public function getErrors() 
    {
        return $this->errors;
    }

    public function getId() 
    {
        return (int) $this->id;
    }

    public function getTitle() 
    {
        return $this->title;
    }

    public function getSlug() 
    {
        return $this->slug;
    }

    public function getBody() 
    {
        return $this->body;
    }

    public function getCreated() 
    {
        return date('D, d.m.y H:i:s', $this->created);
    }

    public function getAuthor() 
    {
        $this->user->find($this->author_id);
        return $user;
    }

    public function getImage() 
    {
        return $this->image;
    }

    public function getArray() 
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'slug' => $this->getSlug(),
            'body' => $this->getBody(),
            'image' => $this->getImage()
        ];
    }
}