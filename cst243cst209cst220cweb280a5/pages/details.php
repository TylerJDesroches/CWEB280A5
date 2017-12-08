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

//define comment table
$comment = new Comment();
$db->exec($comment->tableDefinition());

//Get all the images from the database (for the previous and next page)
$allImages = $db->selectSomeOrder(new Image(), array('views'=>'DESC'), array(new Filter("approved", 1)));
//if the id is set in the GET superglobals
if(isset($_GET['id']))
{
    //get the corresponding image
    $image = $db->selectSome(new Image(), array(new Filter('id', $_GET['id'])))[0];
}

//if the id returned an image and the image is approved
if(isset($_GET['id']) && $image != false && $image->approved )
{
    //Increment the view count
    $image->views += 1;
    $db->update($image);

    //Get the member who originally posted the image
    $origMember = $db->selectSome(new Member(), array(new Filter('memberId', $image->memId)))[0];
    $db->close();
    $db = null;

    //create a hidden input for the image id
    $imageId = new Input('imageId', 'hidden', $image->id, 'imageId');
}
else //if the id isn't set or the image doesn't exist
{
    $db->close();
    $db = null;
    //Redirect the user back to the gallery page
    header('Location: index.php');
}

//Create a new input for signed in users to post a comment
$newComment = new Input('newComment', 'text', null, 'newComment');
//If the logged in user is the original uploader
if($isMember && $member['memberId'] === $image->memId)
{
    //Create an input for the caption
    $inputCaption = new Input("imageCaption", 'text', $image->caption, 'caption', 'onblur="updateCaption();"');
    //Create a hidden input for image path for knockout to use to get all comments for the image
    $imagePath = new Input('imagePath', 'hidden', $image->path, 'imagePath');
    $isOrigUploader = true;
}

$prevUrl = "";
$nextUrl = "";
// Loop through all the images and find the previous and next id's for the links
$done = false;
for ($i = 0; $i < count($allImages) && !$done; $i++)
{
    // Check if we are on the current image
	if ($allImages[$i]->id === $image->id)
    {
    	// take the id previous and next if possible
        if ($i >= 1)
        {
        	$prevUrl = "href='details.php?id={$allImages[$i - 1]->id}'";
        }
        if ($i < count($allImages) - 1)
        {
        	$nextUrl = "href='details.php?id={$allImages[$i + 1]->id}'";
        }
        $done = true;
    }
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
                            for(var i = 0; i < jsonComments.length; i++)
                            {
                                observableComments.push(new observableComment(jsonComments[i]));
                            }
                            viewModel.comments(observableComments);
                        }
                    })

                },
                postComment: function () {
                    $.ajax('../json/commentjsonpost.php',
                        {
                            data: {
                                "comment": document.getElementById('newComment').value,
                                'imageId': document.getElementById('imageId').value
                            },
                            method: 'POST',
                            success: function(data) {
                                if(data === "success")
                                {
                                    viewModel.getComments();
                                    document.getElementById('newComment').value = "";
                                    document.getElementById('errorMessage').innerHTML = "";
                                }
                                else
                                {
                                    document.getElementById('errorMessage').innerHTML = data;
                                }
                            }
                        });

                }
            };

            function observableComment(jsonObj)
            {
                //this is defining an object constructor
                //Take in a json object with the same property names as the observable comment object
                this.commentId = jsonObj.commentId;
                this.ranking = jsonObj.ranking;
                this.description = jsonObj.description;
                this.imagePath = jsonObj.imagePath;
                this.alias = jsonObj.alias;
            }

            function vote(comment, event) {
                var isUp = $(event.target).text() == "Upvote" ? 1 : 0;
                $.ajax('../json/vote.php',
                    {
                        "data": {
                            "id": comment.commentId,
                            "isUp": isUp
                        },
                        "method": "POST",
                        "success": function (data) {
                            if (data == "success") {
                                viewModel.getComments();
                            }
                            else {
                                alert(data);
                            }
                            
                        }
                    });
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
        <link href="../style/pagestyling.css" rel="stylesheet" />
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
            <img class="profile" src="<?=htmlentities($origMember->profileImgPath)?>" />
            <p>
                User: <?= htmlentities($origMember->alias)?>
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
                <?=htmlentities($image->caption)?>
            </h2><?php } ?>
        </div>
        <img src="<?=htmlentities($image->path)?>" />
        <h2>Comments</h2>
        <?php if($isMember) {?>
        <div class="error" id="errorMessage"></div>
        <label>Post a new comment</label>
        <?php $newComment->render(); ?>
        <button data-bind="click: viewModel.postComment" >Post</button>
        <?php } ?>
        <table data-bind="sort: { list: comments}">
            <thead>
                <tr>

                    <th>Profile</th>
                    <th>Alias</th>
                    <th>Points</th>
                    <th>Comment</th>
                    <th>Upvote</th>
                    <th>Downvote</th>
                </tr>
            </thead>
            <tbody data-bind="foreach: comments">
                <tr>
                    <td><img class="profile" data-bind="attr: { src: imagePath }"/></td>
                    <td data-bind="text: alias"></td>
                    <td data-bind="text: ranking"></td>
                    <td data-bind="text: description"></td>
                    <td class="clickable" data-bind="attr: {'data-commentid': commentId}, click: vote">Upvote</td>
                    <td class="clickable" data-bind="attr: {'data-commentid': commentId}, click: vote">Downvote</td>
                </tr>
            </tbody>
        </table>

        <a <?= $prevUrl ?>>Previous</a> | 
        <a <?= $nextUrl ?>>Next</a>

    </body>
</html>