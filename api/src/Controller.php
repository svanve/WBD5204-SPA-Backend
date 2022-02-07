<?php

namespace WBD5204;

abstract class Controller {

    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';

    protected function isMethod( string $method ) : bool {
        return $_SERVER[ 'REQUEST_METHOD' ] === $method;
    }

    protected function responseCode( int $status = 200 ) : void {
        http_response_code($status);
    }

    protected function printJSON( array $output ) : void {
        echo json_encode($output, JSON_PRETTY_PRINT);
    }
}