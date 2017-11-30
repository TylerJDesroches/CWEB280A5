<?php

session_start();

use DB3\DB3; // instead of include or require we  can use the keyword 'use' to include classes in a namespace.
use DB3\Filter;
use HTMLForm\Input;

// tells php to look for the classes in a certain folder structure defined with an anonymous function aka lamda function;
spl_autoload_register(function ($class) {
    require_once '..\\classes\\' .$class . '.php';
});
// If the form was posted
$isPosted = $_SERVER['REQUEST_METHOD'] === 'POST';
// If the member is registered
$isRegistered = false;

// Create a new member
$member = new Member();
// If the form is posted, populate the member fields
if ($isPosted)
{
	$member->email = $_POST['email'];
    $member->password = $_POST['password'];
    $member->alias = $_POST['alias'];
    // Get the image path stuff
}



?>

