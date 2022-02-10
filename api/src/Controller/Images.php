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

        if ( $this->isMethod( self::METHOD_POST ) && $this->ImagesModel->uploadImage( $errors, $result ) ) {
            $this->responseCode(200);
            $this->printJSON( [ 'success' => true, 'result' => $result ]);
        } else {
            $this->responseCode(400);
            $this->printJSON( [ 'errors' => $errors ] );
        }
    }

}   