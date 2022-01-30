<?php

session_start();

require_once("../app/App.php");

$app = new App\App;

// Irgendwo in der Dokumentation ausweisen, was Fremd- und was Eigencode ist. 70% oder so wird Fremdcode sein, das ist aber auch okay dann!
