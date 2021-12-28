<?php

namespace WBD5204\Controller;

use WBD5204\Controller as AbstractController;
use WBD5204\Model\User as UserModel;

final class User extends AbstractController {

    public function __construct() {
        echo 'this is the ma\'phuckin\' UserController!';
        $this->UserModel = new UserModel();
    }

    // @POST
    public function login() : void {
        /** @var array $errors */
        $errors = [];

        if($this->UserModel->login($errors)) {
            $this->responseCode( 200 );
            $this->printJSON( ['success' => true] );
        } 
        else {
            $this->responseCode( 400 );
            $this->printJSON( ['errors' => $errors] );
        }
    }

    // @POST
    public function register() : void {
        /** @var array $errors */
        $errors = [];

        if($this->UserModel->register($errors)) {
            $this->responseCode( 200 );
            $this->printJSON( ['success' => true] );
        }
        else {
            $this->responseCode( 400 );
            $this->printJSON( ['errors' => $errors] );
        }
    }
}