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
        return $this->checkProperty('email',!empty(trim($this->email)), '%s is required')
            && $this->checkProperty('email',filter_var($this->email,FILTER_VALIDATE_EMAIL), '%s should be a valid email address');
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
            $this->password == $this->hashedPassword), '%s must be between 8 and 16 characters.');
    }
    /**
     * This is a validator function that checks if the alias is
     * less than 15 characters and isn't empty.
     * @return boolean
     */
    public function validate_alias()
    {
        return $this->checkProperty('alias', !empty( $this->alias ) && strlen($this->alias) <= 15, '%s must be less than 15 characters' );
    }
    /**
     * A validator function for profileImgPath that checks if the path is not empty
     */
    public function validate_profileImgPath()
    {
        return $this->checkProperty('profileImgPath', !empty($this->profileImgPath), '%s must be specified');
    }
    /**
     * A validator function for profileImgType that
     * checks if the Type is either empty or is a correct img type
     */
    public function validate_profileImgType()
    {
        return $this->checkProperty('profileImgType', !empty($this->profileImgType), '%s must be specified' ) &&
            $this->checkProperty('profileImgType',
                $this->profileImgType == 'image/png' || $this->profileImgType == 'image/jpeg' ||
                $this->profileImgType == 'image/bmp' || $this->profileImgType == 'image/webp'
                ,'%s must be a valid img type');
    }
    /**
     * A validator function for profileImgSize that checks
     * if the Image Size is 15kb or lower;
     */
    public function validate_profileImgSize()
    {
        return $this->checkProperty('profileImgSize',
            !empty($this->profileImgSize) && $this->profileImgSize <= 15360, '%s must be lower than 15kb');
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
        // path
        $this->defineColumn('profileImgPath',Type::VRC, null, false);

        // Set labels for each attribute
        $this->setLabel('memberId','ID');
        $this->setLabel('email','Email Address');
        $this->setLabel('alias','Alias');
        $this->setLabel('password','Password');
        $this->setLabel('profileImgPath', 'Profile Image');
        $this->setLabel('profileImgType', 'Profile Image');
        $this->setLabel('profileImgSize', 'Profile Image');
    }

    // FUNCTIONS

    /**
     * Takes in a hashed password and sets the
     * $password and $hashedPassword attributes to what was passed in
     * @param mixed $hashedPassword The value of the hashed password
     */
    public function setHashedPassword($hashedPassword)
    {
        $this->hashedPassword = $hashedPassword;
        $this->password = $hashedPassword;
    }

}