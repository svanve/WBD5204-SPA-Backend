<?php

namespace WBD5204;

abstract class Controller {
    protected function responseCode( int $status = 200 ) : void {
        http_response_code($status);
    }

    protected function printJSON( array $output ) : void {
        echo json_encode($output, JSON_PRETTY_PRINT);
    }
}