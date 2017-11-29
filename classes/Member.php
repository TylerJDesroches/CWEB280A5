<?php
use DB3\Model;

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
     * A validator function for the password field that checks
     * if the password is either empty or not more than 100 characters.
     * If the password is empty then the password has already been added to the database.
     * If the password is not empty then this is the member has just created their password
     * @return boolean
     */
    public function validate_password()
    {
        return $this->checkProperty('password');
    }
    public $alias;
    public function validate_alias()
    {

    }
    public $profileImgPath;
    public function validate_profileImagePath()
    {

    }

    /**
     * Creates a member with all it's fields
     * @param mixed $memberId the id of member
     * @param mixed $email email address
     * @param mixed $password hashed password
     * @param mixed $alias user alias
     * @param mixed $profileImgPath path to the user's profile image.
     */
    public function __construct($memberId, $email, $password, $alias, $profileImgPath)
    {
        $this->memberId = $memberId;
        $this->email = $email;
        $this->password = $password;
        $this->alias = $alias;
        $this->profileImgPath = $profileImgPath;
    }

}