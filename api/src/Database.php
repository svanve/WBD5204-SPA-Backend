<?php

namespace WBD5204;

final class Database extends \PDO {
    
    public function __construct() {
        /** @var string $dsn */
        $dsn = sprintf(
            'mysql:host=%1$s;port=%2$s;dbname=%3$s;charset=%4$s;',
            DB_HOST, 
            DB_PORT, 
            DB_NAME, 
            DB_CHARSET
        );

        /** @var string $db_user */
        $db_user = DB_USER;
        
        /** @var string $db_pass */
        $db_pass = DB_PASS;
        
        /** @var array $options */
        $options = [
            \PDO::ERRMODE_WARNING => TRUE,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
        ];

        parent::__construct($dsn, $db_user, $db_pass, $options);
    }
}