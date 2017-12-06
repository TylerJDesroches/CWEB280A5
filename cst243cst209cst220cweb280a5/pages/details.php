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

//use this to verify that the logged in user is the original uploader
$isOrigUploader = false;

//Get the member variables
$member = get_object_vars($_SESSION['member']);
$db = new DB3('../../db/imageranker.db');

if(isset($_GET['id']) && $db->selectSome(new Image(), array(new Filter('id', $_GET['id']))) != false)
{

    $image = $db->selectSome(new Image(), array(new Filter('id', $_GET['id'])))[0];

}
else //if the id isn't set or the image doesn't exist
{
    header('Location: index.php');
}



//If the logged in user is the original uploader
if($member['memberId'] === $image->memId)
{
    //Create an input for the caption
    $inputCaption = new Input("imageCaption", 'text', $image->caption, 'caption', 'onblur="updateCaption();"');
    $imagePath = new Input('imagePath', 'hidden', $image->path, 'imagePath');
    $isOrigUploader = true;
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Image Detail</title>
        <script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/knockout/3.4.2/knockout-min.js"></script>
        <script type="text/javascript">
            var viewModel = {



}



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
    </head>
    <body>
        <nav>
            <a href="index.php">Gallery</a>
            <a href="fileupload.php">Upload</a>
            <a href="memberregister.php">Register</a>
            <a href="memberlogin.php">Login</a>
        </nav>
        <h1>Image Detail</h1>
        <div>
            <p id="captionError"></p>
        </div>
        <div>
            <?php if($isOrigUploader)
                  {$inputCaption->render();
                  $imagePath->render();
                  } else { ?>
            <h2>
                <?=$image->caption?>
            </h2><?php } ?>
        </div>
        <img src="<?=$image->path?>" />
    </body>
</html>