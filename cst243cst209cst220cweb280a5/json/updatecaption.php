<?php
session_start();
use DB3\DB3;
use DB3\Filter;

spl_autoload_register(function ($class) {
    require_once '..\\..\\classes\\' .$class . '.php';
});
if(isset($_SESSION['member']))
{
    $member = get_object_vars($_SESSION['member']);
}

$isPosted = $_SERVER['REQUEST_METHOD'] === 'POST';
//validate that there is a caption and image path
if($isPosted && isset($_POST['caption']) && isset($_POST['path']))
{
    $db = new DB3('../../db/imageranker.db');
    //get the image the corresponds to the image path
    $image = $db->selectSome(new Image(), array(new Filter('path', $_POST['path'])))[0];
    if(isset($_SESSION['member']) && $image->memId == $member['memberId'])//if member is logged in and member is posting as themselves
    {
        //update caption
        $image->caption = $_POST['caption'];
        if($image->validate_caption())
        {
            echo $db->update($image)? 'success' : 'failed to update caption';
        }
        else
        {
            echo $image->getError('caption');//display error
        }
    }
    else
    {
        echo 'Not authorized';//display if not authorized
    }


    $db->close();
    $db = null;
}
else
{
    echo "missing or unexpected data";
}






?>