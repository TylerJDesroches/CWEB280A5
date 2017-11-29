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
    $db = new DB3('../db/imageranker.db');
    $db->
    //see if the member exists based off of the email OR alias
    $filters = array(new Filter('email', $loginMember->aliasEmail), new Filter('alias', $loginMember->aliasEmail));
    $logins = $db->selectSome(new Member(), $filters, false); // Use the OR operator
    // Close the database
    $db->close();
    $db = null;

    // See if there is a single member that matches, and that their password matches
    $isAuthenticated = count($logins) === 1 && password_verify($loginMember->password, $logins[0]->password);
}

// If the user isn't authenticated, generate the labels to show on screen
if (!$isAuthenticated)
{
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
    $_SESSION['member'] = $logins[0];
    // Redirect
    header('Location: index.php');
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login User</title>
    <style>
        form .error {
            color: red;
            display: block;
        }

        form label {
            display: block;
        }
    </style>
</head>
<body>
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
        Credentials not found <?= htmlentities($loginMember->email) ?>.
    </div>
    <?php } ?>
</body>
</html>