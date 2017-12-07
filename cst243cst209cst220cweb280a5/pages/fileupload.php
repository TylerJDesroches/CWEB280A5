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

//Check to see if the user tried to upload nothing
$isEmptyUpload = !empty($_FILES['imageUpload']['error']);

//If the page has been posted
$isPosted = $_SERVER['REQUEST_METHOD'] === 'POST';

//This will be used to declare that the user has uploaded a valid image file
$isValidPost = false;

//Create a string that will hold error messages to output to user
$errorMessage = '';

//If the user has uploaded an image, then selected delete
if(isset($_POST['delete']))
{
    //Delete the image from the img folder
    unlink($_POST['imagePath']);
    $db = new DB3('../../db/imageranker.db');
    //Get the Image object from the database
    $toDelete = $db->selectSome(new Image(), array(new Filter('path',$_POST['imagePath'])))[0];
    //Delete the record of the image
    $db->delete($toDelete);
    $db->close();
    $db = null;
}

//set the logged in member to a variable to make it easier to call
//$member = get_object_vars($_SESSION['member']);


//If the user has submitted a valid image
if($isPosted && !$isEmptyUpload && isset($_FILES['imageUpload']))
{
    $imageVars = $_FILES['imageUpload'];
    //Create an Image object
    $uploadedFile = new Image($imageVars['name'], $imageVars['tmp_name'],$imageVars['size'],$imageVars['type'], $member['memberId']);
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
    $errorMessage .=<<<EOT

<div>
    $uploadedFile->getError('size');
</div>

EOT;
    $errorMessage .=<<<EOT

<div>
    $uploadedFile->getError('type');
</div>

EOT;
    $errorMessage .=<<<EOT

<div>
    $uploadedFile->getError('path');
</div>

EOT;
    }
}

//When the user selects "Post to gallery"
if($isPosted && isset($_POST['imagePath']) && !isset($_POST['delete']))
{
    $db = new DB3('../../db/imageranker.db');
    //get the image that is being approved
    $validUpload = $db->selectSome(new Image(), array(new Filter('path',$_POST['imagePath'])))[0];
    //Set the approved value to true
    $validUpload->approved = true;
    $db->update($validUpload);
    $db->close();
    $db = null;
}

$inputFile = new Input("imageUpload", "file");

//If a file has been selected to upload
if(isset($uploadedFile))
{
    $inputImagePath = new Input("imagePath", "hidden", $uploadedFile->path);
    $deleteButton = new Input("delete", "submit", 'Delete');
    $inputCaption = new Input("imageCaption", 'text', $uploadedFile->caption, 'caption', 'onblur="updateCaption();"');

    
    
}



//If the user pressed "Upload" without selecting a file
if($isEmptyUpload)
{
    $errorMessage .= <<<EOT
<div>
    <p>Must select a file to upload<p>
</div>
EOT;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>File Upload</title>
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script type="text/javascript">
        function updateCaption() {

            $.ajax('../json/updatecaption.php',
                {
                    "data": {
                        "caption": document.getElementById("caption").value,
                        "path": document.getElementById("imagePath").value,
                    },
                    "method": "POST",
                    "success": function(data) {
                        
                        if(data != 'success')
                        {
                            document.getElementById('captionError').innerHTML=data;
                        }
                        else
                        {
                            document.getElementById('captionError').innerHTML=null;
                        }
                    }
                });
        }
    </script>
    <link href="../style/pagestyling.css" rel="stylesheet" />
</head>
<body>
    <nav>
        <a href="index.php">Gallery</a>
        <a href="fileupload.php">Upload</a>
        <a href="memberregister.php">Register</a>
        <a href="memberlogin.php">Login</a>
    </nav>
    <h1>File Upload</h1>
    <div>
        <p id="captionError"></p>
    </div>
    <?=$errorMessage?>
    <?php if(!$isPosted || !$isValidPost) { ?>
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
                <?php //Check to see if the signed in user is the one who uploaded the file
                if($member['memberId'] == $uploadedFile->memId){ $inputCaption->render(); }?>
            </div>
            <div>
                <img src="<?=$uploadedFile->path?>" alt="Uploaded Image" />
            </div>
            <div>
                <input type="submit" value="Post to Gallery" />
                
                <?php $inputImagePath->render();
                      $deleteButton->render();?>
            </div>
        </fieldset>
     </form> 
          <?php }?>
</body>
</html>