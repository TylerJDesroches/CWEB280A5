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

//will be used to see if the user is a registered member
$isMember = false;

//If the current user is not signed in, they will be redirected to the login page
if(isset($_SESSION['member']))
{
    $member = get_object_vars($_SESSION['member']);
    $isMember = true;
}

//use this to verify that the logged in user is the original uploader
$isOrigUploader = false;

//Get the member variables

$db = new DB3('../../db/imageranker.db');

if(isset($_GET['id']) && $db->selectSome(new Image(), array(new Filter('id', $_GET['id']))) != false)
{

    $image = $db->selectSome(new Image(), array(new Filter('id', $_GET['id'])))[0];
    //Increment the view count
    $image->views += 1;
    $db->update($image);

    //define comment table
    $comment = new Comment();
    $db->exec($comment->tableDefinition());

    $origMember = $db->selectSome(new Member(), array(new Filter('memberId', $image->memId)))[0];

    $db->close();
    $db = null;
    $imageId = new Input('imageId', 'hidden', $image->id, 'imageId');
}
else //if the id isn't set or the image doesn't exist
{
    header('Location: index.php');
}

$newComment = new Input('newComment', 'text', null, 'newComment');
//If the logged in user is the original uploader
if($member['memberId'] === $image->memId)
{
    //Create an input for the caption
    $inputCaption = new Input("imageCaption", 'text', $image->caption, 'caption', 'onblur="updateCaption();"');
    //Create a hidden input for image path for knockout to use to get all comments for the image
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
                comments: ko.observableArray(),
                getComments: function() {
                    var thisClass = $(this);

                    $.getJSON('../json/commentjson.php', {'imageId': document.getElementById('imageId').value},
                    function (jsonComments) {
                        if(jsonComments != "No comments")
                        {
                        
                            observableComments = Array();
                            for(i = 0; i < jsonComments.length; i++)
                            {
                                observableComments.push(new observableComment(jsonComments[i]));
                            }
                            viewModel.comments(observableComments);
                        }
                    })

                },
                postComment: function (comment, event) {
                    alert(comment[0]);
                        $.ajax('../json/commentjsondecode',
                            {
                                'data': {
                                    "comment": document.getElementById('newComment').value,
                                    "memberId": 

                                }
                                'method': 'POST',
                                'success': function(data) {
                                    if(data === 'success')
                                    {
                                        viewModel.getComments();
                                    }
                                }
                            });
                    
                }
            };

            function observableComment(jsonObj)
            {
                //this is defining an object constructor
                //Take in a json object with the same property names as the observable comment object
                this.memberId = ko.observable(jsonObj.memberId);
                this.ranking = ko.observable(jsonObj.ranking);
                this.description = ko.observable(jsonObj.description);

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

            $(function () {
                viewModel.getComments();
                ko.applyBindings(viewModel);
            });
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
            <img src="<?=$origMember->profileImgPath?>" width="50px" height="50px" />
            <p>
                User: <?=$origMember->alias?>
            </p>
        </div>
        <div>
        </div>
        <div>
            <p id="captionError"></p>
            <?php $imageId->render(); ?>
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
        <h2>Comments</h2>
        <?php if(isset($_SESSION['member'])) {?>
        <label>Post a new comment</label>
        <?php $newComment->render(); ?>
        <button data-bind="click: viewModel.postComment" >Post</button>
        <?php } ?>
        <table data-bind="sort: { list: comments, alwaysBy: 'ranking'}">
            <thead>
                <tr>
                    <th>Member ID</th>
                    <th>Ranking</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody data-bind="foreach: comments">
                <tr>
                    <td data-bind="text: memberId"></td>
                    <td data-bind="text: ranking"></td>
                    <td data-bind="text: description"></td>
                </tr>
            </tbody>
        </table>
    </body>
</html>