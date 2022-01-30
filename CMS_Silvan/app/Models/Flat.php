<?php

namespace App\Models;

use Exception;
use App\Models\User;
use App\Models\Str;
use App\Models\Database;
use App\Models\Task;

class Flat {
    private $userId;
    private $flatId;
    private $flatName;
    private $allFlatTasks = [];
    private $allFlatMembers = [];
    private $db;
    private $user;

    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->user = new User($this->db);
    }

    public function create(int $userid, array $data)
    {
        $this->db->query(
            'INSERT INTO flats(name) VALUES (:name)',
            [ ':name' => $data['name'] ]
        );

        $flatidQuery = $this->db->query(
            'SELECT id FROM flats WHERE name = :name',
            [ ':name' => $data['name'] ]
        );
        $flatid = $flatidQuery->first()['id'];

        $this->db->query(
            'INSERT INTO flats_members(flat_id, user_id) VALUES (:flatid, :userid)',
            [ ':flatid'  => $flatid, ':userid' => $userid ]
        );
    }

    public function getFlatId()
    {
        $this->userId = $this->user->getId();

        $getFlatIdQuery = $this->db->query(
            'SELECT flat_id FROM flats_members WHERE user_id = :user_id',
            [ ':user_id' => $this->userId ]
        );

        $this->flatId = $getFlatIdQuery->first()['flat_id'];

        return $this->flatId;
    }

    public function getName()
    {
        $this->getFlatId();

        $getNameQuery = $this->db->query(
            'SELECT name FROM flats WHERE id = :flat_id',
            [ ':flat_id' => $this->flatId ]
        );

        $this->flatName = $getNameQuery->first()['name'];

        return $this->flatName;
    }

    public function getAllFlatTasks() 
    {
        $this->flatId = $this->getFlatId();
        $this->userId = $this->user->getId();

        //get flat members in id
        $allMembersQuery = $this->db->query(
            'SELECT user_id FROM flats_members WHERE flat_id = :flat_id',
            [ ':flat_id' => $this->flatId ]
        );
        $allFlatMembersId = $allMembersQuery->results();
        
        $task = new Task($this->db);
        foreach($allFlatMembersId as $i => $member) {
            $allTasks[$i] = $task->getUserTasks($member['user_id'], 1);
            foreach($allTasks[$i] as $singletask) {
                array_push($this->allFlatTasks, $singletask);
            }
        }
        
        $task = new Task($this->db);
        $allFlatMembersAlias = $this->user->getAliasById($allFlatMembersId);
        $this->allFlatMembers = $allFlatMembersAlias;

        
        $resultArr = array($this->allFlatTasks, $this->allFlatMembers);

        return $resultArr;
    }

    public function addMate(int $idToAdd, string $alias) {
        $flat = new Flat($this->db);
        $flatId = $flat->getFlatId();

        if ($this->user->exists($alias)) {
            if (!$this->user->isFlatMember($idToAdd)) {
                $this->db->query(
                    'INSERT INTO flats_members(flat_id, user_id) VALUES (:flatid, :userid)',
                    [ ':flatid' => $flatId, ':userid' => $idToAdd ]
                );
            }
        }
    }
}