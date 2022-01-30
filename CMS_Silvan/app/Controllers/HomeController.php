<?php 

namespace App\Controllers;

use App\Request;
use App\Interfaces\BaseController;

class HomeController extends BaseController {
    public function index(Request $request) {
        $this->renderView('home');
    }
}

?>