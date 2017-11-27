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
    public $email;
    public $password;
    public $alias;
    public $profileImgPath;

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