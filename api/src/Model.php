<?php

namespace WBD5204;

abstract class Model {

    protected ?Database $Database = NULL;

    public function __construct() {
        var_dump('hey yo im Model class (database)');
        $this->Database = new Database();
    }
}