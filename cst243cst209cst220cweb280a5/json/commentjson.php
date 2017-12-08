<?php
session_start();
header('Content-type:application/json'); //very touchy function. Only wokrs if no other output is on page before this function call.
use DB3\DB3;
use DB3\Filter;

spl_autoload_register(function ($class) {
    require_once '..\\..\\classes\\' .$class . '.php';
});

$db = new DB3('../../db/imageranker.db');
$comments = $db->selectSomeOrder(new Comment(), array('ranking' => 'DESC'), array(new Filter('imageId', $_GET['imageId'])));

// Get all the members out of the database
$allMembers = $db->selectAll(new Member());
$keys = array();
foreach ($allMembers as $currentMember)
{
	$keys[] = $currentMember->memberId;
}
$allMembers = array_combine($keys, $allMembers);

$commentsProfile = array();

foreach($comments as $comment)
{
    $commentsProfile[] = new CommentMember($comment, $allMembers[$comment->memberId]);
}

$db->close();
$db = null;

//Check to make sure array has comments
if(isset($comments[0]))
{
    echo json_encode($commentsProfile);
}
else
{
    echo json_encode("No comments");
}

?>