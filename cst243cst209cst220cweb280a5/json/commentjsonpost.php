<?php
session_start();
header('Content-type:application/json');
use DB3\DB3;
use DB3\Filter;

spl_autoload_register(function ($class) {
    require_once '..\\..\\classes\\' .$class . '.php';
});

if(isset($_SESSION['member']))
{
    //Get the currently logged in user
    $member = get_object_vars($_SESSION['member']);
    $db = new DB3('../../db/imageranker.db');
    //Create a new comment
    $comment = new Comment();
    $comment->description = $_POST['comment'];
    $comment->memberId = $member['memberId']; //Use the member's id
    $comment->imageId = (int)$_POST['imageId'];
    $comment->ranking = 0;
    $comment->validate();
    $db->insert($comment);
    $db->close();
    $db = null;
    if($comment->validate())
    {
        echo json_encode('success');
    }
    else 
    {
        echo json_encode($comment->getError('description'));
    }

}
else
{
    echo json_encode('Not authenticated');
}

?>