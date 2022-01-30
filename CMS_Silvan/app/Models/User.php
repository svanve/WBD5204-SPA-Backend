<?php

namespace App\Models;

use Exception;
use App\Models\Session;

class User {
    private $db;
    private $id;
    private $email;
    private $alias;
    private $first_name;
    private $last_name;
    private $password;
    private $role_id;
    private $role;

    public function __construct(Database $db) 
    {
        $this->db = $db;
    }

    public function find($identifier) 
    {
        $field = is_numeric($identifier) ? 'id' : NULL;
        $field = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'alias';


        $userQuery = $this->db->table('users')->where($field, '=', $identifier);

        if($userQuery->count()) {
            $userData = $userQuery->first();

            foreach($userData as $field => $value) {
                $this->{$field} = $value;
            }

            return true;
        }

        return false;
    }

    public function register($alias, $email, $password) 
    {
        $userData = [
            'alias' => $alias,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ];

        $this->db->table('users')->store($userData);
    }

    public function login($email, $password)
    {
        if(!$this->find($email)) {
            throw new Exception('Die Email oder das Passwort ist nicht korrekt.');
        } 

        if(!password_verify($password, $this->password)) {
            throw new Exception('Die Email oder das Passwort ist nicht korrekt.');
        }

        Session::set('userId', $this->id);
    }

    public function isLoggedIn()
    {
        return Session::exists('userId');
    }

    public function logout()
    {
        Session::delete('userId');
    }

    public function getId()
    {
        return $this->id ?? Session::get('userId');
    }

    public function getFullName()
    {
        return $this->firstName . ' ' . $this->last_name;
    }

    public function setAdmin(int $userid)
    {
        $this->db->query(
            'UPDATE users SET role_id = :roleid WHERE id = :userid ',
            [ ':roleid' => 2, ':userid' => $userid ]
        );
    }

    public function isAdmin(int $userid)
    {
        $adminQuery = $this->db->query(
            'SELECT role_id FROM users WHERE id = :userid',
            [ ':userid' => $userid ]
        );

        $adminResult = $adminQuery->first()['role_id'];
        
        if($adminResult == 2) {
            return true;
        } else {
            return false;
        }
    }

    public function getIdByAlias(array $userAliases)
    {
        $idResults;
        foreach($userAliases as $i => $alias ) {
            $idQuery = $this->db->query(
                'SELECT id FROM users ' .
                'WHERE alias = :alias',
                [ ':alias' => $alias ]
            );

            $idResults[$i] = $idQuery->results()[0]['id'];
        }
        
        return $idResults; 
    }

    public function getAliasById(array $userIds)
    {        
        foreach($userIds as $i => $userId ) {
            $aliasQuery = $this->db->query(
                'SELECT alias FROM users ' .
                'WHERE id = :user_id',
                [ ':user_id' => $userId['user_id'] ]
            );

            $this->aliasResults[$i] = $aliasQuery->first()['alias'];
        }
        
        return $this->aliasResults; 
    }

    public function exists(string $alias)
    {
        $aliasQuery = $this->db->query(
            'SELECT alias FROM users WHERE alias = :alias',
            [ ':alias' => $alias ]
        );

        if ($aliasQuery->count()) {
            return true;
        } else {
            return false;
        }
    }

    public function isFlatMember(int $userid) {
        $memberQuery = $this->db->query(
            'SELECT user_id FROM flats_members WHERE user_id = :userId',
            [ ':userId' => $userid ]
        );

        if ($memberQuery->count()) {
            return true;
        } else {
            return false;
        }
    }
}