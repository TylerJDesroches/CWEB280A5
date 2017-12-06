<?php
use DB3\DB3;
use DB3\Filter;

spl_autoload_register(function ($class) {
    require_once '..\\..\\classes\\' .$class . '.php';
});

$isPosted = $_SERVER['REQUEST_METHOD'] === 'POST';

if($isPosted && isset($_POST['caption']) && isset($_POST['path']))
{
    $db = new DB3('../../db/imageranker.db');
    $image = $db->selectSome(new Image(), array(new Filter('path', $_POST['path'])))[0];
    $image->caption = $_POST['caption'];
    echo $db->update($image)? $image->id . ':success' : $image->id. ':failure';
    $db->close();
    $db = null;
}
else
{
    echo "missing or unexpected data";
}






?>