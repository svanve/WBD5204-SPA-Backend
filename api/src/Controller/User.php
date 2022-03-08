<?php

namespace WBD5204\Controller;

use WBD5204\Controller as AbstractController;
use WBD5204\Model\User as UserModel;
use WBD5204\Model\Images as ImagesModel;
use WBD5204\Authorize;

final class User extends AbstractController {

    public function __construct() {
        $this->user = new UserModel();
    }

    // @POST
    public function login() : void {
        /** @var array $errors */
        $errors = [];
        /** @var array $result */
        $result = [];

        if( $this->isMethod(self::METHOD_POST) && $this->user->login($errors, $result) ) {
            
            $this->responseCode( 200 );
            $this->printJSON( ['success' => true, 'jwt' => Authorize::createToken( $result['user_id'] ) ] );
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
        /** @var array $result */
        $result = [];

        $image = new ImagesModel();

        if($this->isMethod(self::METHOD_POST) && $image->uploadImage($errors, $result) && $this->user->register($errors, $result)) {
            $this->responseCode( 201 );
            $this->printJSON( ['success' => true, 'jwt' => Authorize::createToken() ] );
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
            $this->printJSON( ['success' => $success, 'jwt' => Authorize::createToken() ] ); // hier auch token?
        }
        else {
            $this->responseCode( 400 );
            $this->printJSON( ['errors' => $errors ] );
        }
    }
}