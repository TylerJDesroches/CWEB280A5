<?php
// Make sure to start the session!
session_start();

use DB3\DB3; // instead of include or require we  can use the keyword 'use' to include classes in a namespace.
use DB3\Filter;
use HTMLForm\Input;

// tells php to look for the classes in a certain folder structure defined with an anonymous function aka lamda function;
spl_autoload_register(function ($class) {
    require_once '..\\..\\classes\\' .$class . '.php';
});

// Holds whether or not the user posted the form
$isPosted = $_SERVER['REQUEST_METHOD'] === 'POST';

// Stores if the user is authenticated
$isAuthenticated = false;

// A variable to hold the validated member
$validatedMember = new Member();

// Create a new member login
$loginMember = new LoginMember();
// If the form is posted, store the information from the form
if ($isPosted)
{
	$loginMember->aliasEmail = $_POST['aliasEmail'];
    $loginMember->password = $_POST['password'];
}

// Try to validate the entered data
$isValidPost = $isPosted && $loginMember->validate();
// If it is a valid submission, open the database and check to see if a member exists that matches
if ($isValidPost)
{
	// open db
    $db = new DB3('../../db/imageranker.db');

    // Make sure the member table exists!
    $member = new Member();
    $db->exec($member->tableDefinition());

    //see if the member exists based off of the email OR alias
    $filters = array(new Filter('email', $loginMember->aliasEmail), new Filter('alias', $loginMember->aliasEmail));
    $logins = $db->selectSome(new Member(), $filters, false); // Use the OR operator
    // Close the database
    $db->close();
    $db = null;

    // Keeps track of what login we are on
    $count = 0;

    // loop through all members that may have been passed back, and see which one matches.
    while (!$isAuthenticated && $count < count($logins))
    {
        // Check each member in logins
        $isAuthenticated = password_verify($loginMember->password, $logins[$count]->password);

        // If we found an authenticated member, then set that member to be the validatedMember
        if ($isAuthenticated)
        {
            $validatedMember = $logins[$count];
        }

        // increment count
        $count++;
    }
}

// If the user isn't authenticated, generate the labels to show on screen
if (!$isAuthenticated)
{
    // If the form was valid, it means they were not found in the database. Clear the inputs
    if ($isValidPost)
    {
    	$loginMember = new LoginMember();
        // also reset isposted so errors don't appear
        $isPosted = false;
    }

	// Email / alias
    $aliasEmailInput = new Input('aliasEmail', 'text', $loginMember->aliasEmail);
    $aliasEmailInput->addLabel($loginMember->getLabel('aliasEmail'));
    $aliasEmailInput->addError($isPosted && !$loginMember->validate_aliasEmail(), $loginMember->getError('aliasEmail'));

    // Password
    $passwordInput = new Input('password', 'password', $loginMember->password);
    $passwordInput->addLabel($loginMember->getLabel('password'));
    $passwordInput->addError($isPosted && !$loginMember->validate_password(), $loginMember->getError('password'));
}
else // Otherwise, the user is authenticated, so redirect them
{
    // Regenerate the ID - best practice
    session_regenerate_id();
    // Store the member in their session to be accessed elsewhere
    $_SESSION['member'] = $validatedMember;
    // Redirect
    header('Location: index.php');
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login User</title>
    <link href="../style/pagestyling.css" rel="stylesheet" />
</head>
<body>
    <nav>
        <a href="index.php">Gallery</a>
        <a href="fileupload.php">Upload</a>
        <a href="memberregister.php">Register</a>
        <a href="memberlogin.php">Login</a>
    </nav>
    <h1>Member Login</h1>
    <?php if (!$isValidPost || !$isAuthenticated)
          { ?>
    <form action="#" method="post">
        <fieldset>
            <div>
                <?php $aliasEmailInput->render(); ?>
            </div>
            <div>
                <?php $passwordInput->render(); ?>
            </div>
            <div>
                <input type="submit" value="Login" />
            </div>
        </fieldset>
    </form>
    <?php }
          if ($isValidPost) { ?>
    <div>
        Credentials not found
    </div>
    <?php } ?>
</body>
</html>