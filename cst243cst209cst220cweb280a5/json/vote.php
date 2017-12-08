<?php
use DB3\DB3;
session_start();

spl_autoload_register(function ($class) {
    require_once '..\\..\\classes\\' .$class . '.php';
});

// Die if the member isn't authenticated
if (!isset($_SESSION['member']))
{
	die("Not authenticated");
}

// Get the stuff from the post
if(!isset($_REQUEST['id']) || !isset($_REQUEST['isUp']))
{
    die("Incorrect data sent");
}
$id = (int)$_REQUEST['id'];
$isUp = $_REQUEST['isUp'];

// Get the comment out of the database
$db = new DB3('../../db/imageranker.db');

$comment = new Comment();
$comment->commentId = $id;
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
