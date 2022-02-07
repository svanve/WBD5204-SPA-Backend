<?php

namespace WBD5204\Controller;

use WBD5204\Controller as AbstractController;
use WBD5204\Model\User as UserModel;

final class User extends AbstractController {

    public function __construct() {
        $this->user = new UserModel();
    }

    // @POST
    public function login() : void {
        /** @var array $errors */
        $errors = [];

        if( $this->isMethod(self::METHOD_POST) && $this->user->login($errors) ) {
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

        if($this->isMethod(self::METHOD_POST) && $this->user->register($errors)) {
            $this->responseCode( 201 );
            $this->printJSON( ['success' => true] );
        }
        else {
            $this->responseCode( 400 );
            $this->printJSON( ['errors' => $errors] );
        }
    }

    // @PUT
    public function logout() : void {
        /** @var array $errors */
        $errors = [];
        /** @var array $success */
        $success = [];

        if($this->isMethod(self::METHOD_PUT) && $this->user->logout( $errors, $success )) {
            $this->responseCode( 200 );
            $this->printJSON( ['success' => $success ] );
        }
        else {
            $this->responseCode( 400 );
            $this->printJSON( ['errors' => $errors ] );
        }
    }
}