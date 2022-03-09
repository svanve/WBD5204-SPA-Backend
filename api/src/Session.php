<?php

namespace WBD5204;

class Session {
    
    public static function start() : void {
        session_start();
    }

    public static function exists($key) : bool {
        return isset($_SESSION[$key]);
    }

    public static function get($key) : string|null {
        
        if (self::exists($key)) {
            return $_SESSION[$key];
        }

        return null;
    }

    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public static function delete($key) {
        unset($_SESSION[$key]);
    }
    
}