<?php

namespace WBD5204;

abstract class Model {

    protected ?Database $Database = NULL;

    public function __construct() {
        $this->Database = new Database();
    }
}