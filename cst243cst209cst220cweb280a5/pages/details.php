<?php
session_start();
use DB3\DB3; // instead of include or require we  can use the keyword 'use' to include classes in a namespace.
use HTMLForm\Input;
use DB3\Filter;
//use DB3\Type;
//require_once "../DB3/Type.php";


// tells php to look for the classes in a certain folder structure defined with an anonymous function aka lamda function;
spl_autoload_register(function ($class) {
    require_once '..\\..\\classes\\' .$class . '.php';
});

//If the current user is not signed in, they will be redirected to the login page
if(!isset($_SESSION['member']))
{
    header('Location: memberlogin.php');
}

//Get the member variables
$member = get_object_vars($_SESSION['member']);
$db = new DB3('../../db/imageranker.db');

if(isset($_POST['id']) && $db->selectSome(new Image(), array(new Filter('id', $_POST['id']))) != false)
{

    $image = $db->selectSome(new Image(), array(new Filter('id', $_POST['id'])));

}
else //if the id isn't set or the image doesn't exist
{
    header('Location: index.php');
}

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Image Detail</title>
        <script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/knockout/3.4.2/knockout-min.js"></script>
        <script type="text/javascript"></script>
    </head>
    <body>
        <h1>Image Detail</h1>
    </body>
</html>