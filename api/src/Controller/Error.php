<?php

namespace WBD5204\Controller;

use WBD5204\Controller as AbstractController;

final class Error extends AbstractController {
    
    public function index( int $status = 404 ) : void {
        $this->responseCode( $status );
        $this->printJSON( ['error' => $status ] );
    }
}