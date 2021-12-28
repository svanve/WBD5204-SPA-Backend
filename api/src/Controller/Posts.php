<?php

namespace WBD5204\Controller;

use WBD5204\Controller as AbstractController;

final class Posts extends AbstractController {
    // provide Data in JSON for API requests from Frontend
    function __construct() {
        $data = [
            'prop' => 'hi ich bin die JSON Data aus PostsController'
        ];

        function json($status, $data) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code($status);
            echo json_encode($data); 
        }

        json(200, $data);
    }

    public function index() : void {
        
    }
}