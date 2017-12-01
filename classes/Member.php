<?php
use DB3\Model;
use DB3\Type;

/**
 * Stores a member in the system
 *
 * @version 1.0
 * @author cst243
 */
class Member extends Model
{

    // ATTRIBUTES
    public $memberId;
    public $email;
    public $password;
    private $hashedPassword;
    public $alias;
    public $profileImgPath;
    private $profileImgType;
    private $profileImgSize;


    // VALIDATION FUNCTIONS

    /**
     * A validator function to make sure the primary
     * key is either empty (not yet been added to database)
     * or is an integer (has been added to the database)
     * @return boolean
     */
    public function validate_pk()
    {
        return $this->checkProperty('memberId', empty($this->memberId) || is_int($this->memberId), '%s must be a number');
    }
    /**
     * A validator function to ensure that the email field
     * is not empty and follows email format
     */
    public function validate_email()
    {
        return $this->checkProperty('email', !empty($this->memberId));
    }
    /**
     * A validator function that checks if the hashed
     * password is not empty OR less than 255 characters.
     */
    public function validateHashedPassword()
    {
        return $this->checkProperty('hashedPassword',
            !empty($this->hashedPassword) && strlen($this->hashedPassword) <= 255, "%s must be specified and below 256 characters");
    }
    /**
     * A validator function for the password field that checks if
     * the password is not empty and 8-16 characters, or
     * the password matches the hashed password
     * @return boolean
     */
    public function validate_password()
    {
        return $this->checkProperty('password',
            !empty($this->password) && ((strlen($this->password) >=8 && strlen($this->password) <= 16) ||
            $this->password == $this->hashedPassword), '%s must be specified');
    }
    /**
     * This is a validator function that checks if the alias is
     * less than 15 characters and isn't empty.
     * @return boolean
     */
    public function validate_alias()
    {
        return $this->checkProperty('alias', !empty( $this->alias ) && strlen($this->alias) <= 15 );
    }
    /**
     * A validator function for profileImgPath that checks if the path is not empty
     */
    public function validate_profileImgPath()
    {
        return $this->checkProperty('profileImgPath', !empty($this->checkProperty), '%s must be specified');
    }
    /**
     * A validator function for profileImgType that
     * checks if the Type is either empty or is a correct img type
     */
    public function validate_profileImgType()
    {
        return $this->checkProperty('profileImgType',
            empty($this->profileImgType) || $this->profileImgType == 'image/png' || $this->profileImgType == 'image/jpeg' ||
            $this->profileImType == 'image/bmp' || $this->profileImType == 'image/webp', '%s must be a valid img type');
    }
    /**
     * A validator function for profileImgSize that checks
     * if the Image Size is 15kb or lower;
     */
    public function validate_profileImgSize()
    {
        return $this->checkProperty('profileImgSize',
            empty($this->profileImgSize) || $this->profileImgSize <= 15360, '%s must be lower than 15kb');
    }


    // CONSTRUCTOR

    public function __construct($profileImgType='', $profileImgSize='')
    {
        // Set private variables
        $this->profileImgType = $profileImgType;
        $this->profileImgSize = $profileImgSize;

        // Define the columns for a member
        // Autoincrementing primary key
        $this->defineColumn('memberId', Type::INT, null, false,true,true);
        // password
        $this->defineColumn('password', Type::TXT, 255, false);
        // email
        $this->defineColumn('email', Type::VRC, 200, false,false,false);
        // alias
        $this->defineColumn('alias', Type::VRC, 15, false);
        // Encrypted Password
        $this->defineColumn('hashedPassword', Type::VRC, 255,false);
        // path
        $this->defineColumn('profileImgPath',Type::VRC, null, false);

        // Set labels for each attribute
        $this->setLabel('memberId','ID');
        $this->setLabel('email','Email Address');
        $this->setLabel('alias','Alias');
    }

    // GETTERS AND SETTERS

    public function setprofileImgType()
    {
        
    }

    /**
     * Takes in a hashed password and sets the
     * $password and $hashedPassword attributes to what was passed in
     * @param mixed $hashedPassword The value of the hashed password
     */
    private function setHashedPassword($hashedPassword)
    {
        $this->hashedPassword = $hashedPassword;
        $this->password = $hashedPassword;
    }

}