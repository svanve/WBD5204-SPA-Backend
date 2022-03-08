<?php

namespace WBD5204;

use Firebase\JWT\JWT;

abstract class Authorize {

    const ALGO      = JWT_ALGO;
    const SECRET    = JWT_SECRET;
    const ISS       = JWT_ISS;
    const AUD       = JWT_AUD;

    public static function createToken() : array {
        return (array) JWT::encode(
            self::createData(),
            self::SECRET,
            self::ALGO
        );
    }

    private static function createData() : array {
        return [
            'iss'   =>  self::ISS,
            'aud'   =>  self::AUD,
            'iat'   =>  self::createCurrentTimestamp(),
            'nbf'   =>  self::createCurrentTimestamp(),
            'exp'   =>  self::createExpirationTimestamp()
        ];
    }
    
    private static function createCurrentTimestamp() : int {

        return ( new \DateTimeImmutable() )->getTimestamp();
    }

    private static function createExpirationTimestamp() : int {

        return ( new \DateTimeImmutable() )->modify( '+5 minutes' )->getTimestamp();
    }
}