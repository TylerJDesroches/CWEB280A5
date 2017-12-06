<?php
use DB3\DB3;
use DB3\Filter;

session_start();

spl_autoload_register(function ($class) {
    require_once '..\\..\\classes\\' .$class . '.php';
});

// Open the database to query for the images
$db = new DB3('../../db/imageranker.db');
// Get the images ordered by views... Would love to use sql to only select the top 5, but our DB3 isn't designed for this.
$orders = array('views'=>'DESC');
$filters = array(new Filter('approved', true)); // only images that are approved
$topImages = $db->selectSomeOrder(new Image(), $orders, $filters);

// Reduce the top images to just 5
$topImages = array_slice($topImages, 0, 4);

// Get all the images ordered by date
$orders = array('id'=>'DESC');
$allImages = $db->selectSomeOrder(new Image(), $orders, $filters);

// Get all the members out of the database
$allMembers = $db->selectAll(new Member());

// Close the database, real quick like sanic
$db->close();
$db = null;

// Create an array where the key is the id of the member
$keys = array();
foreach ($allMembers as $member)
{
	$keys[] = $member->memberId;
}
$allMembers = array_combine($keys, $allMembers);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Image Ranker</title>
    <!--Include jquery-->
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script type="text/javascript">
        function updateCaption() {

            $.ajax('../json/updatecaption.php',
                {
                    "data": {
                        "caption": document.getElementById("caption").value,
                        "path": document.getElementById("imagePath").value
                    },
                    "method": "POST"
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

    <h1>Trending</h1>
    <ul>
    <?php
    // Loop through all the top images and put them on the page
    foreach ($topImages as $currentImage)
    { ?>
    	<li><a href="details.php?id=<?= $currentImage->id ?>"><img src="<?= $currentImage->path ?>" /></a></li>
    <?php
    } ?>
    </ul>

    <h1>All Images</h1>
    <ul>
    <?php
    // Loop through all the normal images and output their information
    foreach ($allImages as $currentImage)
    { 
        // Get the current member associated with the posted image
        $currentMember = $allMembers[$currentImage->memId];
        ?>
    	<li>
            <div><a href="details.php?id=<?= $currentImage->id ?>"><img src="<?= $currentImage->path ?>" /></a></div>
            <div>Caption: <?= $currentImage->caption ?></div>
            <div>Alias: <?= $currentMember->alias ?></div>
            <div><img class="profile" src="<?= $currentMember->profileImgPath ?>" /></div>
        </li>
    <?php
    } ?>
    </ul>
</body>
</html>
