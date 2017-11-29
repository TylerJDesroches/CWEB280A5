<?php
// Make sure to start the session!
session_start();

use DB3\DB3; // instead of include or require we  can use the keyword 'use' to include classes in a namespace.
use DB3\Filter;
use HTMLForm\Input;

// tells php to look for the classes in a certain folder structure defined with an anonymous function aka lamda function;
spl_autoload_register(function ($class) {
    require_once '..\\classes\\' .$class . '.php';
});

// Holds whether or not the user posted the form
$isPosted = $_SERVER['REQUEST_METHOD'] === 'POST';

// Stores if the user is authenticated
$isAuthenticated = false;

// Create a new member login
$member = new LoginMember();
// If the form is posted, store the information from the form 
if ($isPosted)
{
	$member->aliasEmail = $_POST['alias'];
    $member->password = $_POST['password'];
}

// Try to validate the entered data
$isValidPost = $isPosted && $member->validate();
// If it is a valid submission, open the database and check to see if a member exists that matches
if ($isValidPost)
{
	// open db
    $db = new DB3('../db/imageranker.db');
    //see if the member exists based off of the email OR alias
    $filters = array(new Filter('email', $member->aliasEmail), new Filter('alias', $member->aliasEmail));
    $logins = $db->selectSome(new Member(), $filters, false); // Use the OR operator
    // Close the database
    $db->close();
    $db = null;
}


// Create the form inputs
// Email / Alias




?>