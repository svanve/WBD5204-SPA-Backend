<?php

namespace WBD5204\Controller;

use WBD5204\Controller as AbstractController;
use WBD5204\Model\Images as ImagesModel;

final class Images extends AbstractController {

    public function __construct() {
        $this->ImagesModel = new ImagesModel();
    }

    // @POST
    public function upload() : void {
        /** @var array $errors */
        $errors = [];
        /** @var array $result */
        $result = [];

        if ( $this->isMethod( self::METHOD_POST ) 
        // && Authorize::authorizeToken( $errors, $result )
        && $this->ImagesModel->uploadImage( $errors, $result ) ) {
            $this->responseCode(200);
            $this->printJSON( [ 'success' => true, 'result' => $result, /* 'jwt' => Authorize::createToken( $result['user_id'] ) */ ]);
        } else {
            $this->responseCode(400);
            $this->printJSON( [ 'errors' => $errors ] );
        }
    }

    // @GET 
    /// Braucht es diese Funktion (oder nur intern?) ///
    public function get( ?int $image_id ) : void {
        /** @var array $error */
        $errors = [];
        /** @var array $result */
        $result = [];

        if ( $this->isMethod( self::METHOD_GET ) 
        // && Authorize::authorizeToken( $errors, $result )
        && $this->ImagesModel->getImagePath( $errors, $result, $image_id ) ) {
            $this->responseCode(200);
            $this->printJSON( [ 'success' => true, 'result' => $result, /* 'jwt' => Authorize::createToken( $result['user_id'] ) */ ] );
        } else {
            $this->responseCode(400);
            $this->printJSON( [ 'errors' => $errors ] );
        }

    }

}   