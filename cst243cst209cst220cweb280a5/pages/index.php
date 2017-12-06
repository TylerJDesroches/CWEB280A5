<<<<<<< HEAD
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
// Get all the images ordered by date
$orders = array('id'=>'DESC');
$allImages = $db->selectSomeOrder(new Image(), $orders, $filters);

// Close the database, real quick like sanic
$db->close();
$db = null;


?>

<!DOCTYPE html>
<html>
<head>
    <title>Image Ranker</title>
</head>
<body>
    <h1>Trending</h1>
    <ul>
        <?php
        // Only loop for the first 5 images
        for ($i = 0; $i < 5 && $i < count($topImages); $i++)
        {?>
        	<li><img src="<?= htmlentities($topImages[$i]->path) ?>" /></li>
        <?php
        }
        ?>
    </ul>

    <h1>All Images</h1>
    <ul>

    </ul>
</body>
=======
<?php

?>

<!DOCTYPE html>
<html>
<head>
    <title>index</title>
</head>
<body>
    <h1>index</h1>
</body>
>>>>>>> master
</html>