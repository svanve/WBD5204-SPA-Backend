<?php

namespace WBD5204\Controller;

use WBD5204\Controller as AbstractController;
use WBD5204\Model\User as UserModel;

final class User extends AbstractController {

    public function __construct() {
        $this->user = new UserModel();
    }

    //
    public function login() : void {
        /** @var array $errors */
        $errors = [];

        if($this->user->login($errors)) {
            $this->responseCode( 200 );
            $this->printJSON( ['success' => true] );
        } 
        else {
            $this->responseCode( 400 );
            var_dump($errors);
            $this->printJSON( ['errors' => $errors] );
        }
    }

    // @POST
    public function register() : void {
        /** @var array $errors */
        $errors = [];

        if($this->user->register($errors)) {
            $this->responseCode( 200 );
            $this->printJSON( ['success' => true] );
        }
        else {
            $this->responseCode( 400 );
            $this->printJSON( ['errors' => $errors] );
        }
    }

    // @PATCH
    public function logout() : void {
        /** @var array $errors */
        $errors = [];
        /** @var array $success */
        $success = [];

        if($this->user->logout($errors)) {
            $this->responseCode( 200 );
            var_dump($success);
            $this->printJSON( ['success' => $success['logout']] );
        }
        else {
            $this->responseCode( 400 );
            $this->printJSON( ['errors' => $errors['logout']] );
        }
    }
}