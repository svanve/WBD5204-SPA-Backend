<?php 

namespace App;

class Config {
    private static $options = [
        'root' => '/CMS_Silvan/public',
        'database' => [
            'host' => 'localhost',
            'dbName' => 'FlatShareCMS',
            'username' => 'root',
            'password' => 'root'
        ]
    ];

    public static function get(string $selector) {
        $elements = explode('.', $selector);
        $dataset = self::$options;

        foreach ($elements as $element) {
            $dataset = $dataset[$element];
        }

        return $dataset;
    } 
}