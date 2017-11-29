<?php

/**
 * Stores an image that a user uploads (basically like a "post"
 *
 * @version 1.0
 * @author cst243
 */
class Image
{
    public $imageId;
    public $name;
    public $path;
    public $size; // HEY NOTE WE MAY NOT HAVE TO STORE SIZE OR TYPE
    public $type;
    public $ranking;
    public $caption;
    public $views;

    /**
     * Makes a new image object
     * @param mixed $imageId id of image
     * @param mixed $name file name of image
     * @param mixed $path path to image
     * @param mixed $size size of image
     * @param mixed $type type of image
     * @param mixed $ranking rank of image
     * @param mixed $caption caption of image
     * @param mixed $views number of views image recieves
     */
    /*
    public function __construct($imageId, $name, $path, $size, $type, $ranking, $caption, $views)
    {
        $this->imageId = $imageId;
        $this->name = $name;
        $this->path = $path;
        $this->size = $size;
        $this->type = $type;
        $this->ranking = $ranking;
        $this->caption = $caption;
        $this->views = $views;
    }
    */
}