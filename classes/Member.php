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
    public $memberId;

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
    public $email;
    /**
     * A validator function to ensure that the email field
     * is not empty and follows email format
     */
    public function validate_email()
    {
        return $this->checkProperty('email', !empty($this->memberId));
    }
    public $password;
    /**
     * A validator function for the password field that checks if
     * the password is not empty and 8-16 characters, or
     * the password matches the hashed password
     * @return boolean
     */
    public function validate_password()
    {
        return $this->checkProperty('password',
            !empty($this->password) && strlen($this->password) >=8 && strlen($this->password) <= 16  ||
            password_hash($this->password) == $this->hashedPassword, '%s must be specified');
    }
    private $hashedPassword;
    public $alias;
    /**
     * This is a validator function that checks if the alias is
     * less than 15 characters and isn't empty.
     * @return boolean
     */
    public function validate_alias()
    {
        return $this->checkProperty('alias', !empty( $this->alias ) && strlen($this->alias) <= 15 );
    }
    public $profileImgPath;
    /**
     * A validator function for profileImgPath that checks if the path is not empty
     */
    public function validate_profileImagePath()
    {
        return $this->checkProperty('profileImgPath', !empty($this->checkProperty), '%s must be specified');
    }


    public function __construct()
    {
        // Define the columns for a member
        // Autoincrementing primary key
        $this->defineColumn('memberId', Type::INT, null, false,true,true);
        // password
        $this->defineColumn('password', Type::TXT, 255, false);
        // email
        $this->defineColumn('email', Type::TXT, 200, false,false,false);
        // alias
        $this->defineColumn('alias', Type::TXT, 15, false);
        // Encrypted Password

        // path
        $this->defineColumn('profileImgPath',Type::TXT, null, false);


        // Set labels for each attribute

    }

}