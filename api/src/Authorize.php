<?php

namespace WBD5204;

use Firebase\JWT\JWT;

abstract class Authorize {

    const ALGO      = JWT_ALGO;
    const SECRET    = JWT_SECRET;
    const ISS       = JWT_ISS;
    const AUD       = JWT_AUD;

    private static array $matches = [];

    public static function createToken( int $user_id ) : array {
        return (array) JWT::encode(
            self::createData( strval($user_id) ),
            self::SECRET,
            self::ALGO
        );
    }

    public static function authorizeToken( array &$errors, ?array &$result = [] ) : bool {
        if ( self::checkIfTokenExists() === FALSE ) {
            $errors['jwt'][] = 'Keine Authorization im HTTP Header.';
            return FALSE;
        } 
        if ( self::checkIfTokenMatched() === FALSE ) {
            $errors['jwt'][] = 'Der HTTP Header enthält kein Token.';
            return FALSE;
        }


        try {
            /** @var array $token */
            $token = self::getToken();

            if (
                $token[ 'iss' ] !== self::ISS
                || $token[ 'nbf' ] > self::createCurrentTimestamp()
                || $token[ 'exp' ] < self::createCurrentTimestamp()
            ) {
                $errors[ 'jwt' ][] = 'Token invalide oder abgelaufen.';
                return FALSE;
            }

            /** @var array $result */
            $result['user_id'] = $token[ 'sub' ];

            return TRUE;
        } catch ( \Exception $exception ) {

            $errors[ 'jwt' ][] = $exception->getMessage();
            return FALSE;
        }
    }

    private static function checkIfTokenExists() : bool {

        return @preg_match( '/AUTHORIZE\s(\S+)/', $_SERVER[ 'HTTP_AUTHORIZATION' ], self::$matches);
    }

    private static function checkIfTokenMatched() : bool {
        
        return isset( self::$matches[ 1 ] );
    }

    private static function createCurrentTimestamp() : int {
        
        return ( new \DateTimeImmutable() )->getTimestamp();
    }
    
    private static function createData( string $user_id ) : array {
        return [
            'iss'   =>  self::ISS,
            'aud'   =>  self::AUD,
            'sub'   =>  $user_id,
            'iat'   =>  self::createCurrentTimestamp(),
            'nbf'   =>  self::createCurrentTimestamp(),
            'exp'   =>  self::createExpirationTimestamp()
        ];
    }
    
    private static function createExpirationTimestamp() : int {
        
        return ( new \DateTimeImmutable() )->modify( '+5 minutes' )->getTimestamp();
    }
    
    private static function getToken() : array {
        JWT::$leeway = 30; // Toleranz für Zeitspanne zwischen Anfrage des Users und Autorisierung unsererseits (Server)

        return (array) JWT::decode( self::$matches[1], self::SECRET, [ self::ALGO ] );
    }

}