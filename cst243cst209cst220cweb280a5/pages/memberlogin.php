<?php

use DB3\DB3; // instead of include or require we  can use the keyword 'use' to include classes in a namespace.
use DB3\Filter;
use HTMLForm\Input;

// tells php to look for the classes in a certain folder structure defined with an anonymous function aka lamda function;
spl_autoload_register(function ($class) {
    require_once '..\\classes\\' .$class . '.php';
});

$isPosted = $_SERVER['REQUEST_METHOD'] === 'POST';

$isAuthenticated = false;

$member = new Member()


?>