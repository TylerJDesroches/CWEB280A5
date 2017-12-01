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

//Check to see if the user tried to upload nothing
$isEmptyUpload = !empty($_FILES['imageUpload']['error']);

//If the page has been posted
$isPosted = $_SERVER['REQUEST_METHOD'] === 'POST';

//This will be used to declare that the user has uploaded a valid image file
$isValidPost = false;

//Create a string that will hold error messages to output to user
$errorMessage = '';

//set the logged in member to a variable to make it easier to call
//$member = get_object_vars($_SESSION['member']);


//If the user has submitted a valid image
if($isPosted && !$isEmptyUpload && isset($_FILES['imageUpload']))
{
    $imageVars = $_FILES['imageUpload'];
    //Create an Image object
    $uploadedFile = new Image($imageVars['name'], $imageVars['tmp_name'],$imageVars['size'],$imageVars['type'], 1);
    //Check to see if the uploaded image is valid
    $isValidPost = $uploadedFile->validate();


    if($isValidPost)
    {
        $db = new DB3('../../db/imageranker.db');
        $db->exec($uploadedFile->tableDefinition());
        //Assign a unique id to the name
        $uniqid = uniqid();
        //Move the image to a temp folder while the user assigns a caption
        $shortName = substr($imageVars['name'],strpos($imageVars['name'],"."));
        if(move_uploaded_file($uploadedFile->path, '../img/' .$uniqid.$shortName))
        {
            $uploadedFile->path = '../img/' .$uniqid.$shortName;
        }
        //insert the image data into the database
        $db->insert($uploadedFile);
        $db->close();
        $db = null;
    }
    else
    {
        //Display error messages
        $errorMessage .= $uploadedFile->getError('size');
        $errorMessage .= $uploadedFile->getError('type');
        $errorMessage .= $uploadedFile->getError('path');
    }
}

//When the user selects "Post to gallery"
if($isPosted && isset($_POST['imagePath']))
{
    $db = new DB3('../../db/imageranker.db');
    //get the image that is being approved
    $validUpload = $db->selectSome(new Image(), array(new Filter('path',$_POST['imagePath'])))[0];
    //Set the approved value to true
    $validUpload->approved = true;
    $validUpload->caption = $_POST['imageCaption'];

}

$inputFile = new Input("imageUpload", "file");
if(isset($uploadedFile))
{
    $inputImagePath = new Input("imagePath", "hidden", $uploadedFile->path);

    $inputCaption = new Input("imageCaption", 'text');
}
//TODO: REMOVE THIS ONCE MEMBER IS IMPLEMENTED!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
$memIDSet = new Input("memID", "hidden", 1);

//If the user pressed "Upload" without selecting a file
if($isEmptyUpload)
{
    $errorMessage .= <<<EOT
    <p>Must select a file to upload<p>

EOT;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>File Upload</title>
</head>
<body>
    <h1>File Upload</h1>
    <?php if(!$isPosted || !$isValidPost) { ?>
    <?=$errorMessage?>
    <form action="#" method="post" enctype="multipart/form-data">
        <fieldset>
            <div>
                <?php $inputFile->render(); ?>
            </div>
            <div>
                <input type="submit" value="Upload" />
                <?php $memIDSet->render(); ?>
            </div>
        </fieldset>
    </form>
    <?php } else { ?>
    <form action="#" method="post" enctype="multipart/form-data">
        <fieldset>
            <div>
                <label>Image Caption</label>
                <?php  $inputCaption->render(); ?>
            </div>
            <div>
                <img src="<?=$uploadedFile->path?>" alt="Uploaded Image" />
            </div>
            <div>
                <input type="submit" value="Post to Gallery" />
                <?php $inputImagePath->render(); ?>
            </div>
        </fieldset>
        
    </form>
            
          <?php }?>
</body>
</html>