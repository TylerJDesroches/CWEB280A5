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
//if(!isset($_SESSION['member']))
//{
//    header('Location: memberlogin.php');
//}

//If the page has been posted
$isPosted = $_SERVER['REQUEST_METHOD'] === 'POST';

//This will be used to declare that the user has uploaded a valid image file
$isValid = false;

//set the logged in member to a variable to make it easier to call
$member = get_object_vars($_SESSION['member']);

//Get the file that the user has uploaded
if(isset($_FILES['imageUpload']))
{
    $imageVars = $_FILES['imageUpload'];
    $uploadedFile = new Image($imageVars['name'], $imageVars['tmp_name'],$imageVars['size'],$imageVars['type'], 1);
}

//Check to see if the user has set a caption, or opted not to set one.
if(isset($_POST['imageCaption']))
{
    $db = new DB3('../../db/imageranker.db');
    $image = $db->selectSome(new Image(), array(new Filter('path', $_POST['imagePath'])));
    //move the file to the img folder
    if(move_uploaded_file($uploadedFile->path, '../img/' .$uploadedFile->name))
    {
        $uploadedFile->path = '../img/' .$uploadedFile->name;
    }


    ////create db as needed and insert new record.
    //
    //$db->exec($uploadedFile->tableDefinition()); //create table as needed for the files


    //$db->close();
    //$db=null;
}

//If the user has submitted a valid image
if($isPosted && isset($_FILES['imageUpload']) && $uploadedFile->validate())
{
    //Assign a unique id to the name
    $uniqid = uniqid();
    //Move the image to a temp folder while the user assigns a caption
    if(move_uploaded_file($uploadedFile->path, '../tempimg/' .$uniqid.$uploadedFile->name))
    {
        $uploadedFile->path = '../tempimg/' .$uniqid.$uploadedFile->name;
    }

}

$inputFile = new Input("imageUpload", "file");
$inputCaption = new Input("imageCaption", 'text');
$inputPath = new Input("imagePath", "hidden", $uploadedFile->path);

?>

<!DOCTYPE html>
<html>
<head>
    <title>File Upload</title>
</head>
<body>
    <h1>File Upload</h1>
    <?php if(!$isPosted && !$isValid) { ?>
    <form action="#" method="post" enctype="multipart/form-data">
        <fieldset>
            <div>
                <?php $inputFile->render(); ?>
            </div>
            <div>
                <input type="submit" value="Upload" />
            </div>
        </fieldset>
    </form>
    <?php } else { ?>
    <form action="#" method="post" enctype="multipart/form-data">
        <fieldset>
            <div>
                <label>Image Caption</label>
                <?php $inputCaption->render(); ?>
            </div>
            <div>
                <img src="<?=$uploadedFile->path?>" alt="Uploaded Image" />
            </div>
            <div>
                <input type="submit" value="Post to Gallery" />
                <?php $inputPath->render(); ?>
            </div>
        </fieldset>
        
    </form>
            
          <?php }?>
</body>
</html>