<?php
use DB3\DB3;
use DB3\Filter;
// Make it do JSON
header('Content-type:application/json');

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

// Close the database, real quick like sanic
$db->close();
$db = null;

// Send the datas back
echo json_encode(array('topImages'=>$topImages, 'allImages'=>$allImages));