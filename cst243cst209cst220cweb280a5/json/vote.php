<?php
use DB3\DB3;
session_start();

spl_autoload_register(function ($class) {
    require_once '..\\..\\classes\\' .$class . '.php';
});

// Die if the member isn't authenticated
if (!isset($_POST['member']))
{
	die("Not authenticated");
}

// Get the stuff from the post
if(!isset($_POST['id']) || !isset($_POST['isUp']))
{
    die("Incorrect data sent");
}
$id = (int)$_POST['id'];
$isUp = $_POST['isUp'];

// Get the comment out of the database
$db = new DB3('../../db/imageranker.db');

$comment = new Comment();
$comment->commentId = $id;

// If the comment doesn't exist, go away
if (!$db->exists($comment))
{
	die("Invalid comment id");
}
$db->select($comment);



if ($isUp)
{
	$comment->ranking++;
}
else
{
    $comment->ranking--;
}

$saved = $db->save($comment);

// Close
$db->close();
$db = null;
// Return success
echo $saved ? "success" : "failure";
