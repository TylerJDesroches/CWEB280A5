<?php
use DB3\DB3;
use DB3\Filter;

session_start();

spl_autoload_register(function ($class) {
    require_once '..\\..\\classes\\' .$class . '.php';
});

// Open the database to query for the images
$db = new DB3('../../db/imageranker.db');
// Define all tables in case this is the first run
$image = new Image();
$db->exec($image->tableDefinition());
$member = new Member();
$db->exec($member->tableDefinition());


// Get the images ordered by views... Would love to use sql to only select the top 5, but our DB3 isn't designed for this.
$orders = array('views'=>'DESC');
$filters = array(new Filter('approved', true)); // only images that are approved
$topImages = $db->selectSomeOrder(new Image(), $orders, $filters);

// Reduce the top images to just 5
$topImages = array_slice($topImages, 0, 5);

// Get all the images ordered by date
$orders = array('id'=>'DESC');
$allImages = $db->selectSomeOrder(new Image(), $orders, $filters);

// Get all the members out of the database
$allMembers = $db->selectAll(new Member());

// Close the database, real quick like sanic
$db->close();
$db = null;

// Get the current logged in member if there is one (otherwise it will just default to the empty member created earlier.
if (isset($_SESSION['member']))
{
	$member = get_object_vars($_SESSION['member']);
}

// Create an array where the key is the id of the member
$keys = array();
foreach ($allMembers as $currentMember)
{
	$keys[] = $currentMember->memberId;
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
        function updateCaption(imagePath, imageId) {
            $.ajax('../json/updatecaption.php',
                {
                    "data": {
                        "caption": $(event.target).val(),
                        "path": imagePath
                    },
                    "method": "POST",
                    "success": function(data) {
                        if(data != 'success')
                        {
                            $('#' + imageId).html(data)
                            
                        }
                        else
                        {
                            $('#' + imageId).html(null)
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
    <ul class="topImages">
    <?php
    // Loop through all the top images and put them on the page
    foreach ($topImages as $currentImage)
    { ?>
    	<li><a href="details.php?id=<?= $currentImage->id ?>"><img src="<?= htmlentities($currentImage->path) ?>" /></a></li>
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
    	<li class="allImages">
            <div>
                <img class="profile" src="<?= htmlentities($currentMember->profileImgPath) ?>" />
                <?= htmlentities($currentMember->alias) ?>
            </div>

            <div>
                <a href="details.php?id=<?= htmlentities($currentImage->id) ?>"><img src="<?= htmlentities($currentImage->path) ?>" /></a>
            </div>         

                        <?php
        
            if(isset($_SESSION['member']) && $member['memberId'] === $currentImage->memId)
            {
                // Give them an update input instead of just text
                ?>
                <div class="error" id="<?= $currentImage->id ?>"></div>
                <div><input type="text" value="<?= htmlentities($currentImage->caption) ?>" 
                            onblur="updateCaption('<?= htmlentities($currentImage->path) ?>', <?= $currentImage->id ?>);" /></div>
            <?php
            }
            else
            { ?>
            <div><?= htmlentities($currentImage->caption) ?></div>
            <?php
            }
            ?>  

            <hr />
        </li>
    <?php
    } ?>
    </ul>
</body>
</html>
