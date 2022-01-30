<?php

namespace App\Controllers;

use App\Interfaces\BaseController;
use App\Request;

class NotFoundController extends BaseController {
    public function index(Request $request) {
        echo 'Die von Ihnen angefragte Seite exisitiert nicht.';
    }
}