<?php
session_start();
header('Content-type:application/json'); //very touchy function. Only wokrs if no other output is on page before this function call.
use DB3\DB3;
use DB3\Filter;

spl_autoload_register(function ($class) {
    require_once '..\\..\\classes\\' .$class . '.php';
});

$db = new DB3('../../db/imageranker.db');
$comments = $db->selectSomeOrder(new Comment(), array('ranking'), array(new Filter('imageId', $_GET['imageId'])));
$db->close();
$db = null;

//Check to make sure array has comments
if(isset($comments[0]))
{
    echo json_encode($comments);
}
else
{
    echo json_encode("No comments");
}

?>