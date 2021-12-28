<?php

namespace WBD5204;

abstract class Session {
    
    public static function start() : void {
        session_start();
    }
}