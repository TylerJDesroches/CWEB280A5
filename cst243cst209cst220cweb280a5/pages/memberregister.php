<?php

session_start();

use DB3\DB3; // instead of include or require we  can use the keyword 'use' to include classes in a namespace.
use DB3\Filter;
use HTMLForm\Input;

// tells php to look for the classes in a certain folder structure defined with an anonymous function aka lamda function;
spl_autoload_register(function ($class) {
    require_once '..\\..\\classes\\' .$class . '.php';
});
// If the form was posted
$isPosted = $_SERVER['REQUEST_METHOD'] === 'POST';
// If the member is registered
$isRegistered = false;
// If the member is valid
$isValidMember = false;

// Create a new member variable
$member = new Member();

// If the form is posted, populate the member fields
if ($isPosted)
{
    var_dump($_FILES);
    // Create a new member by hashing the password, getting the file type and file size. This is due to validation, but not storing some things in the database.
    $member = new Member($_FILES['profileImg']['type'], $_FILES['profileImg']['size']);
	$member->email = $_POST['email'];
    $member->password = $_POST['password'];
    $member->alias = $_POST['alias'];
    // Get / produce the image path
    $member->profileImgPath = '..\\img\\profileimg\\' . uniqid() . $_FILES['profileImg']['name'];

    // Try to validate the new member
    $isValidMember = $member->validate();

    // If it is a valid member (if nothing posted remember this will not be the case)
    if ($isValidMember)
    {
        // Put the new member in the database
        $db = new DB3('../../db/imageranker.db');
        // Create the member table if needed
        $db->exec($member->tableDefinition());
        // Check if the email address already exists
        $existingLogins = $db->selectSome($member, array(new Filter('email', $member->email), new Filter('alias', $member->alias)), false); // Use OR

        // Time to hash the password
        $member->setHashedPassword(password_hash($member->password, PASSWORD_DEFAULT));

        // Make sure there is no existing members, and then insert this member into the database
        $isRegistered = count($existingLogins) === 0 && $db->insert($member);

        // Now that they are registered, redirect them to the login page
        if ($isRegistered)
        {
            header('Location: memberlogin.php');
        }
    }
}

// Create form fields
// Email
$emailInput = new Input('email', 'email', $member->email);
$emailInput->addLabel($member->getLabel('email'));
$emailInput->addError($isPosted && !$member->validate_email(), $member->getError('email'));

// Password
$passwordInput = new Input('password', 'password', $member->password);
$passwordInput->addLabel($member->getLabel('password'));
$passwordInput->addError($isPosted && !$member->validate_password(), $member->getError('password'));

// Profile image
$profileInput = new Input('profileImg', 'file');
$profileInput->addLabel('Profile Image');
$profileInput->addError($isPosted && !$member->validate_profileImgPath(), $member->getError('profileImgPath'));
$profileInput->addError($isPosted && !$member->validate_profileImgSize(), $member->getError('profileImgSize'));
$profileInput->addError($isPosted && !$member->validate_profileImgType(), $member->getError('profileImgType'));

$aliasInput = new Input('alias', 'text', $member->alias);
$aliasInput->addLabel($member->getLabel('alias'));
$aliasInput->addError($isPosted && !$member->validate_alias(), $member->getError('alias'));

?>

<!DOCTYPE html>
<html>
<head>
    <title>Member Register</title>
</head>
<body>
    <h1>Member Resiter</h1>
    <?php if (!$isValidMember)
          { ?>
    <form action="#" method="post" enctype="multipart/form-data">
        <fieldset>
            <div>
                <?php $emailInput->render(); ?>
            </div>
            <div>
                <?php $passwordInput->render(); ?>
            </div>
            <div>
                <?php $profileInput->render(); ?>
            </div>
            <div>
                <?php $aliasInput->render(); ?>
            </div>
            <div>
                <input type="submit" value="Register" />
            </div>
        </fieldset>
    </form>
    <?php } else if (!$isRegistered) { ?>
    <div>
        Email address or alias is already in use.
    </div>
    <?php } ?>
</body>
</html>