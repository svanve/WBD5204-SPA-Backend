<?php

namespace App;

use App\Config;
use App\Models\Session;
use App\Models\Sanitization;

class View {
    public function render($view, array $data = [])
    {
        $session = Session::class; //Warum wird hier nur der Klassenname abgebildet?
        $root = Config::get('root');

        $data = Sanitization::sanitize($data);
        extract($data);

        require_once("../app/Views/partials/header.php");
        require_once("../app/Views/{$view}.php");
        require_once("../app/Views/partials/footer.php");

        exit();
    }
}