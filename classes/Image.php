<?php
use DB3\Model;
use DB3\Type;

/**
 * Stores an image that a user uploads (basically like a "post")
 *
 * @version 1.0
 * @author cst243
 */
class Image extends Model
{
    public $id;
    public $path;
    public $memId;
    public $caption;
    public $views;
    public $approved;

    private $name;
    private $size;
    private $type;


    ///**
    // * Validates that the name is not empty.
    // * @return mixed - call to the checkProperty method that sees if an error needs to be added to the errors array.
    // */
    //public function validate_name()
    //{
    //    //Name cannot be empty
    //    return $this->checkProperty('name', !empty(trim($this->name)), '%s is required');
    //}

    /**
     * Validates that the path is not empty and it exists.
     * @return boolean - call to the checkProperty method that sees if an error needs to be added to the errors array.
     */
    public function validate_path()
    {
        return $this->checkProperty('path', !empty(trim($this->path)), '%s is required')
            && $this->checkProperty('path', file_exists(trim($this->path)), 'File not found at location');
    }

    /**
     * Validates that the type is one of the possible options, based on what type of member is logged in.
     * @return boolean - call to the checkProperty method that sees if an error needs to be added to the errors array.
     */
    public function validate_type()
    {
        if($this->type === "")
        {
            $result = true;
        }
        else
        {
            $result = $this->checkProperty('type', strcmp(substr($this->type, 0, strpos($this->type, '/')),'image') === 0, 'Only files with the filetype of "Image" can be uploaded');
        }
        return $result;
    }

    /**
     * Validates that the size is within the limits of what the current logged in member's type is.
     * @return boolean - call to the checkProperty method that sees if an error needs to be added to the errors array.
     */
    public function validate_size()
    {
        //if uploading an image to the gallery, restrict file size to 100 KB
        return $this->checkProperty('size', $this->size < 102400, 'You can only upload files that are less than 100 KB');
        //TODO: Add validation to check if user is uploading image for gallery or for profile image. Limit profile image to 15KB
    }

    public function validate_caption()
    {
            //Validate that caption is not larger that 144 characters
        return $this->checkProperty('caption', strlen($this->caption) <= 144, 'Captions can only be 144 characters or less');

    }

    /**
     * Makes a new image object
     * @param mixed $name name of the image
     * @param mixed $path path to image
     * @param mixed $size size of image
     * @param mixed $type type of image
     * @param mixed $caption caption of image
     * @param mixed $views number times the image has been viewed
     * @param mixed $approved whether the uploader has approved the image to be viewed in the gallery
     */
    public function __construct($name='', $path='', $size=0, $type='', $memId=0, $caption="", $views=0)
    {
        $this->name = $name;
        $this->path = $path;
        $this->size = $size;
        $this->type = $type;
        $this->memId = $memId;
        $this->caption = $caption;
        $this->views = $views;
        $this->approved = false;

        $this->defineColumn('id',Type::INT,null,false,true,true);
        $this->defineColumn('path',Type::VRC, 255, false);
        $this->defineColumn('memId',Type::INT, null, false);
        $this->defineColumn('caption',Type::VRC, 144, true);
        $this->defineColumn('approved',Type::BOL, null, false);
        $this->defineColumn('views',Type::INT,null);

        $this->setLabel('id', 'File ID');
        $this->setLabel('path', 'File Path');
        $this->setLabel('size', 'File Size');
        $this->setLabel('memId', 'Member');
        $this->setLabel('caption', 'Caption');
        $this->setLabel('approved', 'Approved');

    }

}