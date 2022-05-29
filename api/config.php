<?php

namespace WBD5204;

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_CHARSET', 'UTF8');
define('DB_NAME', 'wbd5204_vl');
define('DB_USER', 'root');
define('DB_PASS', 'root');

// Image File Directories
define('UPLOADS_DIR', 'uploads');
define('UPLOADS_PATH', __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . UPLOADS_DIR);
define('UPLOADS_URI', 'http://localhost:8888/' . UPLOADS_DIR);

// JWT
define( 'JWT_SECRET',   'L{E2>z]$mqBv]i"<A|j%8C;t%VA2)t');
define( 'JWT_ISS',      'http://localhost:8888');
define( 'JWT_AUD',      'http://localhost:8888');
define( 'JWT_ALGO',     'HS256');
