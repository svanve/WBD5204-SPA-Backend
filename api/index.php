<?php

namespace WBD5204;

/** @var string $autoload_file */
$autoload_file = __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

/** @var string $configuration_file */
$configuration_file = __DIR__ . DIRECTORY_SEPARATOR . 'config.php';

if ( !file_exists( $autoload_file ) ) {
    trigger_error( 
        sprintf(
            'Autoload file (%s) doesn\'t exist!',
            $autoload_file
        ),
        E_USER_ERROR
    );
}

if ( !file_exists( $configuration_file ) ) {
    trigger_error(
        sprintf(
            'Configuration file (%s) doesn\'t exist!',
            $configuration_file
        ),
        E_USER_ERROR
    );
}



require_once( $autoload_file );

require_once( $configuration_file );

( new API() )->run();